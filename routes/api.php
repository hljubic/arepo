<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DnsController;
use App\Http\Controllers\GroupAliasController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\InstitutionConnectionController;
use App\Http\Controllers\InstitutionPositionController;
use App\Http\Controllers\InstitutionRoleController;
use App\Http\Controllers\InstitutionTypeController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\ProfessionalStatusController;
use App\Http\Controllers\ResourceFileController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\ScienceFieldController;
use App\Http\Controllers\StrixController;
use App\Http\Controllers\StudentTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZimbraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Zimbra\Admin\AdminFactory;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Pripaziti na rute sa wildcardovima. Ako imamo users/{user} iznad users/frontend uopće neće ulaziti u ovu /frontend rutu.
// Wildcard rute bi trebale ici ispod ovih obicnih

//Route::middleware('zimbra-authenticate')->get('ostauron', function() {
//    $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
//    $k = $api->createDomain('ostaaa.sumit.sum.ba');
//    dd($k);
//});

Route::get('osta', function () {
    return \App\Services\Strix\PreviewNews::getNews('test-antonio-glavic-zg');
});

// Zimbra
Route::get('/zimbra/email', [UserController::class, 'zimbraEmail']);
Route::get('/zimbra/test', [UserController::class, 'zimbraTest']);
Route::get('/zimbra/test2', [UserController::class, 'zimbraTest2']);
Route::post('/zimbra/create_account', [ZimbraController::class, 'createAccount']);
Route::get('/zimbra/preAuthLogin', [UserController::class, 'zimbraPreAuthLogin']);
Route::post('/zimbra/create_distribution_list', [ZimbraController::class, 'createDistributionList']);
Route::post('/zimbra/add_distribution_list_member', [ZimbraController::class, 'addDistributionListMember']);
Route::post('/zimbra/modify_distribution_list/{groupAlias}', [ZimbraController::class, 'modifyDistributionList']);
Route::post('/zimbra/clearAllData', [UserController::class, 'clearZimbraData']);


// PowerDns
Route::post('power_dns/zones', [DnsController::class, 'createZone']);
Route::post('power_dns/dns_records', [DnsController::class, 'createDnsRecords']);

// Schools
Route::post('schools/dns_records', [SchoolController::class, 'changeDNSRecords']);
Route::post('schools/get_school_from_carnet', [SchoolController::class, 'getSchoolFromCarnet']);

Route::post('groups/get_departments_from_carnet', [GroupController::class, 'getDepartmentsFromCarnet']);

// Users
Route::get('users/oib/{oib}', [UserController::class, 'getByOib']);
Route::post('users/{user}/change_status', [UserController::class, 'changeStatus']);

Route::post('_upload_file/{keep_name?}', [Controller::class, 'uploadFile']);

Route::post('users/import_csv', [UserController::class, 'importCSV']);
Route::post('users/import_xml', [UserController::class, 'importXML']);


Route::post('/sanctum/token', [AuthController::class, 'login']);

// Kreiraj CMS
Route::post('cms/create/{school}', [CmsController::class, 'createCms']);
Route::post('cms/publish/{school}', [CmsController::class, 'publishCms']);

// Strix import
Route::get('strix/{school}/get_news', [StrixController::class, 'getNews']);
Route::post('strix/{school}/import_news', [StrixController::class, 'importNews']);


// All routes
$resources = [
    'users' => UserController::class,
    'schools' => SchoolController::class,
    'groups' => GroupController::class,
    'group_alias' => GroupAliasController::class,
    'occupations' => OccupationController::class,
    'school_years' => SchoolYearController::class,
    'student_type' => StudentTypeController::class,
    'science_field' => ScienceFieldController::class,
    'institution_roles' => InstitutionRoleController::class,
    'institution_types' => InstitutionTypeController::class,
    'institution_positions' => InstitutionPositionController::class,
    'institution_connections' => InstitutionConnectionController::class,
    'professional_statuses' => ProfessionalStatusController::class
];

foreach ($resources as $resource => $controller) {
    Route::get($resource . '/frontend', [$controller, 'getFrontendData']);
    Route::get($resource . '/{id}/{relation}', [$controller, 'indexRelation']);
    Route::post($resource . '/{id}/{relation}', [$controller, 'manageRelation']);
}

Route::apiResources(
    $resources
);

Route::get('/user', function (Request $request) {
    return $request->user();
});


