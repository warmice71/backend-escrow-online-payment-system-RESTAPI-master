<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mailgun\Mailgun;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Handle email queue
       
        app('view')->addNamespace('mail', resource_path('views') . '/mail');
        $html = view('Email.accountSignup')->with([
            'token'  => $this->params['token'], 
            'email' => $this->params['email']
        ])->render();
        
        $mg = Mailgun::create(config('app.mailgunsecret'), config('app.mailguneuurl'));
        
        $mg->messages()->send(config('app.domainurl'), [
        'from'    => 'no-reply@bridgepay.com',
        'to'      => $this->params['email'],
        'subject' => 'Bridgepay email verification',
        'html'    => $html
        ]);
    }
}
