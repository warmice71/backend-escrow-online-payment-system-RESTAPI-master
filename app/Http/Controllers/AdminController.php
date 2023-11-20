<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Payment;

class AdminController extends Controller
{    
    public function __construct()
    {
        $this->middleware('auth:api');
        
        $this->middleware('checkAdmin');
    }

    public function view()
    {
        return response()->json(['message' => 'Successfully viewed admin board']);
    }

    //Clear config cache
    public function clearCache()
    {
        try {
            $clearCache = Artisan::call('cache:clear');
            $optimize = Artisan::call('optimize');
            $configCache = Artisan::call('config:cache');            
                                    
        } catch(Exception $e) {
            
            return response()->json(
                ['errors' => $e->getMessage()], 
                Response::HTTP_NOT_FOUND
            );
        }        
        return response()->json(['message' => 'Successfully cleared cache']);
    }

    //Search for a transaction as an Admin
    public function searchPaymentAdmin(Request $request)
    {        
        $paymentId = $request->paymentId;
        $paymentId = strip_tags($paymentId);
        $paymentId = str_replace(' ', '', $paymentId);

        try {
            
            $payment = Payment::where([
                ['hash_id', $paymentId],
                ['payment_completed', true]
            ])->first();

            if(!is_null($payment)){                                           
    
                return response()->json(['payment' => $payment]);
                
            } else {
                throw new Exception('This Transaction was not found');
            }
                     
            
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }  
         
    }
    
}
