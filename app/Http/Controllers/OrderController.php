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
        $this->middleware('permission:order-list|order-create|order-edit|order-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:order-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:order-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request, string $lang)
    {
        $query = Order::with('customer', 'employee');
        $customers = Customer::pluck('name', 'id');
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

        return view('orders.index', compact('orders', 'customers', 'parameterNames'));
    }



public function create(string $lang)
{
    $customers = Customer::pluck('name', 'id');
    $products = Product::available()->with('brand', 'series', 'color', 'storage')->get();
    $brands = Brand::all();
    $nextOrderId = (Order::max('id') ?? 0) + 1;
    $productsJson = $products->map(function($p) {
        return [
            'id'    => $p->id,
            'name'  => $p->product_name,
            'imei'  => $p->product_imei,
            'price' => $p->selling_price,
            'brand' => $p->brand_id,
        ];
    })->toJson();

    return view('orders.create', compact('customers', 'products', 'brands', 'nextOrderId', 'productsJson'));
}

    public function store(Request $request, string $lang)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'productIds'     => 'required|array|min:1',
            'productIds.*'   => 'exists:products,id',
            'payment_type'   => 'required|in:1,2,3',
            'payment_status' => 'required|in:1,2',
            'note'           => 'nullable|string',
        ]);

        $total = 0;
        foreach ($request->productIds as $productId) {
            $product = Product::available()->findOrFail($productId);
            $total += $product->selling_price;
        }

        $order = Order::create([
            'customer_id'    => $request->customer_id,
            'employee_id'    => Auth::id(),
            'status'         => Order::STATUS_ACTIVE,
            'total_amount'   => $total,
            'payment_status' => $request->payment_status,
            'payment_type'   => $request->payment_type,
            'note'           => $request->note,
            'order_date'     => Carbon::now(),
        ]);

        foreach ($request->productIds as $productId) {
            $product = Product::available()->findOrFail($productId);

            OrderDetail::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'unit_price' => $product->selling_price,
            ]);

            $product->update(['status' => Product::STATUS_ID_SOLD]);
        }

        return redirect()->route('orders.index', withLang())
            ->with('success', 'Order created successfully');
    }

    public function show(string $lang, Order $order)
    {
        $order = $order->with('orderDetails', 'customer', 'employee')->findOrFail($order->id);
        $order_detals = OrderDetail::where('order_id', $order->id)->with('product')->get();
        return view('orders.show', compact('order', 'order_detals'));
    }

    public function destroy(string $lang, Order $order)
    {
        $orderDetails = OrderDetail::where('order_id', $order->id)->get();

        foreach ($orderDetails as $detail) {
            Product::where('id', $detail->product_id)
                ->update(['status' => Product::STATUS_ID_AVAILABLE]);
        }

        OrderDetail::where('order_id', $order->id)->delete();
        $order->delete();

        return redirect()->route('orders.index', withLang())
            ->with('success', 'Sale deleted successfully');
    }

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
}