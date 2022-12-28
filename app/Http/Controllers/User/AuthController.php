<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordFormRequest;
use App\Http\Requests\User\AuthFormRequest;
use App\Http\Requests\User\UserAreaFormRequest;
use App\Http\Requests\User\UpdateUserFormRequest;
use App\Http\Requests\User\UserFormRequest;
use App\Http\Requests\User\UserLoginFormRequest;
use App\Models\Cart;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class AuthController extends Controller
{



    public function signUp(UserFormRequest $request)
    {

        $data = $request->all();

        $data['mobile'] = ltrim($data['mobile'], "0");

        $user = User::create($data);

        $token = $user->createToken('UserToken')->plainTextToken;

        Cart::create(['user_id' => $user->id]);

        return $this->dataResponse(['user' => $user, 'complete_profile' => false, 'token' => $token], 'success', 200);
    }



    public function login(UserLoginFormRequest $request)
    {
        if (isset($request->email) && !empty($request->email)) {
            $user = User::where('email', $request->email)->whereNull('deleted_at')->first();
        } elseif (isset($request->mobile) && !empty($request->mobile)) {
            $user = User::where('mobile', $request->mobile)->whereNull('deleted_at')->first();
        }

        $token = $user->createToken('UserToken')->plainTextToken;

        if (isset($user->email) && isset($user->first_name) && isset($user->last_name)) {
            $complete_profile = true;
        } else {
            $complete_profile = false;
        }

        return $this->dataResponse(['user' => $user, 'complete_profile' => $complete_profile, 'token' => $token], 'success', 200);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return $this->successResponse(' success logged out', 200);
    }

    /**
     * auth
     */
    public function authentication(AuthFormRequest $request)
    {

        $data = $request->validated();
        $user = User::where('mobile', $data['mobile'])->first();

        if (!$user) {

            $data['mobile'] = ltrim($data['mobile'], "0");

            $user = User::create($data);
            $token = $user->createToken('UserToken')->plainTextToken;
            Cart::create(['user_id' => $user->id]);

            $message = 'Register Successfuly';
        } else {
            $token = $user->createToken('UserToken')->plainTextToken;
            $message = 'Login Successfuly';
        }

        if (isset($user->email) && isset($user->first_name) && isset($user->last_name)) {
            $complete_profile = true;
        } else {
            $complete_profile = false;
        }
        return $this->dataResponse([
            'user' => $user, 'complete_profile' => $complete_profile,
            'token' => $token
        ], $message, 200);
    }
    public function ChangePassword(ChangePasswordFormRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponseWithMessage('Current password does not match!', 400);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        return $this->successResponse();
    }

    /**
     * Undocumented function
     *
     * @param UserAreaFormRequest $request
     * @return void
     */
    public function userArea(UserAreaFormRequest $request)
    {
        $user = Auth::user();
        $user->update($request->validated());
        return $this->successResponse();
    }


    public function updateProfile(UpdateUserFormRequest $request)
    {

        $user = Auth::user();

        $user->update($request->input());


        if (isset($user->email) && isset($user->first_name) && isset($user->last_name)) {
            $complete_profile = true;
        } else {
            $complete_profile = false;
        }

        return $this->dataResponse(['user' => $user, 'complete_profile' => $complete_profile], 'success', 200);

    }



    public function storeToken(Request $request)
    {
        // dd($request->token);
            $user=Provider::findOrfail(1);
            $user->update(['fcm_token'=>$request->token]);
            return response()->json(['Token successfully stored.']);
    }
  


    public function testSendNotfiaction(){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
                            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $user=Provider::where('id',1)->firstOrfail();
        $token=$user->fcm_token ;


        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);


        // $message = CloudMessage::withTarget('token', $token)->withNotification(['title' => 'My title', 'body' => 'My Body']);
        // $keyPath = storage_path("app/firebase/anwar-project-8fece-firebase-adminsdk-b0ojg-d7e6ecb3c6.json");

        // $messaging = (new Factory)->withServiceAccount($keyPath)->createMessaging();

        // $messaging = $auth->createMessaging();

        //   dd($messaging->send($message));


    }
    // public function testEmail(Request $request)
    // {
    //     Mail::send(['html' => 'view_name'], $data, function ($m) use ($email) {
    //         $m->from('anwarsaeed1@yahoo.com', 'name');
    //         $m->to($email, $email)->subject('email subject');
    //     });
    // }

       /**
     * Write code on Method
     *
     * @return response()
     */
    public function firebaseOtp()
    {

        // change $keyPath to yours, do not just copy and paste
        $keyPath = storage_path("app/firebase/anwar-project-8fece-firebase-adminsdk-b0ojg-d7e6ecb3c6.json");
        $auth = (new Factory)->withServiceAccount($keyPath)->createAuth();

        try {
            // change $examplePhoneNumber to yours
            $examplePhoneNumber = '+201066520139';
            $user = $auth->getUserByPhoneNumber($examplePhoneNumber);

            return ($user);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
    }
}
