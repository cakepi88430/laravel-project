<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Fortify;
use Illuminate\Contracts\Auth\PasswordBroker;
use Laravel\Fortify\Actions\CompletePasswordReset;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $repos = new CreateNewUser();
        $data = $request->all();
        $user = $repos->create($data);
        // $user->sendEmailVerificationNotification();
        if($user){
            event(new Registered($user));
            $response = [
                "status" => true,
                "method" => "sign up",
                "message" => "success"
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                "status" => false,
                "method" => "sign up",
                "message" => "unexpected error",
            ];
            return response()->json($response, 200);
        }
    }

    public function login(Request $request)
    {
        $token_name = "Cake";
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                // 'message' => ['These credentials do not match our records.']
                'message' => [trans('auth.failed')]
            ], 404);
        }
        // $user->tokens()->where('name', $token_name)->delete();
        $token = $user->createToken($token_name, ['cake'])->plainTextToken;
        // $token = $user->createToken('token')->plainTextToken;
        $response = array();
        $header = array(
            'Token' => 'Bearer ' . $token,
        );

        $is_verify = $user->hasVerifiedEmail();

        if($user){
            $status = true;
        } else {
            $status = false;
        }
        if(!$is_verify) {
            $user->sendEmailVerificationNotification();
            $status = false;
            $message = '[Email not verified] A new verification link has been sent to the email address you provided during registration.';
            $response['message'] =  $message;
        }
        $response['status'] =  $status;
    
        return response()->json($response, 201)->withHeaders($header)->withCookie(cookie('Token', 'Bearer ' . $token, 1440));
    }

    public function GetAnonymousToken(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if($request->username != "anonymous") {
            return response([
                'message' => [trans('auth.failed')]
            ], 404);
        }

        $user = User::where('name', $request->username)->first();
        
        $date = Carbon::now()->subDays(1)->toDateTimeString();
        $tokens = $user->tokens()->where('created_at', "<=", $date)->delete();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => [trans('auth.failed')]
            ], 404);
        }
        $token_name = "anonymous_" . $request->ip();
        $token = $user->createToken($token_name, ['cake'])->plainTextToken;
        $response = array();
        $header = array(
            'Token' => 'Bearer ' . $token,
        );
        if($user) {
            $status = 'ok';
        }
        $response['status'] =  $status;
        return response()->json($response, 201)->withHeaders($header)->withCookie(cookie('Token', 'Bearer ' . $token, 1440));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([],204);
    }

    public function EmailVerify(Request $request)
    {
        
        $user = User::find($request->route('id'));
        if ($user->hasVerifiedEmail()) {
            // return redirect(env('FRONT_URL') . '/email/verify/already-success');
            return view('auth.APIUserHasVerify');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // return redirect('/email/verify/success');
        return view('auth.APIUserVerify');
    }

    public function ForgotPassword(Request $request)
    {
        $request->validate([Fortify::email() => 'required|email']);

        $status = $this->broker()->sendResetLink(
            $request->only(Fortify::email())
        );

        $response_status = false;

        if($status == Password::RESET_LINK_SENT) {
            $response_status = true;
        } 

        $response['status'] =  $response_status;
        return response()->json($response, 200);

    }

    /**
     * Copy from Laravel\Fortify\Http\Controllers\NewPasswordController@store
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function ResetPassword(Request $request, StatefulGuard $guard)
    {
        $request->validate([
            'token' => 'required',
            Fortify::email() => 'required|email',
            'password' => 'required',
        ]);
        // password_confirmation

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = $this->broker()->reset(
            $request->only(Fortify::email(), 'password', 'password_confirmation', 'token'),
            function ($user) use ($request, $guard) {
                app(ResetsUserPasswords::class)->reset($user, $request->all());
                app(CompletePasswordReset::class)($guard, $user);
            }
        );

        $response_status = false;
        if($status == Password::PASSWORD_RESET) {
            $response_status = true;
        } 
        $response['status'] =  $response_status;
        return response()->json($response, 200);
    }

    public function UpdatePassword(Request $request)
    {
        $action = new UpdateUserPassword();
        $user = $request->user();
        $res = $action->update($user, $request->all());
        $response['status'] =  true;
        return response()->json($response, 200);
    }

    protected function broker(): PasswordBroker
    {
        return Password::broker(config('fortify.passwords'));
    }
}