<?php

namespace App\Services\Zimbra;

use App\Http\Requests\ZimbraCreateAccountRequest;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Zimbra\Admin\AdminFactory;
use Zimbra\Admin\AdminInterface;
use Zimbra\Admin\Http;
use Zimbra\Struct\KeyValuePair;

class ZimbraService
{
//    public function __construct()
//    {
//        $this->middleware('zimbra-authenticate');
//    }

//    public static function createAccount(ZimbraCreateAccountRequest $zimbraCreateAccountRequest)
//    {
//        $createAccountParameters = static::createAccountString($zimbraCreateAccountRequest->validated());
//        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
//        try {
//            $createdAccount = $api->createAccount(...$createAccountParameters);
//            return response()->json([
//                'success' => 'Uspješno ste kreirali email account',
//                'id' => $createdAccount->account->id,
//                'name' => $createdAccount->account->name,
//            ], 201);
//        } catch (RequestException $requestException) {
//            $error = $requestException->getResponse()->getBody()->getContents();
//            return response()->json($error, 400);
//        }
//    }

    public function createDistributionList(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);
        $attributes = [];
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
        if (isset($validated['description']))
            array_push($attributes, new KeyValuePair('description', $validated['description']));

        $distributionListResponse = $api->createDistributionList($validated['name'], false, $attributes);
        return response()->json([
            'success' => 'Uspješno ste kreirali distribucijsku listu.',
            'id' => $distributionListResponse->dl->id,
            'name' => $distributionListResponse->dl->name,
        ], 201);
    }

    public static function createAccountString(string $username, string $domain, string $password, array $additionalParameters = []): array
    {
        $additionalParameters = array_map(function ($value, $key) {
            return new KeyValuePair ($key, $value);
        }, $additionalParameters);
        $emailName = $username . '@' . $domain;
        return [$emailName, $password, $additionalParameters];
    }
}
