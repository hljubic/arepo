<?php

use Aacotroneo\Saml2\Saml2Auth;
use App\Http\Controllers\IdentityController;
use \App\Http\Controllers\SSOController;
use \Illuminate\Http\Request;

Route::get('/login', [SSOController::class, 'login'])->name('login');
Route::get('/logout', [SSOController::class, 'logout'])->name('logout');

Route::get('/{idpName}/sls', [SSOController::class, 'sls'])->name('saml2_sls');
Route::post('/{idpName}/acs', [SSOController::class, 'acs'])->name('saml2_acs');
Route::get('/{idpName}/metadata', [SSOController::class, 'metadata'])->name('saml2_metadata');

Route::get('/user', function () {
    $user = Auth::user();
    if ($user->role_id == 2)
        $user->setAttribute('school_identifier', $user->school->identifier);
    return $user;
    //return new UserResource($user);
})->middleware('auth')->name('user');

// LDAP
Route::post('/identity/{user}/password', [IdentityController::class, 'changePassword']);

Route::get('/zimbra_preauth', function (Request $request) {
    /**
     * Globals. Can be stored in external config.inc.php or retreived from a DB.
     */
    $PREAUTH_KEY = env("ZIMBRA_PREAUTH_KEY");
    $WEB_MAIL_PREAUTH_URL = env("ZIMBRA_PREAUTH_URL");

    if (!Auth::check()) {
        $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('hredu'));

        return $saml2Auth->login(env('APP_URL') . '/sso/zimbra_preauth');
    }
    /**
     * User's email address and domain. In this example obtained from a GET query parameter.
     * i.e. preauthExample.php?email=user@domain.com&domain=domain.com
     * You could also parse the email instead of passing domain as a separate parameter
     */
    $user = Auth::user()->uid;
    $domain = env("ZIMBRA_DOMAIN");;

    $email = "{$user}@{$domain}";

    if (empty($PREAUTH_KEY)) {
        die("Need preauth key for domain " . $domain);
    }

    /**
     * Create preauth token and preauth URL
     */
    $timestamp = time() * 1000;
    $preauthToken = hash_hmac("sha1", $email . "|name|0|" . $timestamp, $PREAUTH_KEY);
    $preauthURL = $WEB_MAIL_PREAUTH_URL . "?account=" . $email . "&by=name&timestamp=" . $timestamp . "&expires=0&preauth=" . $preauthToken;

    /**
     * Redirect to Zimbra preauth URL
     */
    header("Location: $preauthURL");
})->name('preauth');

//Route::post('/identity/password', 'IdentityController@changePassword');
//Route::post('/identity/email', 'IdentityController@emailCredentials');
//Route::get('/ldap_users', 'IdentityController@indexAAIUsers');
