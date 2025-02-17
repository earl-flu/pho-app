<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;

class PrintController extends Controller
{
    public function printPurchaseRequest(PurchaseRequest $purchaseRequest)
    {
        $items = $purchaseRequest->items->toArray();
        $total = $purchaseRequest->items->sum('subtotal');

        $pdf = PDF::loadView('prints.purchase_request', 
        compact('purchaseRequest', 'items', 'total'));

        return $pdf->stream('purchase_request.pdf');
    }
}
