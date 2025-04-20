<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cart;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auth = Auth::user();
        $items = Item::where('user_id', $auth->id)->get();
        return Inertia::render('Items/Index', ['items' => $items]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::where('user_id', Auth::id())->get();
        return Inertia::render('Items/Create', ['items' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|between:0,99999999.99',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'max' => 'required|integer|min:1',
            'file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $filePath = $request->file('file') ? $request->file('file')->store('files', 'public') : null;
        $auth = Auth::user();

        Item::create([
            'item_id' => $request->item_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'date' => $request->date,
            'max' => $request->max,
            'file_path' => $filePath,
            'user_id' => $auth->id,
        ]);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return Inertia::render('Items/Show', ['item' => $item]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $items = Item::where('user_id', Auth::id())->where('id', '!=', $item->id)->get();
        return Inertia::render('Items/Edit', ['items' => $items, 'item' => $item]);
    }

    public function source(Item $item)
    {
        $auth = Auth::user();
        $item = Item::where('user_id', $auth->id)->where('id', $item->id)->first();
        $carts = Cart::with('user')->where('item_id', $item->id)->get();
        return Inertia::render('Items/Source', ['item' => $item, 'carts' => $carts]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'item_id' => '',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|between:0,99999999.99',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'max' => 'required|integer|min:1',
            'file_path' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle file upload
        if ($request->file('file')) {
            if ($item->file_path) {
                Storage::disk('public')->delete($item->file_path); // Delete old file
            }
            $filePath = $request->file('file')->store('files', 'public'); // Save new file
        } else {
            $filePath = $item->file_path; // Retain old file if no new file is uploaded
        }

        // Update the item
        $item->update([
            'item_id' => $request->item_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'date' => $request->date,
            'max' => $request->max,
            'file_path' => $filePath,
        ]);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        if ($item->file_path) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}