<?php


namespace App\Http\Controllers\Auth;


use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'longitude' => 'required|longitude',
            'latitude' => 'required|latitude',
            'language_id' => 'nullable|integer|exists:languages,id',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:7',
        ]);

        $user = User::create($validated);
//        event(new Registered($user));
        return $user;
    }

    public function sendVerificationEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
    }

    public function resendEmailVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => $status])
            : response()->json(['email' => $status]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
            }
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => $status])
            : response()->json(['email' => $status]);
    }

    public function zimbraLoginWithSoapClient()
    {
//        $URL = 'https://zimbra.sumit.sum.ba:7071/service/admin/soap';

//        $param = [
//            'name' => 'admin',
//            'password' => 'eb1QUO7Ak',
//            'authToken' => '0_cab45c593247bca26e4414fce899e8dc12a8c384_69643d33363a38653265613838312d653663342d343366622d396438302d3962313336346664623235353b6578703d31333a313631393230333731313437373b61646d696e3d313a313b747970653d363a7a696d6272613b753d313a613b7469643d393a3837313930363433303b76657273696f6e3d31343a382e382e31355f47415f333836393b637372663d313a313b'
//        ];
//        $client = new SoapClient(null, [
//            'location' => $URL,
//            'uri' => "urn:zimbraAdmin",
//            'trace' => 1,
//            'ZM_ADMIN_AUTH_TOKEN' => '0_cab45c593247bca26e4414fce899e8dc12a8c384_69643d33363a38653265613838312d653663342d343366622d396438302d3962313336346664623235353b6578703d31333a313631393230333731313437373b61646d696e3d313a313b747970653d363a7a696d6272613b753d313a613b7469643d393a3837313930363433303b76657273696f6e3d31343a382e382e31355f47415f333836393b637372663d313a313b'
//        ]);
//        dd($client->CheckHealthRequest()->__getLastResponse());
//        return $client->getLastRequestHeaders();
//
//        return $client->AuthRequest($param)->__getFunctions();

//        return Soap::to('https://zimbra.sumit.sum.ba:7071/service/admin/soap')->withOptions($options)->call('POST', ['AuthRequest']));

        /*
        $return = $client->__soapCall("getTimeZoneTime",
            array(new SoapParam(new SoapVar('ZULU', XSD_DATETIME), 'timezone')),
            array('soapaction' => 'http://www.Nanonull.com/TimeService/getTimeZoneTime')
        );


            $opts = array(
                'http' => array(
                    'user_agent' => 'PHPSoapClient'
                )
            );
            $context = stream_context_create($opts);

            $wsdlUrl = 'https://zimbra.sumit.sum.ba:7071/service/admin/soap';
            $soapClientOptions = [
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_MEMORY,
                'soap_version'   => SOAP_1_1,
                'exceptions' => true,
                'keep_alive' => true,
                'exception' => 0
            ];

            $client = new SoapClient($wsdlUrl, $soapClientOptions);
            return 2;
//            dd($client);
            $checkVatParameters = array(
                'countryCode' => 'DK',
                'vatNumber' => '47458714'
            );


        $options = [
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
            'http' => [
                'user_agent' => 'PHPSoapClient'
            ],
            'soap_version' => SOAP_1_2,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true
                    ]
                ]
            )
        ];
        //libxml_disable_entity_loader(false); //adding this worked for me
//
//        $k = new \SoapClient('https://zimbra.sumit.sum.ba:7071/service/admin/soap', $options);
//        $k->SoapClient(NULL);
//        $k->call('d');

        dd(Soap::to('https://zimbra.sumit.sum.ba:7071/service/admin/soap')->withOptions($options)->call('POST', ['AuthRequest']));
*/
    }
}
