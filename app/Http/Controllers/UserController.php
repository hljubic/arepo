<?php

namespace App\Http\Controllers;


use App\Models\Group;
use App\Models\School;
use App\Models\User;
use App\Services\CarnetMatica\CarnetMaticaService;
use App\Services\HrEdu\HrEduService;
use App\Services\HrEdu\HrEduUser;
use App\Services\Zimbra\ZimbraService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Zimbra\Admin\AdminFactory;
use function React\Promise\map;

class UserController extends ResourceController
{
    protected static $modelName = 'User';
    protected array $keys = [
        'sn' => 'last_name',
        'givenName' => 'first_name',
        'mail' => 'email',
        'uid' => 'uid',
        'hrEduPersonUniqueNumber' => 'oib',
        'hrEduPersonDateOfBirth' => 'birth_date',
        'hrEduPersonGender' => 'sex',
        'hrEduPersonPrimaryAffiliation' => 'basic_institution_connection',
        'hrEduPersonAffiliation' => 'institution_connection',
        'hrEduPersonExpireDate' => 'basic_connection_expiration_date',
        'hrEduPersonGroupMember' => 'group_affiliation',
        'telephoneNumber' => 'phone_number',
        'homeTelephoneNumber' => 'home_phone_number',
        'mobile' => 'mobile_phone_number',
        'hrEduPersonOIB' => 'oib',
        'ou' => 'organisational_unit',
        'roomNumber' => 'room_number',
        'postalAddress' => 'home_postal_address',
        'postalCode' => 'postal_code',
        'hrEduPersonCommURI' => 'desktop_device',
        'hrEduPersonPrivacy' => 'privacy_label',
        'street' => 'street_house_number',
//    'hrEduPersonHomeOrg'=>'',
        'hrEduPersonTitle' => 'institution_position_id',
//    'hrEduPersonRole'=>'',
//    'hrEduPersonStaffCategory'=>'',
        'hrEduPersonStudentCategory' => 'student_type_id',
        'hrEduPersonAcademicStatus' => 'occupation_id',
//    'hrEduPersonProfessionalStatus'=>'',
        'hrEduPersonScienceArea' => 'science_field_id',
//    'o'=>'',
//    'l'=>''
    ];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('zimbra-authenticate');
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = parent::store($request);
        $result = $user->issueIdentity();
        $createAccountParameters = ZimbraService::createAccountString($result['uid'], env('ZIMBRA_DOMAIN'), $result['password']);
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));

        // Kreiraj mail account sa glavnom (kraćom) domenom - npr.   ime.prezime@sumit.sum.ba / ime.prezime@skole.hr
        $newMailAccount = $api->createAccount(...$createAccountParameters);
        $user->update(['email' => $newMailAccount->account->name]);
        $user->syncAAI();

        if ($user->school_id) {
            $school = School::find($user->school_id);

            if ($request->add_as_admin == 1) {
                $school->admin_id = $user->id;
                $school->save();
            }

            // Kreiraj alias ime.prezime@gundulic.sumit.sum.ba / ime.prezime@gundulic.skole.hr
            $api->addAccountAlias($newMailAccount->account->id, $result['uid'] . '@' . $school->identifier . '.' . env('MAIN_DOMAIN'));
        }

        return $user;
    }

    public function robo($userData)
    {
        $service = new HrEduService();

        $newUser = HrEduUser::create()->setUid('neki.uid');

        $ime = HrEduService::formatName();
        $prezime = HrEduService::formatName('prezime');

        $newUser->setName($ime, $prezime);


        $newUser->setAttribute('key', 'value');

        if ($service->exists('neki.uid')) {
            $service->edit($newUser->asArray(), 'neki.uid');
        } else {
            $service->add($newUser->asArray());
        }

    }

    public function getByOib(string $oib, CarnetMaticaService $carnetMaticaService)
    {
        return $carnetMaticaService->getStudentByOib($oib);
    }

    public function changeStatus(Request $request, User $user)
    {
        $request->validate(['lock' => 'required|bool']);
        $result = $user->changeLockStatus($request->get('lock'));
        if ($result)
            return response()->json(['success' => 'Uspješno ste izmijenili status korisnika']);
        return response()->json(['error' => 'Nešto je pošlo krivo'], 400);
    }

    public function importCSV(Request $request)
    {
        $csvFile = storage_path() . '/app/public/' . $request->file;
        $csv_data = $this->readCSV($csvFile, array('delimiter' => $request->delimiter ?? ','));

        $user_ids = [];
        for ($i = 1; $i < sizeof($csv_data); $i++) {
            $user = User::where('oib', $csv_data[$i][2])->first() ?? null;
            if (!$user) {
                $user = new User();
                $user->first_name = [$csv_data[$i][0]];
                $user->last_name = [$csv_data[$i][1]];
                $user->oib = $csv_data[$i][2];
                $user->email = $csv_data[$i][3];
                $user->role_id = 2;
                $user->school_id = $request->school_id;
                $user->basic_institution_connection_id = 3; // TODO (custom): Ovo nek upiše tamo kad importa. ili to ide od trenutka izdavanja?
                $user->basic_connection_expiration_date = Carbon::now()->addYear()->toDateString();
                $user->save();

                $user_ids[] = $user->id;
            }
        }
        if ($request->group_name) {
            $this->createGroupFromIDs($request, $user_ids);
        }

        return $user_ids;

        /*
         * CSV example
         *
         *
            ime,prezime,oib,email
            hrvoje,ljubic,1231123123,"hrvoje.ljubic@gmail.com"
            anton,martinovic,3131123123,"anton.martinovic@gmail.com"
         */
    }

    public function importXML(Request $request)
    {
        $xml_object = simplexml_load_file(storage_path() . '/app/public/' . $request->file);

        $user_ids = [];
        foreach ($xml_object as $LDAPzapis) {
            $xml_user = [];
            foreach ($LDAPzapis as $attribute) {
                $attrs = (array)$attribute;
                if (array_key_exists($attrs['@attributes']['name'], $xml_user)) {
                    $old = $xml_user[$attrs['@attributes']['name']];
                    unset($xml_user[$attrs['@attributes']['name']]);
                    $xml_user += [$attrs['@attributes']['name'] => array($old, $attrs[0])];
                }
                $xml_user += [$attrs['@attributes']['name'] => $attrs[0]];
            }
            $user = User::where('oib', $xml_user['oib'])->first() ?? null;
            if (!$user) {
                $user = new User();
                $user->first_name = $xml_user['first_name'];
                $user->last_name = $xml_user['last_name'];
                $user->oib = $xml_user['oib'];
                $user->email = $xml_user['email'];
                $user->role_id = 2;
                $user->school_id = $request->school_id;
                $user->basic_institution_connection_id = 3;
                $user->basic_connection_expiration_date = Carbon::now()->addYear()->toDateString();
                $user->save();

                $user_ids[] = $user->id;
            }
        }

        if ($request->group_name) {
            $this->createGroupFromIDs($request, $user_ids);
        }

        return $user_ids;
    }

    public function createGroupFromIDs($request, $user_ids)
    {
        $group = new Group();
        $group->name = $request->group_name;
        $group->school_id = $request->school_id;
        $group->school_year_id = 1;
        $group->department = 0;
        $group->save();
        $group->fresh();

        $group->users()->sync($user_ids);
    }

    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }

}
