<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return Inertia::render('Items/Index', compact('items'));
    }

    public function create()
    {
        return Inertia::render('Items/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|array',
            'details.*.description' => 'required|string|max:255',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        $item = Item::create(['name' => $request->name]);

        foreach ($request->details as $detail) {
            $item->details()->create($detail);
        }

        return response()->json($item->load('details'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'price' => 'required|numeric',
    //     ]);

    //     Item::create($request->all());

    //     return redirect()->route('items.index');
    // }

    public function edit(Item $item)
    {
        return Inertia::render('Items/Edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index');
    }

    public function getItems()
    {
        $items = Item::all();
        return response()->json($items);
    }
}
