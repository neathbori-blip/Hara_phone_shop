<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Brand;
use Illuminate\Http\Request;

class SerialController extends Controller
{
    public function index(Request $request, string $lang)
    {
        $serials = Series::withCount([
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
        $brands = Brand::pluck('name', 'id');

        return view('series.index', ['brands' => $brands, 'serials' => $serials]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request, string $lang)
    {
        $request->validate([
            'name' => 'required|string|max:25|unique:series,name',
        ]);
        $serial = new Series();
        $serial->name = $request->name;
        $serial->save();

        return redirect()->route('serial.index', withLang());
    }

    public function show(Series $serials)
    {
    }

    public function edit(Request $serial, string $lang, string $id)
    {
        //
    }

    public function update(Request $request, string $lang)
    {
        $messages = [
            'required' => 'This field can not be blanked',
            'unique'   => 'This series already exists',
        ];
        $request->validate([
            'name' => 'required|unique:series,name,' . $request->id,
        ], $messages);

        $serial = Series::findOrFail($request->id);
        $serial->name = $request->name;
        $serial->save();

        return redirect()->route('serial.index', withLang());
    }

    public function destroy(string $lang, $id)
    {
        $serial = Series::findOrFail($id);
        $serial->delete();

        return redirect()->route('serial.index', withLang());
    }
}