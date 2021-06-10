<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZimbraCreateAccountRequest;
use App\Models\GroupAlias;
use App\Models\Relations\HasManySyncable;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Zimbra\Admin\AdminFactory;
use Zimbra\Admin\AdminInterface;
use Zimbra\Admin\Http;
use Zimbra\Struct\KeyValuePair;

class ZimbraController extends Controller
{
    public function __construct()
    {
        $this->middleware('zimbra-authenticate');
    }

    public function createAccount(ZimbraCreateAccountRequest $zimbraCreateAccountRequest)
    {
        $createAccountParameters = $this->createAccountString($zimbraCreateAccountRequest->validated());
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
        try {
            $createdAccount = $api->createAccount(...$createAccountParameters);
            return response()->json([
                'success' => 'Uspješno ste kreirali email account',
                'id' => $createdAccount->account->id,
                'name' => $createdAccount->account->name,
            ], 201);
        } catch (RequestException $requestException) {
            $error = $requestException->getResponse()->getBody()->getContents();
            return response()->json($error, 400);
        }
    }

    public function createDistributionList(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:group_aliases,name',
            'description' => 'nullable|string',
            'members' => 'required|array',
            'members.*' => 'email'
        ]);
        $attributes = [];
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
        if (isset($validated['description']))
            array_push($attributes, new KeyValuePair('description', $validated['description']));

        $distributionListResponse = $api->createDistributionList($validated['name'], false, $attributes);
        $validated['uuid'] = $distributionListResponse->dl->id;
        $groupAlias = GroupAlias::create($validated);
        $members = collect($validated['members']);
        if ($members->isNotEmpty()) {
            $api->addDistributionListMember($groupAlias->uuid, $request->get('members'));
            $members = $members->map(function ($item) {
                return ['email' => $item];
            });
            $groupAlias->groupAliasMembers()->createMany($members);
        }

        return response()->json([
            'success' => 'Uspješno ste kreirali distribucijsku listu.',
            'distribution_list' => $groupAlias->load('groupAliasMembers')
        ], 201);
    }

    public function modifyDistributionList(Request $request, GroupAlias $groupAlias)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:group_aliases,name,' . $groupAlias->id,
            'description' => 'nullable|string',
            'members' => 'required|array',
            'members.*' => 'email'
        ]);
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
        $groupAlias->update($validated);
        // Ako je mijenjano ime, hitaj Rename Request
        if ($groupAlias->wasChanged(['name'])) {
            $api->renameDistributionList($groupAlias->uuid, $groupAlias->name);
        }

        // Ako je mijenjan opis, hitaj Modify Request
        if ($groupAlias->wasChanged(['description'])) {
            $api->modifyDistributionList($groupAlias->uuid, [
                new KeyValuePair('description', $groupAlias->description)
            ]);
        }

        $before = $groupAlias->groupAliasMembers()->pluck('email');
        $after = collect($validated['members']);
        $forDelete = $before->diff($after);
        $forInsert = $after->diff($before);
        if ($forDelete->isNotEmpty()) {
            $api->removeDistributionListMember($groupAlias->uuid, $forDelete->all());
            $groupAlias->groupAliasMembers()->whereIn('email', $forDelete)->delete();
        }

        if ($forInsert->isNotEmpty()) {
            $api->addDistributionListMember($groupAlias->uuid, $forInsert->all());
            $forInsert = $forInsert->map(function ($item) {
                return ['email' => $item];
            });
            $groupAlias->groupAliasMembers()->createMany($forInsert);
        }
        return $groupAlias->load('groupAliasMembers');
    }

    public function addDistributionListMember(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'members' => 'required|array',
            'members.*' => 'email'
        ]);
//        $id = '6be33f71-902d-4c38-bfca-e25692440002';
//        $members = ['antun-branko@zextras.sumit.sum.ba', 'osta'];
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
        $api->addDistributionListMember($request->get('id'), $request->get('members'));
        return response()->json([
            'success' => 'Uspješno ste dodali korisnike na distribucijsku listu.',
        ], 201);
    }

    private function createAccountString(array $parameters): array
    {
        $parameters = collect($parameters);
        $username = $parameters->pull('username');
        $domain = $parameters->pull('domain');
        $password = $parameters->pull('password');
        $additionalParameters = $parameters->map(function ($value, $key) {
            return new KeyValuePair ($key, $value);
        })->toArray();
        $emailName = $username . '@' . $domain;
        return [$emailName, $password, $additionalParameters];
    }
}
