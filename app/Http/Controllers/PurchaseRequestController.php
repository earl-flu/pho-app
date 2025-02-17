<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetChange;
use App\Models\PrItemDetail;
use App\Models\PrSignatory;
use App\Models\PurchaseRequest;
use App\Models\RequestedItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Inertia\Inertia;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        // dd(FacadesRequest::input('receivedOnly'));
        $purchaseRequests = PurchaseRequest::with('budget', 'items')
            ->when(FacadesRequest::input('name'), function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when(FacadesRequest::input('status') && FacadesRequest::input('status') != 'all', function ($query, $status) {
                $query->where('status', FacadesRequest::input('status'));
            })
            ->when(FacadesRequest::input('receivedOnly') === 'true', function ($query) {
                $query->where('is_received', 1);
            })
            ->paginate(5)
            ->withQueryString()
            ->onEachSide(0);
        // dd($purchaseRequests->toArray());
        $purchaseRequests->through(function ($pr) {
            $pr->budgetName = $pr->budget->name;
            $pr->totalAmount = collect($pr->items)->sum('subtotal');
            return $pr;
        });

        return Inertia::render(
            'PurchaseRequests/Index',
            [
                'purchaseRequests' => $purchaseRequests,
                'filters' => FacadesRequest::only('name')
            ]
        );
    }

    public function create()
    {
        $budgets = Budget::all();
        $prSignatory = PrSignatory::first();

        $items = PrItemDetail::with('item')
            ->when(FacadesRequest::input('searchItemName'), function ($query, $name) {
                $query->whereHas('item', function ($query) use ($name) {
                    $query->where('name', 'like', "%{$name}%");
                });
            })
            ->paginate(5)
            ->withQueryString()
            ->onEachSide(0);

        $items->through(function ($itemDetail) {
            $itemDetail->itemName = $itemDetail->item->name ?? null;
            return $itemDetail;
        });

        return Inertia::render(
            'PurchaseRequests/Create',
            [
                'budgets' => $budgets,
                'items' => $items,
                'prSignatory' => $prSignatory,
                'filters' => FacadesRequest::only('searchItemName')
            ]
        );
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'remarks' => 'nullable|string',
            'name' => 'required|string',
            'status' => 'required|string|in:pending,approved,rejected',
            'number' => 'nullable|string',
            'date_approved' => [
                'nullable',
                'required_if:status,approved',
                'date',
            ],
            'is_received' => 'required|boolean',
            'date_received' => [
                'nullable',
                'required_if:is_received,true',
                'date',
            ],
            'requested_by' => 'required|string',
            'requested_by_position' => 'required|string',
            'cash_availability' => 'required|string',
            'cash_availability_position' => 'required|string',
            'approved_by' => 'required|string',
            'approved_by_position' => 'required|string',
            'items' => 'required|array',
            'items.*.item_detail_id' => 'required|exists:pr_item_details,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.uom' => 'required|string',
            'items.*.markup_price' => 'required|numeric|min:0',
        ]);

        // Process date_approved
        $validatedData['date_approved'] = $this->processDate($validatedData['date_approved'], $validatedData['status'] === 'approved');

        // Process date_received
        $validatedData['date_received'] = $this->processDate($validatedData['date_received'], $validatedData['is_received']);


        $totalValue = collect($validatedData['items'])->sum('subtotal');
        $budget = Budget::findOrFail($validatedData['budget_id']);

        if ($validatedData['status'] === 'approved' && $budget->amount < $totalValue) {
            return redirect()->back()->withErrors(['budget' => 'Insufficient budget for this purchase request.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validatedData, $totalValue, $budget) {
                $purchaseRequest = PurchaseRequest::create([
                    'name' => $validatedData['name'],
                    'budget_id' => $validatedData['budget_id'],
                    'status' => $validatedData['status'],
                    'remarks' => $validatedData['remarks'],
                    'number' => $validatedData['number'],
                    'date_approved' => $validatedData['date_approved'],
                    'is_received' => $validatedData['is_received'],
                    'date_received' => $validatedData['date_received'],
                    'requested_by' => $validatedData['requested_by'],
                    'requested_by_position' => $validatedData['requested_by_position'],
                    'cash_availability' => $validatedData['cash_availability'],
                    'cash_availability_position' => $validatedData['cash_availability_position'],
                    'approved_by' => $validatedData['approved_by'],
                    'approved_by_position' => $validatedData['approved_by_position'],
                ]);

                foreach ($validatedData['items'] as $item) {
                    RequestedItem::create([
                        'purchase_request_id' => $purchaseRequest->id,
                        'item_detail_id' => $item['item_detail_id'],
                        'markup_price' => $item['markup_price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                        'uom' => $item['uom']
                    ]);
                }

                if ($validatedData['status'] === 'approved') {
                    $budget->amount -= $totalValue;
                    $budget->save();

                    // Log the budget change
                    BudgetChange::create([
                        'budget_id' => $budget->id,
                        'amount' => $totalValue,
                        'operation' => 'subtract',
                        'remarks' => "Approved Purchase Request: {$purchaseRequest->number}",
                        'user_id' => Auth::id(),
                    ]);
                }
            });

            return redirect()->route('purchase-requests.index')->with('success', 'Purchase Request created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred while creating the purchase request.'])->withInput();
        }
    }

    private function processDate($date, $condition)
    {
        if ($date && $condition) {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        }
        return null;
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('items.itemDetail.item');

        $items = PrItemDetail::with('item')
            ->when(FacadesRequest::input('searchItemName'), function ($query, $name) {
                $query->whereHas('item', function ($query) use ($name) {
                    $query->where('name', 'like', "%{$name}%");
                });
            })
            ->paginate(5)
            ->withQueryString()
            ->onEachSide(0);

        // Add item name to items
        $items->through(function ($itemDetail) {
            $itemDetail->itemName = $itemDetail->item->name ?? null;
            return $itemDetail;
        });
        
        return Inertia::render('PurchaseRequests/Edit', [
            'purchaseRequest' => $purchaseRequest,
            'items' => $items,
            'budgets' => Budget::all(),
            'filters' => request()->only('searchItemName'),
        ]);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validatedData = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'remarks' => 'nullable|string',
            'name' => 'required|string',
            'status' => 'required|string|in:pending,approved,rejected',
            'number' => 'nullable|string',
            'date_approved' => [
                'nullable',
                'required_if:status,approved',
                'date',
            ],
            'is_received' => 'required|boolean',
            'date_received' => [
                'nullable',
                'required_if:is_received,true',
                'date',
            ],
            'requested_by' => 'required|string',
            'requested_by_position' => 'required|string',
            'cash_availability' => 'required|string',
            'cash_availability_position' => 'required|string',
            'approved_by' => 'required|string',
            'approved_by_position' => 'required|string',
            'items' => 'required|array',
            'items.*.item_detail_id' => 'required|exists:pr_item_details,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.uom' => 'required|string',
            'items.*.markup_price' => 'required|numeric|min:0',
        ]);

        // Process date_approved
        $validatedData['date_approved'] = $this->processDate($validatedData['date_approved'], $validatedData['status'] === 'approved');

        // Process date_received
        $validatedData['date_received'] = $this->processDate($validatedData['date_received'], $validatedData['is_received']);

        $newTotalValue = collect($validatedData['items'])->sum('subtotal');
        $newBudget = Budget::findOrFail($validatedData['budget_id']);

        if ($validatedData['status'] === 'approved' && $newBudget->amount < $newTotalValue) {
            return redirect()->back()->withErrors(['budget' => 'Insufficient budget for this purchase request.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validatedData, $purchaseRequest, $newTotalValue, $newBudget) {
                $oldStatus = $purchaseRequest->status;
                $oldBudgetId = $purchaseRequest->budget_id;
                $oldTotalValue = $purchaseRequest->items->sum('subtotal');

                // Update purchase request
                $purchaseRequest->update([
                    'name' => $validatedData['name'],
                    'budget_id' => $validatedData['budget_id'],
                    'status' => $validatedData['status'],
                    'remarks' => $validatedData['remarks'],
                    'number' => $validatedData['number'],
                    'number' => $validatedData['number'],
                    'date_approved' => $validatedData['date_approved'],
                    'is_received' => $validatedData['is_received'],
                    'date_received' => $validatedData['date_received'],
                    'requested_by' => $validatedData['requested_by'],
                    'requested_by_position' => $validatedData['requested_by_position'],
                    'cash_availability' => $validatedData['cash_availability'],
                    'cash_availability_position' => $validatedData['cash_availability_position'],
                    'approved_by' => $validatedData['approved_by'],
                    'approved_by_position' => $validatedData['approved_by_position'],
                ]);

                // Update requested items
                $purchaseRequest->items()->delete(); // Remove old items
                foreach ($validatedData['items'] as $item) {
                    RequestedItem::create([
                        'purchase_request_id' => $purchaseRequest->id,
                        'item_detail_id' => $item['item_detail_id'],
                        'markup_price' => $item['markup_price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                        'uom' => $item['uom']
                    ]);
                }

                // Handle budget adjustments
                if ($oldStatus === 'approved') {
                    $oldBudget = Budget::findOrFail($oldBudgetId);
                    $oldBudget->amount += $oldTotalValue;
                    $oldBudget->save();

                    // Log the budget change
                    $this->logBudgetChange($oldBudget->id, $oldTotalValue, 'add', "Reversed Approved Purchase Request: {$purchaseRequest->number}");
                }

                if ($validatedData['status'] === 'approved') {
                    $newBudget->amount -= $newTotalValue;
                    $newBudget->save();

                    // Log the budget change
                    $this->logBudgetChange($newBudget->id, $newTotalValue, 'subtract', "Approved Purchase Request: {$purchaseRequest->number}");
                }
            });

            return redirect()->route('purchase-requests.index')->with('success', 'Purchase Request updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating purchase request: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating the purchase request.'])->withInput();
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        // dd($purchaseRequest->toArray());
        // dd($purchaseRequest->items->toArray());
        $items = $purchaseRequest->items;
        $totalPrice = $items->sum('subtotal');
        return Inertia::render(
            'PurchaseRequests/Show',
            [
                'purchaseRequest' => $purchaseRequest,
                'items' => $items,
                'totalPrice' => $totalPrice,
            ]
        );
    }

    private function logBudgetChange($budgetId, $amount, $operation, $remarks)
    {
        BudgetChange::create([
            'budget_id' => $budgetId,
            'amount' => $amount,
            'operation' => $operation,
            'remarks' => $remarks,
            'user_id' => Auth::id(),
        ]);
    }
}
