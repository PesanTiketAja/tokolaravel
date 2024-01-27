<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductApiController extends Controller
{
    public function index()
    {
        $limit = request()->input('limit', 10);
        $items = Product::paginate($limit);
        $data = $items->items();
        $meta = [
            'currentPage' => $items->currentPage(),
            'perPage' => $items->perPage(),
            'total' => $items->total(),
        ];
        if ($items->hasMorePages()) {
            $meta['next_page_url'] = url($items->nextPageUrl());
        }
        if ($items->currentPage() > 2) {
            $meta['prev_page_url'] = url($items->previousPageUrl());
        }
        if ($items->lastPage() > 1) {
            $meta['last_page_url'] = url($items->url($items->lastPage()));
        }
        return response()->json(['data' => $data, 'meta' => $meta], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'description' => 'required|string',
        ]);

        $item = Product::create($validatedData);
        return response()->json(['data' => $item], 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'product_name' => 'string',
            'price' => 'numeric',
            'category' => 'string',
            'description' => 'string',
        ]);

        $item = Product::findOrFail($id);
        $item->update($validatedData);
        return response()->json(['data' => $item], 200);
    }
    public function show($id)
    {
        $item = Product::findOrFail($id);
        return response()->json(['data' => $item], 200);
    }
    public function destroy($id)
    {
        $item = Product::findOrFail($id);
        $item->delete();
        return response()->json([
            'data' => $item
        ], 200);
    }
}
