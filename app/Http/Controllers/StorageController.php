<?php

namespace App\Http\Controllers;

use App\Models\Storage as StorageModel;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        $storages = StorageModel::withCount([
            'products',
            'products as products_status_instock_count' => function ($query) {
                $query->where('status', 1);
            },
            'products as products_status_sold_count' => function ($query) {
                $query->where('status', 2);
            },
            'products as products_status_loan_count' => function ($query) {
                $query->where('status', 3);
            },
        ])->get();

        return view('storages.index', ['storages' => $storages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:25|unique:storages,name',
        ]);

        $storage = new StorageModel();
        $storage->name = $request->name;
        $storage->save();

        return redirect()->route('storage.index', withLang());
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $storage = StorageModel::findOrFail($id);

        $exists = StorageModel::where('name', $request->name)
                              ->where('id', '!=', $id)
                              ->exists();

        if ($exists) {
            return response()->json(['isUnique' => true]);
        }

        $storage->name = $request->name;
        $storage->save();

        return response()->json(['isUnique' => false]);
    }

    public function destroy(string $lang, $id)
    {
        $storage = StorageModel::findOrFail($id);
        $storage->delete();

        return redirect()->route('storage.index', withLang());
    }
}