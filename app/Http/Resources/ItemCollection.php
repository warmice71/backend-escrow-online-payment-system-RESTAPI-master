<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return parent::toArray($request);
    }
    // public function toArray($request)
    // {
    //     return [
    //         'id' => $this->id,
    //         'itemName' => $this->item_name,
    //         'itemPrice' => $this->amount,
    //         'coverPhoto' => $this->theImages,
    //         'buyerName' => $this->buyer_name,
    //         'connectionChannel' => $this->connection_channel,
    //         'itemDescription' => $this->description,
    //         'itemSerialNo' => $this->serial_no,
    //         'itemModelNo' => $this->model_no,
    //         'imeiFirst' => $this->imei_first,
    //         'imeiLast' => $this->imei_last,
    //         'created' => $this->created_at->diffForHumans(),
    //         'link' => [
    //             'first_page_url' => $this->first_page_url,
    //             'last_page' => $this->last_page
    //         ]        
    //     ];
    // }
}

