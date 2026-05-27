<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Series;
use App\Models\Color;
use App\Models\ModelType;
use App\Models\Storage;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','store']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Product::query()->with('network');

        $brands = Brand::pluck('name', 'id');
        $series = Series::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $modelTypes = ModelType::pluck('name', 'id');
        $storage = Storage::pluck('name', 'id');

        $type_of_machines = Product::TYPE_OF_MACHINE;
        $conditions = Product::CONDITION;
        $status = Product::getStatuses();

        $parameterNames = [];

        if ($request->search) {
            $filters = $request->only([
                'condition',
                'brand_id',
                'series_id',
                'type_of_machine',
                'color_id',
                'storage_id',
                'status'
            ]);

            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, $value);
                    $parameterNames[$key] = $value;
                }
            }

            if (!empty($request->search_product)) {
                $search = $request->search_product;

                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%$search%")
                      ->orWhere('product_imei', 'like', "%$search%");
                });

                $parameterNames['search_product'] = $search;
            }
        }

        $view = $request->view === 'gride' ? 'gride' : 'list';
        $viewUrl = $view === 'gride' ? 'list' : 'gride';

        $url = $request->fullUrlWithQuery(['view' => $viewUrl]);

        $productsQuery = clone $query;

        $products = $query
            ->orderBy('status', 'asc')
            ->orderBy('purchase_date', 'desc')
            ->paginate(20);

        $totalProductAvailable = (clone $productsQuery)->where('status', 1)->count();
        $totalProductSold = Product::where('status', 2)->count();

        return view('products.index', compact(
            'products',
            'view',
            'brands',
            'series',
            'colors',
            'modelTypes',
            'storage',
            'conditions',
            'type_of_machines',
            'status',
            'parameterNames',
            'url',
            'totalProductAvailable',
            'totalProductSold'
        ));
    }

    //  FIXED: CREATE METHOD (THIS IS WHAT YOU NEED)
    public function create()
    {
        $brands = Brand::pluck('name', 'id');
        $series = Series::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $modelTypes = ModelType::pluck('name', 'id');
        $storage = Storage::pluck('name', 'id');

        $status = Product::getStatuses();
        $conditions = Product::CONDITION;

        return view('products.create', compact(
            'brands',
            'series',
            'colors',
            'modelTypes',
            'storage',
            'status',
            'conditions'
        ));
    }

    public function store(ProductRequest $request)
    {
        $product = new Product();

        $product->product_code = $request->product_code ?? '';
        $product->product_name = $request->product_name;
        $product->product_imei = $request->product_imei;
        $product->brand_id = $request->brand;
        $product->series_id = $request->series;
        $product->color_id = $request->color;
        $product->model_type_id = $request->model_type;
        $product->condition = $request->condition;
        $product->storage_id = $request->storage;
        $product->type_of_machine = $request->type_of_machine;
        $product->network_id = $request->network;
        $product->battery_percentage = $request->battery_percentage;
        $product->percentage = $request->percentage;
        $product->purchase_price = $request->purchase_price;
        $product->selling_price = $request->selling_price;
        $product->employee_id = Auth::id();
        $product->purchase_date = $request->purchase_date;
        $product->image = '';
        $product->status = $request->status;
        $product->note = $request->note ?? '';

        $product->save();

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $destinationPath = 'images/product/';
            $formattedNumber = str_pad($product->id, 5, '0', STR_PAD_LEFT);

            $filename = md5($image->getClientOriginalName() . time())
                . '.' . $image->getClientOriginalExtension();

            $image->move($destinationPath, $formattedNumber . '_' . $filename);

            $product->image = $formattedNumber . '_' . $filename;
            $product->save();
        }

        return redirect()->route('products.show', ['product' => $product->id]);
    }

    public function show(string $lang, Product $product)
    {
        $product->load('brand', 'series', 'color', 'modelType', 'storage');

        return view('products.show', compact('product'));
    }

    public function edit(string $lang, Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, string $lang, Product $product)
    {
        $product->update($request->validated());

        return redirect()->route('products.index', $lang);
    }

    public function destroy(string $lang, Product $product)
    {
        $product->delete();

        return redirect()->route('products.index', $lang);
    }
}