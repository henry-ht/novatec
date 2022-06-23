<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    protected $tokenPhrase = 'novatec Personal Access Client';

    public function login(Request $request){
        $message    = ['message' => [__('Something is not right')]];
        $status     = 'warning';
        $data       = false;

        $credentials = $request->only([
            'email',
            'password',
            'remember_token'
        ]);

        $x_user = $request->header('x_user');
        $x_password = $request->header('x_password');

        $validation = Validator::make($credentials,[
            'email'             => 'required|max:250|email|exists:users,email|in:'.$x_user,
            'password'          => 'required|max:20|min:6|in:'.$x_password,
            'remember_token'    => 'sometimes|required|boolean'
        ]);

        if (!$validation->fails()) {

            $authOk = Auth::attempt([
                'email'     => $credentials['email'],
                'password'  => base64_decode($credentials['password'])
            ]);

            if ($authOk) {
                $authenticatedUser  = Auth::user();

                $tokenResult        = $authenticatedUser->createToken($this->tokenPhrase);
                $token              = $tokenResult->token;

                if (isset($credentials['remember_token'])){
                    if($credentials['remember_token']){
                        $token->expires_at = Carbon::now()->addMonths(6);
                    }
                }

                $token->save();

                $tokenUser = [
                    'access_token'  => $tokenResult->accessToken,
                    'token_type'    => 'Bearer',
                    'expires_at'    => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
                ];

                $message    = ['message' => [__('Welcome')]];
                $status     = 'success';
                $data       = $tokenUser;

            }else{
                $message    = ['message' => [__('Invalid credentials'), __('Try again or talk to the administrator')]];
                $status     = 'warning';
                $data       = false;
            }


        }else{
            $message    = $validation->messages();
            $status     = 'warning';
            $data       = false;

        }

        return response([
            'status'    => $status,
            'data'      => $data,
            'message'   => $message,
        ],200);
    }
}
