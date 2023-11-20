<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mailgun\Mailgun;

class PaymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        //
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Handle invoice emailing to seller      
        app('view')->addNamespace('mail', resource_path('views') . '/mail');
                 
        $html = view('Pdf.invoice')->with([
            'paymentId' => $this->params['paymentId'],
            'itemname'  => $this->params['itemname'], 
            'itemPrice' => $this->params['itemPrice'],            
            'amountReceived' => $this->params['amountReceived'],
            'itemDescription' => $this->params['itemDescription'],
            'paymentOption' => $this->params['paymentOption'],
            'sellerEmail' => $this->params['sellerEmail'],
            'buyerEmail' => $this->params['buyerEmail'],
            'currency' => $this->params['currency'],
            'seller' => $this->params['seller'],
            'paymentDate' => $this->params['paymentDate']            
        ])->render();
                
        $mg = Mailgun::create(config('app.mailgunsecret'), config('app.mailguneuurl'));
        
        $mg->messages()->send(config('app.domainurl'), [
        'from'    => 'no-reply@bridgepay.com',
        'to'      => $this->params['sellerEmail'],
        'subject' => 'Payment Notification',
        'html'    => $html
        ]);
    }
}
