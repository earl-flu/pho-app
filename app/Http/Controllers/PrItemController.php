<?php

namespace App\Http\Controllers;

use App\Models\PrCategory;
use App\Models\PrItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;

class PrItemController extends Controller
{
    public function index()
    {
        $items = PrItem::with('details')->get();
        return response()->json($items);
    }

    public function storeItem(Request $request, $categoryId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PrItem::create([
            'category_id' => $categoryId,
            'name' => $validated['name'],
        ]);

        return redirect()->route('categories.items.index', $categoryId)->with('success', 'Item created successfully.');
    }

    public function updateItem(Request $request, $categoryId, $itemId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:pr_items,name,' . $itemId . ',id,category_id,' . $categoryId,
        ]);

        $item = PrItem::findOrFail($itemId);
        $item->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('categories.items.index', $categoryId)->with('success', 'Item updated successfully.');
    }


    public function getItemsByCategory($categoryId)
    {
        $category = PrCategory::findOrFail($categoryId);

        $items = $category->items()
            ->when(FacadesRequest::input('name'), function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->paginate(10)
            ->withQueryString()
            ->onEachSide(0);

        return inertia('PrItems/Index', [
            'category' => $category,
            'items' => $items,
            'filters' => FacadesRequest::only('name')
        ]);
    }
}
