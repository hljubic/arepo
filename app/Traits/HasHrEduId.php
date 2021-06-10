<?php

namespace App\Traits;

use App\Exceptions\EduHrEmailException;
use App\Exceptions\UserExistsException;
use App\Exceptions\UserHasNoIdentity;
use App\Mail\IdentityCredentials;
use App\Services\HrEdu\HrEduHelper;
use App\Services\HrEdu\HrEduService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mail;

trait HasHrEduId
{
    public function initializeHasHrEduId()
    {
        $this->append(['has_identity']);
    }

    public function getHasIdentityAttribute()
    {
        return $this->uid != null;
    }

    public function canGetIdentity()
    {
        $validator = Validator::make($this->toArray(), self::getValidationForIdentity());
        return !$validator->fails();
    }

    public static function getValidationForIdentity()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'oib' => 'required|nullable|string|digits:8'
        ];
    }

    public function issueIdentity()
    {
        if ($this->hasIdentity)
            throw new UserExistsException("User already has identity.", 409);

        //if (!$this->canGetIdentity())
        //    throw new \Exception("Missing required data for identity.", 422);

        $password = mb_strtoupper(Str::random(8));

        try {
            $result = HrEduHelper::createUser($this, $password);
            $uid = $result['uid'];
            $this->uid = $uid;
            $this->save();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

        // Activity ?
        //storeActivity('issueIdentity', $this->id);

        return [
            'uid' => $result['uid'],
            'password' => $password
        ];
    }

    public function syncAAI()
    {
        $service = new HrEduService();

        $uid = $this->uid;

        $updateUser = $service->get($uid);

        $this->parseEduUser($updateUser);
        $service->edit($updateUser->asArray(), $uid);

        return 'Success';
    }

    public function parseEduUser(&$eduUser)
    {
        $ime = HrEduService::formatName($this->first_name[0]);
        $prezime = HrEduService::formatName($this->last_name[0]);

        $eduUser->setName($ime, $prezime);

        $eduUser->setAttributes(
            [
                'hrEduPersonUniqueNumber' => $this->oib,
                'hrEduPersonOIB' => $this->oib,
                'mail' => $this->email,
                //'hrEduPersonGender' => $this->gender
            ]
        );
    }

    public function destroyHrEdu()
    {
        if (!$this->hasIdentity)
            throw new UserHasNoIdentity("User has no identity.", 411);

        $service = new HrEduService();

        $service->delete($this->uid);

        $service->closeConnection();
    }

    public function changePassword($password = null)
    {
        if (!$this->hasIdentity)
            throw new UserHasNoIdentity("User has no identity.", 411);

        if (!$password)
            $password = mb_strtoupper(Str::random(8));

        HrEduHelper::changePassword($this->uid, $password);

        //storeActivity('changePassword', $this->id);

        return [
            'uid' => $this->uid,
            'password' => $password
        ];
    }

    public function emailCredentials($credentials, $email = null)
    {
        if ($email) {
            $this->email_private = $email;
            $this->save();
        }

        if ($this->email_private == null)
            throw new EduHrEmailException("User has no private email entered, please enter email.", 412);

        //storeActivity('emailCredentials', $this->id);

        $data = [
            'credentials' => $credentials,
            'user' => $this
        ];

        Mail::to($this->email_private)->send(new IdentityCredentials($data));
        //Mail::to('podrska@skole.sum.ba')->send(new IdentityCredentials($data));
    }

}
