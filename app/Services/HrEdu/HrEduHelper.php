<?php
namespace App\Services\HrEdu;

use App\Models\User;

class HrEduHelper {

    public static function createUser(User $user, $password)
    {
        $service = new HrEduService();

        $i = 0;
        do {
            $uid = HrEduService::generateUsername($user, $i);
            $i++;
        } while ($service->exists($uid));

        $newUser = HrEduUser::create()->setUid($uid);
        $newUser->setPassword($password);

        $persistentId = SHA1($uid . date_timestamp_get(date_create()));
        $newUser->setAttributes(
            [
                'hrEduPersonPersistentID' => $persistentId,
                //'hrEduPersonAllowAccess' => "TRUE",
                'hrEduPersonExpireDate'=> "NONE"
            ]
        );

        $user->parseEduUser($newUser);

        $result = $service->add($newUser);

        $service->closeConnection();

        if (!$result)
            throw new \Exception("LDAP error.");

        return $newUser->asArray();
    }

    public static function changePassword($uid, $password) {
        $service = new HrEduService();

        $user = $service->get($uid)->setPassword($password);

        return $service->edit($user->asArray(), $uid);
    }

}
