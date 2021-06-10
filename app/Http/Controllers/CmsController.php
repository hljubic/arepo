<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsController extends Controller
{
    public function createCms(School $school)
    {
        if (!$school->admin)
            throw ValidationException::withMessages(['errors' => 'Niste odabrali admina škole.']);

        $admin = User::where('id', $school->admin_id)->first();
        $domain = env('CMS_DOMAIN');
        $apiUrl = "https://{$domain}/wp-json/wp/v2/sites/create";
        $schoolDomainUrl = 'test-' . $school->identifier . '.' . env('MAIN_DOMAIN');
        $createUserApiUrl = "https://{$schoolDomainUrl}/wp-json/wp/v2/users";
        $response = Http::withoutVerifying()->withToken($this->getBearerToken())
            ->post($apiUrl, [
                'domain' => $schoolDomainUrl,
//                'admin_email' => env('SUPERADMIN'),
//                'admin_user' => env('SUPERADMIN'),
                'options' => ['public']
            ]);

        if (!$response->successful())
            return response()->json(['errors' => $response->json('message')], 400);


        $response = Http::withoutVerifying()->withToken($this->getBearerToken($schoolDomainUrl))
            ->post($createUserApiUrl, [
                'username' => Str::remove('.', $admin->uid),
                'email' => $admin->email,
                'roles' => ['administrator'],
                'password' => Str::random(14)
            ]);

        if (!$response->successful())
            return response()->json(['errors' => $response->json('message')], 400);

        $school->update([
            'url' => $schoolDomainUrl,
            'site_id' => $response->body(),
            'cms_status' => 1
        ]);
        return response()->json(['success' => 'Uspješno ste kreirali CMS.', 'school' => $school->url]);
    }

    public function publishCms(School $school)
    {
        $domain = env('CMS_DOMAIN');
        $apiUrl = "https://{$domain}/wp-json/wp/v2/sites/update";
        $schoolDomainUrl = $school->identifier . '.' . env('MAIN_DOMAIN');
        $response = Http::withoutVerifying()->withToken($this->getBearerToken())
            ->put($apiUrl, [
                'blog_id' => $school->site_id,
                'domain' => $schoolDomainUrl
            ]);
        if (!$response->successful())
            return response()->json(['errors' => $response->json('message')], 400);

        $school->update([
            'url' => $schoolDomainUrl,
            'cms_status' => 2
        ]);

        return response()->json(['success' => 'Uspješno ste objavili CMS.', 'school' => $school->url]);
    }
    private function getBearerToken($schoolDomainUrl = null): ?string
    {

        $domain = env('CMS_DOMAIN');
        if ($schoolDomainUrl)
            $domain = $schoolDomainUrl;

        $url = "https://{$domain}/wp-json/jwt-auth/v1/token";
        $queryParams = [
            'username' => env('CMS_SUPERADMIN_USERNAME'),
            'password' => env('CMS_SUPERADMIN_PASSWORD'),
        ];

        $response = Http::withoutVerifying()->post($url, $queryParams);

        if ($response->successful())
            return $response->json()['token'];

        return null;
    }
}
