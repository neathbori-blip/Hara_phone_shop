<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request, string $lang)
    {
        $brands = Brand::withCount([
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

        return view('brands.index', ['brands' => $brands]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:25|unique:brands,name',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->save();

        return redirect()->route('brand.index', withLang());
    }

    public function show(Brand $brand)
    {
        //
    }

    public function edit(Brand $brand)
    {
        //
    }

    public function update(Request $request, Brand $brand)
    {
        $messages = [
            'required' => 'This field can not be blanked',
            'unique' => 'This model type is exists'
        ];

        $request->validate([
            'name' => 'required|unique:brands'
        ], $messages);

        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->save();

        return redirect()->route('brand.index', withLang());
    }

    public function destroy(string $lang, $id)
    {
    $brand = Brand::findOrFail($id);
    $brand->delete();

    return redirect()->route('brand.index', withLang());
    }
}