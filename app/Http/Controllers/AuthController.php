<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SignUpRequest;
use App\User;
use App\Verify;
use App\EmailVerify;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Mailgun\Mailgun;
use App\Jobs\SendEmailJob;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup', 'verifyEmail']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Email/Password combination does not exist'], 401);
        }
        

        return $this->respondWithToken($token);
    }

    public function signup(SignUpRequest $request)
    {
        $secretanswers1 = '';
        $secretanswers2 = '';
        $secretanswers3 = '';
        $secretanswers4 = '';
        $secretanswers5 = '';
        $secretanswers6 = '';
        $secretanswers7 = '';
        $secretanswers8 = '';
        $secretanswers9 = '';
        $secretanswers10 = '';

               
        $secretanswer = $request->secretanswer;
        $stripped = str_replace(' ', '', $secretanswer);
        $stripped = strtolower($stripped);
        $flag = '';
        if($request->country === 'United Kingdom') {
            $flag = 'https://cmkt-image-prd.global.ssl.fastly.net/0.1.0/ps/1822915/6793/3682/m1/fpnw/wm1/british-flag-.jpg?1477811604&s=392fc272ec4e558e1e9f3b7dea190f2f';
        }

        if($request->country === 'United States') {
            $flag = 'https://images01.military.com/sites/default/files/styles/full/public/media/global/newscred/2017/04/us-flag-21-apr-2017.jpeg.jpg?itok=TGwBeaGn';
        }

        $userCheck = User::where('email', $request->email)->first();

        try {
            if(is_null($userCheck)) {
                $user = User::create([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'country' => $request->country,            
                    'phone' => $request->phone,
                    'password' => $request->password,
                    'flag' => $flag
                ]);
                //\Log::info($user->id);
                $secrets = str_split($stripped);
                $secretLength = count($secrets);

                if(isset($secrets[0]) && $secrets[0] != null) {       
                    $secretanswers1 = bcrypt($secrets[0]);           
                }
                if(isset($secrets[1]) && $secrets[1] != null) {       
                    $secretanswers2 = bcrypt($secrets[1]);           
                }
                if(isset($secrets[2]) && $secrets[2] != null) {       
                    $secretanswers3 = bcrypt($secrets[2]);           
                }
                if(isset($secrets[3]) && $secrets[3] != null) {       
                    $secretanswers4 = bcrypt($secrets[3]);           
                }
                if(isset($secrets[4]) && $secrets[4] != null) {       
                    $secretanswers5 = bcrypt($secrets[4]);           
                }
                if(isset($secrets[5]) && $secrets[5] != null) {       
                    $secretanswers6 = bcrypt($secrets[5]);           
                }
                if(isset($secrets[6]) && $secrets[6] != null) {       
                    $secretanswers7 = bcrypt($secrets[5]);           
                }
                if(isset($secrets[7]) && $secrets[7] != null) {       
                    $secretanswers8 = bcrypt($secrets[7]);           
                }
                if(isset($secrets[8]) && $secrets[8] != null) {       
                    $secretanswers9 = bcrypt($secrets[8]);           
                }
                if(isset($secrets[9]) && $secrets[9] != null) {       
                    $secretanswers10 = bcrypt($secrets[9]);           
                }       

                $verify = Verify::create([
                    'user_id' => $user->id,
                    'secretquestion' => $request->secretquestion,
                    'secretanswer1' => $secretanswers1,
                    'secretanswer2' => $secretanswers2,
                    'secretanswer3' => $secretanswers3,
                    'secretanswer4' => $secretanswers4,
                    'secretanswer5' => $secretanswers5,
                    'secretanswer6' => $secretanswers6,
                    'secretanswer7' => $secretanswers7,
                    'secretanswer8' => $secretanswers8,
                    'secretanswer9' => $secretanswers9,
                    'secretanswer10' => $secretanswers10,
                ]);

                $date = date('Y-m-d H:i:s');
                $verificationString = $secretanswer.$date;
                $verificationString = urlencode($verificationString);
                $verificationToken = bcrypt($verificationString);
                $userEmail = urlencode($request->email);

                $params = [
                    'token' => '/verify-email?userEmail='.$userEmail.'&tokenstring='.$verificationToken,
                    'email' => $request->email
                ];
                
                $this->dispatchEmail($params);

                EmailVerify::create([
                    'user_id' => $user->id,
                    'email' => $request->email,
                    'tokenstring' => $verificationToken
                ]);
            } else {
                throw new Exception('This email already exists');
            }                            
            
        } catch(Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        return $this->login($request);
    }

    /**
     * Resend verification email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerify(Request $request)
    {   
        $user = User::where('email', $request->email)->first();
        try {
            
            
            if(!is_null($user)) {
                
                $date = date('Y-m-d H:i:s');
                $userEmail = urlencode($request->email);
                $userEmailString = urlencode($request->email.$date);
                    
                $verificationToken = bcrypt($userEmailString);

                $params = [
                    'token' => '/verify-email?userEmail='.$userEmail.'&tokenstring='.$verificationToken,
                    'email' => $request->email
                ];

                EmailVerify::where(['email' => $userEmail, 'tokenstring' => $verificationToken])->delete();
                
                EmailVerify::create([
                    'user_id' => $user->id,
                    'email' => $request->email,
                    'tokenstring' => $verificationToken
                ]);

                $this->dispatchEmail($params);
            } else {
                throw new Exception('User not found');
            }          
            
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['message' => 'Verification email re-sent successfully']);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $email = $request->query('userEmail');
        $token = $request->query('tokenstring');
        $userEmail = urldecode($email);
        $verified = false;
       
        $count = EmailVerify::where(['email' => $userEmail, 'tokenstring' => $token])->count();
        
        if($count > 0) {
            $updateUser = User::where('email', $userEmail)
            ->update([
                'email_verified_at' => date('Y-m-d H:i:s')
            ]);

            EmailVerify::where(['email' => $userEmail, 'tokenstring' => $token])->delete();
            $verified = true;
        }
        
        return view('Email.accountSignup', [            
            'email'=> $email,
            'verified' => $verified]
        );
    }

    /**
     * Dispatch verification email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function dispatchEmail($params)
    {       
        SendEmailJob::dispatch($params)->delay(now()->addSeconds(5));
    }
    

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * verify secret answer to fully login User.
     *
     * @return \Illuminate\Http\JsonResponse
     */ 
    public function verifyAnswer(Request $request)
    {  
        $newVerifyInfo = [];
        $secretInfos = [];
        $verification = Verify::where('user_id', auth()->user()->id)->first();
        
        if($verification->secretanswer1 != '') {
            array_push($newVerifyInfo, $verification->secretanswer1);
        }
        if($verification->secretanswer2 != '') {
            array_push($newVerifyInfo, $verification->secretanswer2);
        }
        if($verification->secretanswer3 != '') {
            array_push($newVerifyInfo, $verification->secretanswer3);
        }
        if($verification->secretanswer4 != '') {
            array_push($newVerifyInfo, $verification->secretanswer4);
        }
        if($verification->secretanswer5 != '') {
            array_push($newVerifyInfo, $verification->secretanswer5);
        }
        if($verification->secretanswer6 != '') {
            array_push($newVerifyInfo, $verification->secretanswer6);
        }
        if($verification->secretanswer7 != '') {
            array_push($newVerifyInfo, $verification->secretanswer7);
        }
        if($verification->secretanswer8 != '') {
            array_push($newVerifyInfo, $verification->secretanswer8);
        }
        if($verification->secretanswer9 != '') {
            array_push($newVerifyInfo, $verification->secretanswer9);
        }
        if($verification->secretanswer10 != '') {
            array_push($newVerifyInfo, $verification->secretanswer10);
        }
        
        foreach($newVerifyInfo as $key => $value) {
            foreach($request->secrets as $ans) {
                $splitAnsArray = explode(',', $ans);
                
                $charNumber = +($splitAnsArray[0]);
                
                if(($charNumber - 1) == $key) {
                                        
                    if(password_verify($splitAnsArray[1], $value)) {                        
                        array_push($secretInfos, $splitAnsArray[1]);                       
                    }
                }
            }
        }
       
        $secretLength = count($secretInfos);
       
        if($secretLength == 3) {
            return response(['secrets' => $secretInfos]);
        } else {
            return response(['secrets' => 'You have entered incorrect details']);
        }        
        
    }

    /**
     * fetch and check secret answer to verify User.
     *
     * @return \Illuminate\Http\JsonResponse
     */     
    public function verify(Request $request)
    {   
        $newVerify = [];
        $secretInfo = [];
        $verification = Verify::where('user_id', $request->id)->first();                
        
        if($verification->secretanswer1 != '') {
            array_push($newVerify, $verification->secretanswer1);
        }
        if($verification->secretanswer2 != '') {
            array_push($newVerify, $verification->secretanswer2);
        }
        if($verification->secretanswer3 != '') {
            array_push($newVerify, $verification->secretanswer3);
        }
        if($verification->secretanswer4 != '') {
            array_push($newVerify, $verification->secretanswer4);
        }
        if($verification->secretanswer5 != '') {
            array_push($newVerify, $verification->secretanswer5);
        }
        if($verification->secretanswer6 != '') {
            array_push($newVerify, $verification->secretanswer6);
        }
        if($verification->secretanswer7 != '') {
            array_push($newVerify, $verification->secretanswer7);
        }
        if($verification->secretanswer8 != '') {
            array_push($newVerify, $verification->secretanswer8);
        }
        if($verification->secretanswer9 != '') {
            array_push($newVerify, $verification->secretanswer9);
        }
        if($verification->secretanswer10 != '') {
            array_push($newVerify, $verification->secretanswer10);
        }

        $secretLength = count($newVerify);
        array_push($secretInfo, $verification->secretquestion);
        array_push($secretInfo, $secretLength);

        return response(['secrets' => $secretInfo]);
    }

    /**
     * Update details of the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editProfile(Request $request)
    {
       
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = auth()->user()->email;        
        $phone = $request->phone;
        $country = $request->country;

        if($request->theImage) {
            $profile = User::where('email', $email)
            ->update([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'profile_pic' => $request->theImage['paths'][0],
                'phone' => $phone,
                'country' => $country
            ]);

            $user = User::where('email', $email)->first();
                
        return response(['profile' => $user]);
        }

        $profile = User::where('email', $email)
            ->update([
                'firstname' => $firstname,
                'lastname' => $lastname,                
                'phone' => $phone,
                'country' => $country
            ]);
            $user = User::where('email', $email)->first();
        
        return response(['profile' => $user]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 * 2,
            'user_info' => auth()->user()
        ]);
    }
}