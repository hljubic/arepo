<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Services\CarnetMatica\CarnetMaticaService;
use App\Services\Zimbra\ZimbraService;
use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Zimbra\Admin\AdminFactory;

class SchoolController extends ResourceController
{
    protected static $modelName = 'School';

    public function __construct()
    {
        parent::__construct();
        $this->middleware('zimbra-authenticate');
    }

    public function store(Request $request)
    {
        /** @var School $school */
        $school = parent::store($request);
        $powerDns = App::make(Powerdns::class);
        try {
            // Kreiranje DNS zapisa
            $dnsRecords = [
                ['name' => $school->identifier, 'type' => RecordType::A, 'content' => env('CMS_SITE_DNS'), 'ttl' => 60],
                ['name' => 'test-'. $school->identifier, 'type' => RecordType::A, 'content' => env('CMS_SITE_DNS'), 'ttl' => 60],
                ['name' => $school->identifier, 'type' => RecordType::MX, 'content' => env('ZIMBRA_MX_SERVER_IP'), 'ttl' => 60]
            ];

            // Na glavnu domenu (sumit.sum.ba / skole.hr) nakači dva DNS zapisa (A i MX)
            $powerDns->zone(env('MAIN_DOMAIN') . '.')->create($dnsRecords);

            $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
            $newMxDomain = $school->identifier . '.' . env('MAIN_DOMAIN');
            // Na zimbru dodaj novu domenu - npr. identifikatorskole.skole.hr
            $api->createDomain($newMxDomain);

            // Kreiranje maila
            $createAccountParameters = ZimbraService::createAccountString($school->identifier, env('MAIN_DOMAIN'), '123456');
            // Kreiraj mail account - npr. gundulic@sumit.sum.ba / gundulic@skole.hr
            $mainMailAccount = $api->createAccount(...$createAccountParameters);
            // Kreiraj alias - npr. ured@gundulic.sumit.sum.ba / ured@gundulic.skole.hr koji pokazuje na gundulic@sumit.sum.ba / gundulic@skole.hr
            $api->addAccountAlias($mainMailAccount->account->id, 'ured@' . $newMxDomain);
            return $school;
        } catch (RequestException $requestException) {
            $error = $requestException->getResponse()->getBody()->getContents();
            return response()->json($error, 400);
        }
    }

    public function getSchoolFromCarnet(Request $request, CarnetMaticaService $carnetMaticaService)
    {
        $validated = $request->validate([
            'Sifra' => 'required|string',
            'Podsifra' => 'required|string'
        ]);
        return $carnetMaticaService->getSchool($validated['Sifra'], $validated['Podsifra']);
    }

    public function changeDNSRecords(Request $request)
    {
        $school = School::find($request->school_id);
        $school->dns = $request->dns;
    }


    public function destroy(Request $request, $id)
    {
        return abort(412, 'Školu nije moguće izbrisati!');
    }
}
