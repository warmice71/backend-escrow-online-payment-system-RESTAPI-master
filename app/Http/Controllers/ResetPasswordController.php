<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\Requests\ChangePasswordRequest;
use App\Jobs\SendPasswordResetJob;

class ResetPasswordController extends Controller
{
    public function sendEmail(Request $request) 
    {
        // \Log::info($request->all());
        
        if (!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }

        $this->send($request->email);
        return $this->successResponse();
    }

    public function send($email)
    {
        
        $token = $this->createToken($email);
        $url = config('app.frontendurl').'/response-reset?token='. $token;

        $params = [
            'url' => $url,
            'email' => $email
        ];
       
       $this->dispatchResetPassword($params);
        
    }

    /**
     * Dispatch password reset email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function dispatchResetPassword($params)
    {       
        SendPasswordResetJob::dispatch($params)->delay(now()->addSeconds(5));
    }

    public function createToken($email)
    {
        $oldToken = DB::table('password_resets')->where('email', $email)->first();

        if($oldToken) {
            
            return $oldToken->token;
        }

        $token = Str::random(60);
        $this->saveToken($token, $email);
        return $token;
    }

    public function saveToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function validateEmail($email)
    {
        return !!User::where('email', $email)->first();
    }

    public function failedResponse()
    {
        return response()->json([
            'message' => 'Email was not found'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse()
    {
        return response()->json([
            'message' => 'Reset Email has been sent. Please check your email'
        ], Response::HTTP_OK);
    }

    public function resetPassword(ChangePasswordRequest $request) 
    {          
        return $this->getPasswordResetTableRow($request)->count() > 0 ? $this->changePassword($request) : $this->tokenNotFoundResponse();
    }

    private function getPasswordResetTableRow($request)
    {       
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->resetToken
        ]);
    }

    private function tokenNotFoundResponse()
    {
        return response()->json([
            'error' => 'Token or Email is incorrect'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function changePassword($request)
    {
        $user = User::whereEmail($request->email)->first();
        $user->update(['password' => $request->password]);
        $this->getPasswordResetTableRow($request)->delete();
        return response()->json(['data' => 'Password change completed'], Response::HTTP_CREATED);
    }
}
