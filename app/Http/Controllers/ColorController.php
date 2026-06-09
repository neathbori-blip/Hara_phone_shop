<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request, string $lang)
    {
        $colors = Color::withCount([
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

        return view('colors.index', ['colors' => $colors]);
    }

    public function store(Request $request, string $lang)
    {
        $request->validate([
            'name' => 'required|string|max:25|unique:colors,name',
        ]);

        $color = new Color();
        $color->name = $request->name;
        $color->save();

        return redirect()->route('color.index', withLang());
    }

    public function update(Request $request, string $lang)
    {
        $request->validate([
            'name' => 'required|unique:colors,name,' . $request->id,
        ], [
            'required' => 'This field can not be blank',
            'unique'   => 'This color already exists',
        ]);

        $color = Color::findOrFail($request->id);
        $color->name = $request->name;
        $color->save();

        return redirect()->route('color.index', withLang());
    }

    public function destroy(string $lang, $id)
    {
        $color = Color::findOrFail($id);
        $color->delete();

        return redirect()->route('color.index', withLang());
    }
}