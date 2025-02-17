<?php

namespace App\Http\Controllers;

use App\Models\PrCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as FacadesRequest;

class PrCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prCategories = PrCategory::orderBy('name')
            ->when(FacadesRequest::input('name'), function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->paginate(5)
            ->withQueryString()
            ->onEachSide(0);

        return Inertia::render('PrCategories/Index', [
            'prCategories' => $prCategories,
            'filters' => FacadesRequest::only('name')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:pr_categories,name',
        ]);

        PrCategory::create($validated);
        return redirect()->route('categories.index');
    }
}
