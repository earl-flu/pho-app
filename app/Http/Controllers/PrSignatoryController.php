<?php

namespace App\Http\Controllers;

use App\Models\PrSignatory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrSignatoryController extends Controller
{
    public function index()
    {
        $prSignatory = PrSignatory::first();
        // dd($prSignatory);
        return Inertia::render('PrSignatories/Index', ['prSignatory' => $prSignatory]);
    }

    public function create()
    {
        return Inertia::render('PrSignatories/Create', []);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'requested_by' => 'required|string|max:255',
            'cash_availability' => 'required|string|max:255',
            'approved_by' => 'required|string|max:255',
            'requested_by_position' => 'required|string|max:255',
            'cash_availability_position' => 'required|string|max:255',
            'approved_by_position' => 'required|string|max:255',
        ]);

        // Check if a record already exists
        if (PrSignatory::exists()) {
            return redirect()->back()->withErrors(['saving' => 'Only one record is allowed'])->withInput();
        }

        // Create a new PrSignatory record
        PrSignatory::create($validated);

        return redirect()->route('pr-signatories.index')->with('success', 'PR signatories created successfully.');
    }

    public function edit(PrSignatory $prSignatory)
    {
        return Inertia::render('PrSignatories/Edit', [
            'prSignatory' => $prSignatory,
        ]);
    }

    public function update(Request $request, PrSignatory $prSignatory)
    {
        $validated = $request->validate([
            'requested_by' => 'required|string|max:255',
            'cash_availability' => 'required|string|max:255',
            'approved_by' => 'required|string|max:255',
            'requested_by_position' => 'required|string|max:255',
            'cash_availability_position' => 'required|string|max:255',
            'approved_by_position' => 'required|string|max:255',
        ]);

        $prSignatory->update($validated);

        return redirect()->route('pr-signatories.index')->with('success', 'PR signatory updated successfully.');
    }
}
