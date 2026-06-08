<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Series;
use App\Models\Color;
use App\Models\ModelType;
use App\Models\Storage;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:order-list|order-create|order-edit|order-delete', ['only' => ['index','store']]);
        $this->middleware('permission:order-create', ['only' => ['create','store']]);
        $this->middleware('permission:order-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query();
        $customers = Customer::all();
        $parameterNames = [];

        if ($request->search) {
            $filters = $request->only(['customer', 'from_date', 'to_date']);

            if (!empty($filters['customer'])) {
                $query->where('customer_id', $filters['customer']);
                $parameterNames['customer'] = $filters['customer'];
            }

            if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $query->whereBetween('order_date', [$filters['from_date'], $filters['to_date']]);
                $parameterNames['from_date'] = $filters['from_date'];
                $parameterNames['to_date'] = $filters['to_date'];
            } elseif (!empty($filters['from_date'])) {
                $query->where('order_date', '>=', $filters['from_date']);
                $parameterNames['from_date'] = $filters['from_date'];
            } elseif (!empty($filters['to_date'])) {
                $query->where('order_date', '<=', $filters['to_date']);
                $parameterNames['to_date'] = $filters['to_date'];
            }
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(20);
        session(['printInvoiceId' => null]);
        return view('orders.index', compact(
            'orders',
            'customers',
            'parameterNames'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $lang, Order $order)
    {
        $order = $order->with('orderDetails', 'customer', 'employee')->findOrFail($order->id);
        $order_detals = OrderDetail::where('order_id', $order->id)->with('product')->get();
        return view('orders.show', compact('order', 'order_detals'));
    }

    /**
     * Display the specified resource.
     */
    public function checkProductOrder(Request $request)
    {
        foreach ($request->productIds as $key => $productId) {
            $product = Product::available()->find($productId);
            if (!$product) {
                return response()->json(['message' => 'Product not found.'], 404);
            }
        }
        return response()->json(['message' => 'Submiting Order'], 201);
    }

    public function destroy(string $lang, Order $order)
    {
        $orderDetial = OrderDetail::where('order_id', $order->id)->get();

        return redirect()->route('sales.index', withLang())->with('success', 'Sale deleted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function invoice(string $lang, Order $order)
    {
        $order = $order->with('orderDetails', 'customer', 'employee')->findOrFail($order->id);
        $order_detals = OrderDetail::where('order_id', $order->id)->with('product')->get();
        return view('orders.invoice', compact('order', 'order_detals'));
    }

    public function invoicePdf(Request $request, string $lang, Order $order)
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $order = $order->with('orderDetails', 'customer', 'employee')->findOrFail($order->id);
        $order_detals = OrderDetail::where('order_id', $order->id)->with('product')->get();
        $file_pdf = 'invoice-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '.pdf';
        $type = $request->type ?? 'download';
        return view('orders.invoice-pdf', compact('order', 'order_detals', 'currentDate', 'file_pdf', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'order_date'   => 'required|date',
            'productIds'   => 'required|array|min:1',
            'productIds.*' => 'exists:products,id',
            
        ]);

        $order = Order::create([
        'customer_id'  => $request->customer_id,
        'employee_id'  => Auth::id(),
        'order_date'   => $request->order_date,
        'total_price'  => 0,
        'total_amount' => 0, // ✅ add this line
    ]);

        $total = 0;

        foreach ($request->productIds as $productId) {
            $product = Product::available()->findOrFail($productId);

            OrderDetail::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'unit_price' => $product->selling_price,
            ]);
            $total += $product->selling_price;
        }

        $order->update(['total_amount' => $total]);

        return redirect()->route('sales.index', withLang())
            ->with('success', 'Order created successfully.');
    }

    public function create()
    {
        $customers = Customer::all();
        $products  = Product::available()->get();
        $brands    = Brand::all();
        $nextOrderId = (Order::max('id') ?? 0) + 1;

        return view('orders.create', compact(
            'customers',
            'products',
            'brands',
            'nextOrderId',
        ));
    }

    public function saleIndex(Request $request)
{
    $query = Order::with('employee', 'customer'); // ← add this
    $customers = Customer::all();
    $parameterNames = [];

    if ($request->search) {
        $filters = $request->only(['customer', 'from_date', 'to_date']);

        if (!empty($filters['customer'])) {
            $query->where('customer_id', $filters['customer']);
            $parameterNames['customer'] = $filters['customer'];
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('order_date', [$filters['from_date'], $filters['to_date']]);
            $parameterNames['from_date'] = $filters['from_date'];
            $parameterNames['to_date'] = $filters['to_date'];
        } elseif (!empty($filters['from_date'])) {
            $query->where('order_date', '>=', $filters['from_date']);
            $parameterNames['from_date'] = $filters['from_date'];
        } elseif (!empty($filters['to_date'])) {
            $query->where('order_date', '<=', $filters['to_date']);
            $parameterNames['to_date'] = $filters['to_date'];
        }
    }

    $orders = $query->orderBy('order_date', 'desc')->paginate(20);
    return view('sales.index', compact('orders', 'customers', 'parameterNames'));
}

public function saleShow(string $lang, Order $order)
{
    $order = $order->with('orderDetails', 'customer', 'employee')->findOrFail($order->id);
    $order_detals = OrderDetail::where('order_id', $order->id)->with('product')->get();
    return view('sales.show', compact('order', 'order_detals'));  // ← goes to sales/show.blade.php
}

    public function saleCreate()
    {
        $customers   = Customer::all();
        $products    = Product::available()->get();
        $nextOrderId = (Order::max('id') ?? 0) + 1;

        return view('sales.create', compact(
            'customers', 'products', 'nextOrderId'
        ));
    }
}