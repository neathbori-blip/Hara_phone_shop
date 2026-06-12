<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Series;
use App\Models\Color;
use App\Models\ModelType;
use App\Models\Storage;
use App\Models\Network;
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
        // Handle AJAX count refresh request
        if ($request->ajax() && $request->count_only) {
            return response()->json([
                'available' => Product::where('status', 1)->count(),
                'sold'      => Product::where('status', 2)->count(),
            ]);
        }

        $query = Product::with([
            'network',
            'brand',
            'series',
            'color',
            'modelType',
            'storage'
        ]);

        $brands       = Brand::pluck('name', 'id');
        $series       = Series::pluck('name', 'id');
        $colors       = Color::pluck('name', 'id');
        $modelTypes   = ModelType::pluck('name', 'id');
        $storage      = Storage::pluck('name', 'id');

        $type_of_machines = Product::TYPE_OF_MACHINE;
        $conditions       = Product::CONDITION;
        $status           = Product::getStatuses();

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

            if ($request->search_product) {
                $search = $request->search_product;
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                      ->orWhere('product_imei', 'like', "%{$search}%");
                });
                $parameterNames['search_product'] = $search;
            }
        }

        $view = $request->view === 'gride' ? 'gride' : 'list';
        $url  = $request->fullUrlWithQuery(['view' => $view === 'gride' ? 'list' : 'gride']);

        $productsQuery = clone $query;

        $products = $query
            ->orderBy('status', 'asc')
            ->orderBy('purchase_date', 'desc')
            ->paginate(20);

        $totalProductAvailable = (clone $productsQuery)->where('status', 1)->count();
        $totalProductSold      = Product::where('status', 2)->count();

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

    public function create()
    {
        return view('products.create', [
            'brands'           => Brand::pluck('name', 'id'),
            'series'           => Series::pluck('name', 'id'),
            'colors'           => Color::pluck('name', 'id'),
            'modelTypes'       => ModelType::pluck('name', 'id'),
            'storage'          => Storage::pluck('name', 'id'),
            'networks'         => Network::pluck('name', 'id'),
            'type_of_machines' => Product::TYPE_OF_MACHINE,
            'status'           => Product::getStatuses(),
            'conditions'       => Product::CONDITION,
        ]);
    }

   public function store(Request $request)
{
    $request->validate([
        'product_name' => 'required',
        'product_imei' => 'required',
        'brand'        => 'required|exists:brands,id',

        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:20480',
    ], [
        'brand.required' => 'Please select a brand.',
        'image.max'      => 'Image size must be less than 20MB.',
        'image.mimes'    => 'Only JPG, JPEG, PNG, GIF or WEBP images are allowed.',
        'image.image'    => 'The file must be an image.',
    ]);

    $product = new Product();

    $product->product_code       = $request->product_code;
    $product->product_name       = $request->product_name;
    $product->product_imei       = $request->product_imei;

    $product->brand_id           = $request->brand;
    $product->series_id          = $request->series;
    $product->color_id           = $request->color;
    $product->model_type_id      = $request->model_type;
    $product->storage_id         = $request->storage;
    $product->network_id         = $request->network;

    $product->condition          = $request->condition;
    $product->type_of_machine    = $request->type_machine;

    $product->battery_percentage = $request->battery_percentage ?? 0;
    $product->percentage         = $request->product_percentage ?? 0;

    $product->purchase_price     = $request->purchase_price ?? 0;
    $product->selling_price      = $request->selling_price ?? 0;

    $product->purchase_date      = $request->purchase_date;
    $product->status             = $request->status;
    $product->note               = $request->note;

    $product->employee_id        = Auth::id();

    $product->save();

    if ($request->hasFile('image')) {

        $image = $request->file('image');

        $destinationPath = public_path('images/product');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        $image->move($destinationPath, $filename);

        $product->image = $filename;
        $product->save();
    }

    return redirect()->route('products.show', [
        'lang'    => app()->getLocale(),
        'product' => $product->id,
    ]);
}

    public function show(string $lang, Product $product)
    {
        $product->load('brand', 'series', 'color', 'modelType', 'storage', 'network');
        return view('products.product-show', compact('product'));
    }

    public function edit(string $lang, Product $product)
    {
        return view('products.edit', [
            'product'          => $product,
            'brands'           => Brand::pluck('name', 'id'),
            'series'           => Series::pluck('name', 'id'),
            'colors'           => Color::pluck('name', 'id'),
            'modelTypes'       => ModelType::pluck('name', 'id'),
            'storage'          => Storage::pluck('name', 'id'),
            'networks'         => Network::pluck('name', 'id'),
            'type_of_machines' => Product::TYPE_OF_MACHINE,
            'status'           => Product::getStatuses(),
            'conditions'       => Product::CONDITION,
        ]);
    }

    public function update(Request $request, string $lang, Product $product)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120', // max 5MB
        ], [
            'image.max'    => 'Image size must be less than 5MB.',
            'image.mimes'  => 'Only JPG, JPEG, PNG or GIF images are allowed.',
            'image.image'  => 'The file must be an image.',
        ]);

        $product->product_code       = $request->product_code ?? null;
        $product->product_name       = $request->product_name;
        $product->product_imei       = $request->product_imei;

        $product->brand_id           = $request->brand_id;
        $product->series_id          = $request->series_id;
        $product->color_id           = $request->color_id;
        $product->model_type_id      = $request->model_type_id;
        $product->storage_id         = $request->storage_id;
        $product->network_id         = $request->network_id;

        $product->condition          = $request->condition;
        $product->type_of_machine    = $request->type_of_machine;

        $product->battery_percentage = $request->battery_percentage ?? 0;
        $product->percentage         = $request->product_percentage ?? 0;

        $product->purchase_price     = $request->purchase_price ?? 0;
        $product->selling_price      = $request->selling_price ?? 0;
        $product->purchase_date      = $request->purchase_date;

        $product->status             = $request->status;
        $product->note               = $request->note ?? null;
        $product->employee_id        = Auth::id();

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('images/product/' . $product->image))) {
                unlink(public_path('images/product/' . $product->image));
            }

            $image           = $request->file('image');
            $destinationPath = public_path('images/product');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);

            $product->image = $filename;
        }

        $product->save();

        return redirect()->route('products.show', [
            'lang'    => $lang,
            'product' => $product->id,
        ]);
    }

    public function destroy(string $lang, Product $product)
    {
        if ($product->image && file_exists(public_path('images/product/' . $product->image))) {
            unlink(public_path('images/product/' . $product->image));
        }

        $product->delete();

        return redirect()->route('products.index', $lang);
    }
}