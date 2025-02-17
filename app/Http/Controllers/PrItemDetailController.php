<?php

namespace App\Http\Controllers;

use App\Models\PrItem;
use App\Models\PrItemDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;

class PrItemDetailController extends Controller
{
    public function store(Request $request, $categoryId, $itemId)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'uom' => 'required|string|max:50',
            'website_link' => 'nullable|url|max:255',
            'original_price' => 'required|numeric|min:0',
            'markup_price' => 'required|numeric|min:0',
        ]);

        PrItemDetail::create([
            'item_id' => $itemId,
            'description' => $validated['description'],
            'uom' => $validated['uom'],
            'website_link' => $validated['website_link'],
            'original_price' => $validated['original_price'],
            'markup_price' => $validated['markup_price'],
        ]);

        return redirect()->route(
            'items.details.index',
            [$categoryId, $itemId]
        )->with('success', 'Item detail added successfully.');
    }


    public function getDetailsByItem($categoryId, $itemId)
    {
        $item = PrItem::with('category')->findOrFail($itemId);
        
        if ($item->category_id !== (int)$categoryId) {
            return abort(404);
        }

        $itemDetails = PrItemDetail::where('item_id', $itemId)
            ->when(FacadesRequest::input('description'), function ($query, $description) {
                $query->where('description', 'like', "%{$description}%");
            })
            ->paginate(10)
            ->withQueryString()
            ->onEachSide(0);

        return inertia('PrItemDetails/Index', [
            'item' => $item,
            'itemDetails' => $itemDetails,
            'categoryId' => $categoryId,
            'filters' => FacadesRequest::only('description')
        ]);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'uom' => 'required|string|max:50',
            'website_link' => 'nullable|url',
            'original_price' => 'nullable|numeric',
            'markup_price' => 'nullable|numeric',
        ]);

        $itemDetail = PrItemDetail::findOrFail($id);
        $itemDetail->update($validated);

        // return redirect()->route('items.details.index', [
        //     'categoryId' => $itemDetail->category_id,
        //     'itemId' => $itemDetail->item_id,
        // ])->with('success', 'PR Item Detail Updated Successfully');

        return back()->withInput();
    }
}
