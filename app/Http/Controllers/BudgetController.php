<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\BudgetChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::orderBy('name')
            ->when(FacadesRequest::input('name'), function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->paginate(5)
            ->withQueryString()
            ->onEachSide(0);

        return Inertia::render('Budgets/Index', [
            'budgets' => $budgets,
            'filters' => FacadesRequest::only('name')
        ]);
    }

    public function create()
    {
        return Inertia::render('Budgets/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        Budget::create($request->all());

        return redirect()->route('budgets.index');
    }

    public function updateAmount(UpdateBudgetRequest $request, Budget $budget): RedirectResponse
    {
        $newAmount = $this->calculateNewAmount($budget, $request->operation, $request->amount);

        if ($newAmount < 0) {
            return $this->handleInsufficientBudget();
        }

        DB::transaction(function () use ($budget, $newAmount, $request) {
            $budget->update(['amount' => $newAmount]);

            BudgetChange::create([
                'budget_id' => $budget->id,
                'amount' => $request->amount,
                'operation' => $request->operation,
                'remarks' => $request->remarks,
                'user_id' => Auth::id(),
            ]);
        });

        return $this->redirectWithSuccess();
    }

    public function moveBudget(MoveBudgetRequest $request, Budget $sourceBudget): RedirectResponse
    {
        $amount = $request->amount;
        $targetBudget = Budget::findOrFail($request->target_budget_id);

        if ($sourceBudget->amount < $amount) {
            return $this->handleInsufficientBudget();
        }

        DB::transaction(function () use ($sourceBudget, $targetBudget, $amount, $request) {
            $sourceBudget->amount -= $amount;
            $sourceBudget->save();

            $targetBudget->amount += $amount;
            $targetBudget->save();

            $userId = Auth::id();

            BudgetChange::create([
                'budget_id' => $sourceBudget->id,
                'amount' => $amount,
                'operation' => Budget::OPERATION_SUBTRACT,
                'remarks' => "Moved to Budget ID: {$targetBudget->id} - {$request->remarks}",
                'user_id' => $userId,
            ]);

            BudgetChange::create([
                'budget_id' => $targetBudget->id,
                'amount' => $amount,
                'operation' => Budget::OPERATION_ADD,
                'remarks' => "Received from Budget ID: {$sourceBudget->id} - {$request->remarks}",
                'user_id' => $userId,
            ]);
        });

        return $this->redirectWithSuccess('Budget moved successfully.');
    }

    private function calculateNewAmount(Budget $budget, string $operation, float $amount): float
    {
        return $operation === Budget::OPERATION_ADD
            ? $budget->amount + $amount
            : $budget->amount - $amount;
    }

    private function handleInsufficientBudget(): RedirectResponse
    {
        return back()->withErrors([
            'amount' => 'Insufficient budget to subtract the specified amount.'
        ]);
    }

    private function redirectWithSuccess(): RedirectResponse
    {
        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    public function editAmount(Budget $budget)
    {
        return Inertia::render('Budgets/EditAmount', compact('budget'));
    }

    // public function update(Request $request, Budget $budget)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'amount' => 'required|numeric',
    //     ]);

    //     $budget->update($request->all());

    //     return redirect()->route('budgets.index');
    // }

    // public function destroy(Budget $budget)
    // {
    //     $budget->delete();
    //     return redirect()->route('budgets.index');
    // }
}
