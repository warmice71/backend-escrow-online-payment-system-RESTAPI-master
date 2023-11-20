<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Item;
use App\User;
use App\Image;
use App\SearchId;
use App\Jobs\SendSearchIdJob;
use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemCollection;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Jobs\SendInvoiceJob;


class ItemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');               
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page = null)
    {
        if(auth()->user()->status > 2){
                        
            $items = Item::orderBy('id', 'desc')->paginate(2);            
            
        }else{
            
            $items = Item::where('seller_id', auth()->user()->id)->orderBy('id', 'desc')->paginate(10);
        }
              
        return response()->json(['data' => $items]);

    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ItemRequest $request)
    {        
        // \Log::info($request->all());

        try {
            $user = User::where('id', auth()->user()->id)->first();

            if(!is_null($user->email_verified_at)) {

                $itemName = $request->itemName;
                $itemPrice = $request->itemPrice;
                $buyerName = $request->buyerName;
                $connectionChannel = $request->connectionChannel;
                $itemDescription = $request->itemDescription;
                $itemSerialNo = $request->itemSerialNo;
                $itemModelNo = $request->itemModelNo;
                $imeiFirst = $request->imeiFirst;
                $imeiLast = $request->imeiLast;
                $itemCurrency = $request->currency;

                if($request->theImages) {
                    $paths = $request->theImages['paths'];
                }
                        
                $item = Item::create(
                    [
                        'item_name' => $itemName,
                        'amount' => $itemPrice,
                        'buyer_name' => $buyerName,
                        'seller_id' => auth()->user()->id,
                        'seller_email' => auth()->user()->email,
                        'seller_country' => auth()->user()->country,
                        'seller_phone' => auth()->user()->phone,
                        'seller_currency' => $itemCurrency,
                        'seller_flag' => auth()->user()->flag,
                        'connection_channel' => $connectionChannel,
                        'description' => $itemDescription,
                        'cover_photo' => $request->theImages ? $paths[0] : '',
                        'serial_no' => $itemSerialNo,
                        'model_no' => $itemModelNo,
                        'imei_first' => $imeiFirst,
                        'imei_last' => $imeiLast
                    ]            
                );

                if($request->theImages && count($paths) > 0) {
                    $imageCount = count($paths);
                    for($i=0; $i < $imageCount; $i++) {
                        $photos = Image::create(
                            [
                                'item_id' => $item->id,
                                'image_path' => $paths[$i]
                            ]
                        );
                    }
                }
                $searchId = $item->item_name.'/'.$item->id.'/'.$item->buyer_name;
                $searchId = str_replace(' ', '', $searchId);

                $searchString = SearchId::create(
                    [
                    'search_string' => $searchId,
                    'item_id' => $item->id,
                    'buyer_name' => $buyerName,
                    'seller_id' => auth()->user()->id,
                    'seller_email' => auth()->user()->email
                    ]
                );

                $params = [
                    'searchId' => $searchId,
                    'email' => auth()->user()->email
                ];

                $this->dispatchSearchId($params);
                
            }else {
                throw new Exception('User email is not verified. Please verify your email or resend verification link from your profile page');
            }          
            
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }  
        
        return response()->json(['searchId' => $searchId]);
    }
   

    /**
     * Store images.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $paths = [];
        
        try {
            $user = User::where('id', auth()->user()->id)->first();
            
            if(!is_null($user->email_verified_at)) {
                foreach($request['files'] as $file) {
                    $path = $file->store(
                        'my-items', 's3'
                    );

                    array_push($paths, $path);
                }
            } else {
                throw new Exception('User email is not verified. Please verify your email or resend verification link from your profile page');
            }          
            
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['paths' => $paths]);

    }

    //Check if email is verified or not
    public function checkEmailVerification(Request $request)
    {        
               
        try {
            $user = User::where('id', auth()->user()->id)->first();
            
            if(!is_null($user->email_verified_at)) {
                return response()->json(['message' => 'Email is verified']);
            } else {
                throw new Exception('User email is not verified. Please verify your email or resend verification link from your profile page');
            }          
            
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }       

    }


    /**
     * Dispatch verification email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function dispatchSearchId($params)
    {       
        SendSearchIdJob::dispatch($params)->delay(now()->addSeconds(5));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {       
        // \Log::info($request->all());
        $item = Item::findOrFail($id);
        $itemImages = Item::findOrFail($id)->image;
                
        return response([
            'data' => [
              'item' => new ItemResource($item),
              'itemImages' => $itemImages
            ]
        ], Response::HTTP_OK);
    }

    public function searchItem(Request $request)
    {           
        $searchId = str_replace(' ', '', $request->id);  

        try {
            $checkSearch = SearchId::where('search_string', $searchId)->count();
            
            if($checkSearch > 0) {
                $explodeSearchId = explode("/","$searchId");

                $id = $explodeSearchId[1];

                $items = SearchId::findOrFail($id)->item;          
                
                return response(['data' => $items]);
            } else {
        
                throw new Exception('Item was not found. Please check item ID and try again');
            }          
            
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }     
        
    }

    public function CreatePayIntent(Request $request)
    {
        
        try {
            $user = User::where('id', auth()->user()->id)->first();
            
            if(!is_null($user->email_verified_at)) {
                $itemName = $request->itemName;
                $itemPrice = $request->itemPrice;
                $buyerName = $request->buyerName;
                $connectionChannel = $request->connectionChannel;
                $itemDescription = $request->itemDescription;
                $itemSerialNo = $request->itemSerialNo;
                $itemModelNo = $request->itemModelNo;
                $imeiFirst = $request->imeiFirst;
                $imeiLast = $request->imeiLast;
                $itemCurrency = strtolower($request->currency);

                $itemPrice = $itemPrice + ($itemPrice * 0.05);
                                
                \Stripe\Stripe::setApiKey(config('app.stripekey'));
                // \Log::info(+($itemPrice * 100));
                $intent = \Stripe\PaymentIntent::create([
                    'amount' => round($itemPrice * 100),
                    'currency' => $itemCurrency,            
                    'description' => '('.$itemName.')'.' '.$itemDescription            
                ]);
                
                return response(['intent' => $intent]);
            } else {
                throw new Exception('User email is not verified. Please verify your email or resend verification link from your profile page');
            }          
            
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }       
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        \Log::info($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {       
        $item = Item::find($request->id);

        $item->delete();
        // Storage::disk('s3')->delete('folder_path/file_name.jpg');
        if(auth()->user()->status > 2){
                        
            $items = Item::orderBy('id', 'desc')->paginate(2);
            
            
        }else{
            
            $items = Item::where('seller_id', auth()->user()->id)->orderBy('id', 'desc')->paginate(10);
        }
        
        return response()->json(['data' => $items]);
    }
}
