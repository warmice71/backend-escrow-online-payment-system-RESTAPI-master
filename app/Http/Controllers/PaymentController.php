<?php

namespace App\Http\Controllers;

use App\Payment;
use App\PayOut;
use Illuminate\Http\Request;
use App\Stripe;
use App\escrowClient;
use App\Item;
use App\User;
use escrowCheckoutSdk\Orders\OrdersCreateRequest;
use escrowCheckoutSdk\Orders\OrdersGetRequest;
use escrowCheckoutSdk\Orders\OrdersCaptureRequest;
use App\Http\Resources\Payment as PaymentResource;
use App\Http\Resources\Payments as PaymentResourceCollection;
use App\Jobs\SendInvoiceJob;
use App\Jobs\PaymentNotificationJob;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use PDF;
use App\Exports\PayoutExport;
use Excel;


class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['export_pdf', 'exportData']);
    }

    /**
     * Display a listing of payments completed by the buyers, but not released to sellers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->status > 2) {

            $items = Payment::where('payout_initiated', false)->orderByDesc('id')->paginate(5);

        } else {

            $items = Payment::where([
                ['buyer_email', auth()->user()->email],
                ['payout_initiated', false]
            ])->orderByDesc('id')->paginate(5);
        }

        $pendingPayments = [];

        //Initialize Stripe and escrow
        \Stripe\Stripe::setApiKey(config('app.stripekey'));
        $client = escrowClient::client();

        foreach ($items as $item) {
            try {
                $event = null;


                if ($item->payment_option === 'stripe') {

                    //Retrieve confirmed stripe payments
                    $event = \Stripe\PaymentIntent::retrieve($item->intent_id);

                    if ($event->status !== 'succeeded' || (($item->amount_paid * 100) != $event->amount_received)) {

                        $item->payment_status = $event->status;
                        $item->currency = $event->currency;
                        $item->correct_payment = false;
                    } else {

                        $item->payment_status = $event->status;
                        $item->currency = $event->charges->data[0]->currency;
                        $item->correct_payment = true;
                    }

                } elseif ($item->payment_option === 'escrow') {

                    //Retrieve confirmed escrow payments
                    $request = new OrdersGetRequest($item->escrow_order_id);
                    $request->headers["prefer"] = "return=representation";
                    $response = $client->execute($request);
                    $answer = json_encode($response->result);

                    $answertwo = json_decode($answer, true);

                    if ($answertwo['status'] !== 'COMPLETED' || (($item->amount_paid) != $answertwo['purchase_units'][0]['items'][0]['unit_amount']['value'])) {

                        $item->payment_status = 'failed';
                        $item->currency = $answertwo['purchase_units'][0]['items'][0]['unit_amount']['currency_code'];
                        $item->correct_payment = false;
                    } else {

                        $item->payment_status = 'succeeded';
                        $item->currency = $answertwo['purchase_units'][0]['items'][0]['unit_amount']['currency_code'];

                        $item->correct_payment = true;
                    }

                }

                //Detach unnecessary data from response to be sent
                unset($item->intent_id);
                unset($item->escrow_order_id);
                unset($item->transaction_completed);
                unset($item->updated_at);
                unset($item->updbuyer_id);
                unset($item->seller_id);
                unset($item->buyer_id);
                unset($item->amount_received);
                unset($item->commission);

                $pendingPayments[] = $item;

            } catch (Exception $e) {
                return response()->json([
                    'errors' => 'There was an error retrieving payments'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return new PaymentResource($pendingPayments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeescrowOnApprove(Request $request)
    {
        $paymentOption = 'escrow';
        $itemId = $request->itemId;
        $itemName = $request->itemName;
        $amount = $request->amount;
        $realAmount = $request->realAmount;
        $currency = $request->currency;
        $escrowOrderId = $request->escrowOrderId;
        $itemPrice = $request->itemPrice;

        $commission = $request->commission;
        $buyerName = $request->buyer;
        $connectionChannel = $request->connectionChannel;
        $buyerEmail = $request->buyerEmail;
        $itemDescription = $request->itemDescription;
        $sellerId = $request->seller_id;
        $sellerEmail = $request->seller_email;
        $itemModelNo = $request->itemModelNo;
        $imeiFirst = $request->imeiFirst;
        $imeiLast = $request->imeiLast;


        $payment = Payment::create(
            [
                'item_id' => $itemId,
                'escrow_order_id' => $escrowOrderId,
                'payment_option' => $paymentOption,
                'currency' => $currency,
                'amount_paid' => $amount,
                'item_price' => $itemPrice,
                'amount_received' => $realAmount,
                'commission' => $commission,
                'buyer_name' => $buyerName,
                'buyer_email' => $buyerEmail,
                'seller_id' => $sellerId,
                'seller_email' => $sellerEmail,
                'buyer_id' => auth()->user()->id,
                'item_description' => $itemDescription
            ]
        );

        $hashedId = bcrypt($payment->id);

        $updatedPayment = Payment::where('id', $payment->id)->update(['hash_id' => $hashedId]);

        return response(['payment' => $payment]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStripePayment(Request $request)
    {
        $intentId = $request->intentId;
        $itemId = $request->itemId;
        $paymentOption = 'stripe';
        $realAmount = $request->realAmount;
        $currency = $request->currency;
        $itemPrice = $request->itemPrice;
        $amountReceived = +$realAmount - (+$itemPrice * 0.0145);
        $commission = $amountReceived - $itemPrice;
        $buyerName = $request->buyer;
        $buyerEmail = $request->buyerEmail;
        $itemDescription = $request->itemDescription;
        $sellerId = $request->sellerId;
        $sellerEmail = $request->sellerEmail;


        $payment = Payment::create(
            [
                'intent_id' => $intentId,
                'item_id' => $itemId,
                'payment_option' => $paymentOption,
                'currency' => $currency,
                'amount_paid' => $realAmount,
                'item_price' => $itemPrice,
                'amount_received' => $amountReceived,
                'commission' => $commission,
                'buyer_name' => $buyerName,
                'buyer_email' => $buyerEmail,
                'seller_id' => $sellerId,
                'seller_email' => $sellerEmail,
                'buyer_id' => auth()->user()->id,
                'item_description' => $itemDescription,
                'payment_completed' => true
            ]
        );

        $hashedId = bcrypt($payment->id);

        $updatedPayment = Payment::where('id', $payment->id)->update(['hash_id' => $hashedId]);

        $params = [
            'paymentId' => $hashedId,
            'itemname' => $payment->item->item_name,
            'itemPrice' => $payment->item_price,
            'amountReceived' => $payment->amount_received,
            'itemDescription' => $payment->item_description,
            'paymentOption' => $payment->payment_option,
            'sellerEmail' => $payment->seller_email,
            'buyerEmail' => $payment->buyer_email,
            'currency' => $payment->currency,
            'buyer' => true,
            'paymentDate' => $payment->created_at
        ];

        $paramsSeller = [
            'paymentId' => $hashedId,
            'itemname' => $payment->item->item_name,
            'itemPrice' => $payment->item_price,
            'amountReceived' => $payment->amount_received,
            'itemDescription' => $payment->item_description,
            'paymentOption' => $payment->payment_option,
            'sellerEmail' => $payment->seller_email,
            'buyerEmail' => $payment->buyer_email,
            'currency' => $payment->currency,
            'seller' => true,
            'paymentDate' => $payment->created_at
        ];

        $this->dispatchInvoice($params);
        $this->dispatchPaymentNotification($paramsSeller);

        return response(['payment' => $payment]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }


    //Update Payment table to show payment from buyer is completed    
    public function updateescrowOrder(Request $request)
    {
        $payment = Payment::where('escrow_order_id', $request->id)->first();
        $updatedPayment = Payment::where('escrow_order_id', $request->id)->update(['payment_completed' => true]);

        $params = [
            'itemname' => $payment->item->item_name,
            'paymentId' => $payment->hash_id,
            'itemPrice' => $payment->item_price,
            'amountReceived' => $payment->amount_received,
            'itemDescription' => $payment->item_description,
            'paymentOption' => $payment->payment_option,
            'sellerEmail' => $payment->seller_email,
            'buyerEmail' => $payment->buyer_email,
            'currency' => $payment->currency,
            'buyer' => true,
            'paymentDate' => $payment->created_at
        ];

        $paramsSeller = [
            'itemname' => $payment->item->item_name,
            'paymentId' => $payment->hash_id,
            'itemPrice' => $payment->item_price,
            'amountReceived' => $payment->amount_received,
            'itemDescription' => $payment->item_description,
            'paymentOption' => $payment->payment_option,
            'sellerEmail' => $payment->seller_email,
            'buyerEmail' => $payment->buyer_email,
            'currency' => $payment->currency,
            'seller' => true,
            'paymentDate' => $payment->created_at
        ];

        $this->dispatchInvoice($params);
        $this->dispatchPaymentNotification($paramsSeller);
        return response()->json(['message' => 'Payment has been completed']);

    }

    /**
     * Dispatch invoice email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function dispatchInvoice($params)
    {
        SendInvoiceJob::dispatch($params)->delay(now()->addSeconds(10));
    }

    /**
     * Dispatch payment notification email to seller.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function dispatchPaymentNotification($params)
    {
        PaymentNotificationJob::dispatch($params)->delay(now()->addSeconds(15));
    }

    //Search for a payment ID to confirm transaction. You can only confirm transaction if you are the seller
    public function searchPayment(Request $request)
    {
        $paymentId = $request->paymentId;
        $paymentId = strip_tags($paymentId);
        $paymentId = str_replace(' ', '', $paymentId);

        try {

            $payment = Payment::where([
                ['hash_id', $paymentId],
                ['seller_email', auth()->user()->email],
                ['payment_completed', true]
            ])->first();

            if (!is_null($payment)) {

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

    //Register a payment to be sent to seller
    public function sendSellerPayment(Request $request)
    {

        try {

            $payment = Payment::where([
                ['id', $request->id],
                ['payout_initiated', false]
            ])->first();

            if (!is_null($payment)) {

                $payout = PayOut::create(
                    [
                        'payment_id' => $request->id,
                        'currency' => strtoupper($payment->currency),
                        'item_price' => $payment->item_price,
                        'payment_method' => $payment->payment_option,
                        'seller_email' => $payment->seller_email,
                        'item_name' => $payment->item->item_name
                    ]
                );

                $updatedPayment = Payment::where('id', $request->id)->update(['payout_initiated' => true]);

                return response()->json(['message' => 'Payout processing has been initiated. Your funds will be available within 1 working day.']);

            } else {
                throw new Exception('This Transaction was not found');
            }


        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

    //Display invoice in PDF format
    public function export_pdf(Request $request)
    {
        $query = $request->query('paymentId');


        try {
            if (isset($query)) {

                $payment = Payment::where('hash_id', $query)->first();

                $params = [
                    'paymentId' => $payment->hash_id,
                    'itemname' => $payment->item->item_name,
                    'itemPrice' => $payment->item_price,
                    'amountReceived' => $payment->amount_received,
                    'itemDescription' => $payment->item_description,
                    'paymentOption' => $payment->payment_option,
                    'sellerEmail' => $payment->seller_email,
                    'buyerEmail' => $payment->buyer_email,
                    'currency' => $payment->currency,
                    'paymentDate' => $payment->created_at
                ];

                $pdf = PDF::loadView('Pdf.pdf_download', ['params' => $params])->setPaper('a4', 'portrait');

                return $pdf->stream();

            } else {
                throw new Exception('This Transaction was not found');
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
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    //Export payout data to CSV file
    public function exportData(Request $request)
    {
        $email = $request->query('email');
        $password = $request->query('password');

        try {
            if (isset($email) && isset($password)) {

                $user = User::where('email', $email)->first();

                if (!is_null($user)) {
                    if (password_verify($password, $user->password)) {
                        if ($user->status == 3) {
                            $date = date("Y-m-d");

                            return Excel::download(new PayoutExport, $date . 'payout.xlsx');
                        } else {
                            throw new Exception('Only Admins can access this resource');
                        }
                    } else {
                        throw new Exception('Email and password do nit match');
                    }
                } else {
                    throw new Exception('This user was not found');
                }
            } else {
                throw new Exception('You need to enter both email & password to access this resource');
            }

        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
