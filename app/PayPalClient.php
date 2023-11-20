<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use escrowCheckoutSdk\Core\escrowHttpClient;
use escrowCheckoutSdk\Core\SandboxEnvironment;

// ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');

class escrowClient extends Model
{
    /**
     * Returns escrow HTTP client instance with environment that has access
     * credentials context. Use this instance to invoke escrow APIs, provided the
     * credentials have access.
     */
    public static function client()
    {
        return new escrowHttpClient(self::environment());
    }

    /**
     * Set up and return escrow PHP SDK environment with escrow access credentials.
     * This sample uses SandboxEnvironment. In production, use LiveEnvironment.
     */
    public static function environment()
    {
        $clientId = config("app.escrowclientid");
        $clientSecret = config("app.escrowclientsecret");
        return new SandboxEnvironment($clientId, $clientSecret);
    }
}