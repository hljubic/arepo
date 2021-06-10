<?php

namespace App\Http\Controllers;

use App\AAI\SumUser;
use App\Exceptions\UserExistsException;
use App\Helpers\Constants;
use App\Mail\IdentityCredentials;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use \Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class IdentityController extends Controller
{

    public function issueIdentity(Request $request, User $user)
    {
        if (Auth::user()->cannot('issueIdentity', $user))
            abort('403', 'Ne mozete izdati identitet ovome korisniku');

        try {
            $result = $user->issueIdentity();
            if ($request->email) {
                $user->emailCredentials($request->email, $result);
            }
            return json_encode($result);
        } catch (UserExistsException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], 409);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], 400);
        }
    }

    public function changePassword(Request $request, User $user)
    {
        //if (Auth::user()->cannot('changePassword', $user))
        //    abort('403', 'Ne možete promijeniti lozinku.');

        $credentials = $user->changePassword($request->password);

        return json_encode($credentials);
    }

    public function resetPassword(Request $request, User $user)
    {
        /*if (Auth::user()->cannot('issueIdentity', $user))
            abort('403', 'Ne mozete resetirati lozinku ovome korisniku');*/

        $credentials = $user->changePassword();

        if ($request->email) {
            try {
                $user->emailCredentials($request->email, $credentials);
            } catch (\Exception $exception) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ], 400);
            }
        }

        return json_encode($credentials);
    }
}
