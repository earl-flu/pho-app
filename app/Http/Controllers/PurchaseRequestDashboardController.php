<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\PurchaseRequest;
use App\Models\RequestedItem;
use Illuminate\Http\Request;

class PurchaseRequestDashboardController extends Controller
{
    public function index()
    {
        /**
         * 1. Total price per Item - Bar Chart
         * 2. Total price per category - filter by month - approved. - bar chart
         * 3. Total qty per item in every category e.g. ICT Equipment > Computer(30)
         * 4. # of pending PR budget.
         */

        // Get requested Items
        $items = RequestedItem::all()->toArray();
        dd($items);
        dd(RequestedItem::first()->toArray());

        $purchaseRequests = PurchaseRequest::with('items', 'budget')->get();

        $totalValue = $purchaseRequests->sum(function ($pr) {
            return $pr->items->sum('subtotal');
        });

        $statusCounts = $purchaseRequests->groupBy('status')
            ->map(function ($group) {
                return $group->count();
            });

        $monthlyTotals = $purchaseRequests
            ->groupBy(function ($pr) {
                return $pr->created_at->format('M');
            })
            ->map(function ($group) {
                return $group->sum(function ($pr) {
                    return $pr->items->sum('subtotal');
                });
            });

        $topBudgets = Budget::withSum('purchaseRequests as total_value', 'items.subtotal')
            ->orderByDesc('total_value')
            ->take(5)
            ->get();

        $recentRequests = PurchaseRequest::with('budget')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('PurchaseRequests/Dashboard', [
            'purchaseRequests' => $purchaseRequests,
            'totalValue' => $totalValue,
            'statusCounts' => $statusCounts,
            'monthlyTotals' => $monthlyTotals,
            'topBudgets' => $topBudgets,
            'recentRequests' => $recentRequests,
        ]);
    }
}
