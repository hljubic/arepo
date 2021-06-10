<?php

namespace App\Http\Controllers;

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;
use Exonet\Powerdns\Resources\Zone;
use Illuminate\Http\Request;

class DnsController extends Controller
{
    public function createZone(Request $request, Powerdns $powerdns, Zone $zoneResource)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'kind' => 'required|string|in:Native,Master,Slave',
            'nameservers' => 'required|array'
        ]);
        $zoneResource->setName($validated['name']);
        $zoneResource->setKind($validated['kind']);
        $zoneResource->setNameservers($validated['nameservers']);
        $zone = $powerdns->createZoneFromResource($zoneResource);
        return response()->json(['success' => ['name' => $zone->getCanonicalName()]], 201);
    }

    public function createDnsRecords(Powerdns $powerdns)
    {
        $domain = 'ostaa.sumit.';
        $nameServers = ['sumit.sum.'];
        $dnsRecords = [
//            ['name' => '@', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
            ['name' => 'www', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60],
//            ['name' => 'mail01', 'type' => RecordType::A, 'content' => '127.0.0.1'],
//            ['name' => 'mail02', 'type' => RecordType::A, 'content' => '127.0.0.2'],
//
//            ['name' => '@', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60],
//            ['name' => 'www', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60],
//            ['name' => 'mail01', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::2'],
//            ['name' => 'mail02', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::3'],
//
//            ['name' => '@', 'type' => RecordType::MX, 'content' => [sprintf('10 mail01.%s.', $domain), sprintf('20 mail02.%s.', $domain)]],
//            ['name' => '@', 'type' => RecordType::TXT, 'content' => '"v=spf1 a mx include:_spf.example.com ?all"'],
        ];

        // Create a new zone with the defined records and name servers.
        $k = $powerdns->createZone($domain, $nameServers)->create($dnsRecords);
        // To enable DNSSEC, you can pass 'true' as third argument to 'createZone', or you can enable it on the zone itself:
        $powerdns->zone($domain)->enableDnssec();
    }
}
