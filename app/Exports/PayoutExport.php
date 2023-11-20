<?php

namespace App\Exports;

use App\PayOut;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayoutExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {        
        return PayOut::select('seller_email', 'item_price', 'currency', 'payment_id')->get();
    }
}
