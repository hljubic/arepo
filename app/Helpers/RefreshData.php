<?php


namespace App\Helpers;


use App\Http\Controllers\Controller;
use App\Services\HrEdu\HrEduService;
use Exonet\Powerdns\Powerdns;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Zimbra\Admin\AdminFactory;

class RefreshData extends Controller
{
    public function __construct()
    {
        $this->middleware('zimbra-authenticate')->only(['restartZimbra']);
    }

    /**
     * @throws \Exception
     * Poslati cookie string sa powerdnsa i serijski broj koji se moze pronaci na glavnom dashboardu kao drugi parametar
     */
    public static function restartPowerDns(string $cookiesString = null, string $serial = '2021052017'): string
    {
        $cookiesExploded = explode(';', $cookiesString);
        $cookiesArray = [];
        foreach ($cookiesExploded as $cookie) {
            list($key, $val) = explode('=', $cookie, 2);
            $cookiesArray[$key] = $val;
        }
        $dataJson = json_decode('{
    "serial": "' . $serial . '",
    "record": [
        {
            "record_name": "*.wordpress",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.22",
            "record_comment": ""
        },
        {
            "record_name": "@",
            "record_type": "SOA",
            "record_status": "Active",
            "record_ttl": "3600",
            "record_data": "ns1.sumit.carnet.hr. hostmaster.sumit.carnet.hr. 2021052016 10800 3600 604800 3600",
            "record_comment": ""
        },
        {
            "record_name": "@",
            "record_type": "NS",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "ns1.sumit.carnet.hr.",
            "record_comment": ""
        },
        {
            "record_name": "api-osaa",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.22",
            "record_comment": ""
        },
        {
            "record_name": "osa",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.22",
            "record_comment": ""
        },
        {
            "record_name": "osaa",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.22",
            "record_comment": ""
        },
        {
            "record_name": "cms",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.22",
            "record_comment": ""
        },
        {
            "record_name": "ns1",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.23",
            "record_comment": ""
        },
        {
            "record_name": "powerdns",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.23",
            "record_comment": ""
        },
        {
            "record_name": "webmail",
            "record_type": "MX",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "10 193.198.163.39.",
            "record_comment": ""
        },
        {
            "record_name": "webmail",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.39",
            "record_comment": ""
        },
        {
            "record_name": "wordpress",
            "record_type": "A",
            "record_status": "Active",
            "record_ttl": "60",
            "record_data": "193.198.163.22",
            "record_comment": ""
        }
    ],
    "_csrf_token": "' . $cookiesArray['_csrf_token'] . '"
}', true);
        $response = Http::withHeaders(['Cookie' => $cookiesString])->post(env('POWER_DNS_HOST') . '/domain/sumit.carnet.hr/apply', $dataJson);
        if ($response->successful())
            return $response->body();
        else
            throw new \Exception($response->body());
    }

    public static function restartZimbra()
    {
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
        $api->auth(env('ZIMBRA_ADMIN_NAME'), env('ZIMBRA_ADMIN_PASSWORD'));

        $dbLists = $api->getAllDistributionLists()->dl;
        $allDbListsIds = collect(array_column($dbLists, 'id'));
        foreach ($allDbListsIds as $dbList) {
            $api->deleteDistributionList($dbList);
        }
        // TODO domene pobrisat isto

        $adminAccounts = $api->getAllAdminAccounts();
        $adminId = $adminAccounts->account->id;

        $userAccounts = $api->getAllAccounts();
        $allAcountIds = collect(array_column((array)$userAccounts->account, 'id'));
        $accountsToDelete = $allAcountIds->reject(function ($item) use ($adminId) {
            return $item == $adminId;
        })->values();

        foreach ($accountsToDelete as $account) {
            $api->deleteAccount($account);
        }
        return true;
    }

    public function restartLdap()
    {
        $service = new HrEduService();
        $users = $service->all();
        foreach ($users as $user) {
            $uid = $user->asArray()['uid'];
            if (!in_array($uid, ['superadmin.test', 'admin.test']))
                $service->delete($uid);
        }
        $service->closeConnection();
        return true;
    }
}
