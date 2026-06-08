@extends('layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Back & Print Buttons --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('sales.index', withLang()) }}" class="btn btn-outline-secondary">
            <i class='bx bx-arrow-back'></i> {{ __('button.back') ?? 'Back' }}
        </a>
        @can('order-edit')
        <a href="{{ route('sales.invoice', withLang(['order' => $order->id])) }}"
           class="btn btn-outline-primary">
            <i class='bx bx-printer'></i> {{ __('order.invoice') ?? 'Invoice' }}
        </a>
        @endcan
    </div>

    {{-- Invoice Card --}}
    <div class="card" style="max-width:900px; margin:0 auto; padding:40px 50px; font-family:'Segoe UI',sans-serif; color:#333;">

        {{-- Shop Header --}}
        <div class="text-center mb-4">
            <div style="font-weight:700; font-size:16px; color:#3a5fa0;">CMy Phone Shop</div>
            <div style="font-size:13px; color:#3a5fa0;">មានតំរូសសំឡឹ iPhone មកពីអាមេរិក</div>
        </div>

        {{-- Shop Info + Invoice Info --}}
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div style="font-size:13px; color:#555; line-height:2;">
                <div><i class='bx bx-phone me-1'></i> 011 699 952</div>
                <div><i class='bx bx-map me-1'></i> #44 មហាវិថីព្រះម៉ន់រំ សង្កាត់ស្រះចក ខណ្ឌដូនពេញ រាជធានីភ្នំពេញ</div>
            </div>
            <div style="font-size:13px; color:#555; text-align:right; line-height:2;">
                <div>Invoice: <strong>#{{ $order->id_number }}</strong></div>
                <div>Issued Date: <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong></div>
                <div>Order Date: <strong>{{ setToStringDateFormat($order->order_date) }}</strong></div>
            </div>
        </div>

        <hr style="border-color:#e0e0e0;">

        {{-- Customer Info --}}
        <div class="mb-4 mt-3" style="font-size:13px;">
            <div style="color:#888; margin-bottom:4px;">Customer:</div>
            <div style="font-weight:600;">{{ $order->customer?->name ?? 'Walk in Customer' }}</div>
            <div style="color:#888;">{{ $order->customer?->phone ?? '000000000' }}</div>
        </div>

        {{-- INVOICE Title --}}
        <div class="text-center mb-4" style="font-size:15px; font-weight:600; letter-spacing:2px; color:#333;">
            INVOICE
        </div>

        {{-- Items Table --}}
        <table class="table" style="font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #ddd;">
                    <th style="color:#555; font-weight:600; padding:10px 0; width:20%;">ITEMS</th>
                    <th style="color:#555; font-weight:600; padding:10px 0; width:30%;">DESCRIPTION</th>
                    <th style="color:#555; font-weight:600; padding:10px 0; width:15%;">IMAGE</th>
                    <th style="color:#555; font-weight:600; padding:10px 0; width:15%;">COST</th>
                    <th style="color:#555; font-weight:600; padding:10px 0; width:5%;">QTY</th>
                    <th style="color:#555; font-weight:600; padding:10px 0; width:15%; text-align:right;">PRICE</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order_detals as $index => $item)
                <tr style="border-bottom:1px solid #f0f0f0;">

                    <td style="padding:14px 0; vertical-align:middle;">
                        {{ $item->product->product_name ?? '-' }}
                        @if($item->product?->product_imei)
                            <div style="color:#999; font-size:11px;">
                                [ IMEI: {{ $item->product->product_imei }} ]
                            </div>
                        @endif
                    </td>

                    {{-- Description --}}
                    <td style="padding:14px 0; vertical-align:middle; color:#555;">
                        {{ $item->product->product_name ?? '' }}
                        @if(optional($item->product?->storage)->name)
                            , {{ $item->product->storage->name }}
                        @endif
                        @if(optional($item->product?->color)->name)
                            , {{ $item->product->color->name }}
                        @endif
                    </td>

                    <td style="padding:14px 0; vertical-align:middle;">
                        @if($item->product?->image)
                            <img src="{{ asset('images/product/' . $item->product->image) }}"
                                 alt="{{ $item->product->product_name }}"
                                 width="55" height="55"
                                 style="object-fit:cover; border-radius:6px; border:1px solid #eee;">
                        @else
                            <div style="width:55px; height:55px; border-radius:6px;
                                        background:#f5f5f5; border:1px solid #eee;
                                        display:flex; align-items:center;
                                        justify-content:center; color:#ccc;">
                                <i class='bx bx-camera' style="font-size:22px;"></i>
                            </div>
                        @endif
                    </td>

                    {{-- Cost --}}
                    <td style="padding:14px 0; vertical-align:middle;">
                        {{ setToStringDolla($item->unit_price) }}
                    </td>

                    {{-- Qty --}}
                    <td style="padding:14px 0; vertical-align:middle;">1</td>

                    {{-- Price --}}
                    <td style="padding:14px 0; vertical-align:middle; text-align:right;">
                        <strong>{{ setToStringDolla($item->unit_price) }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-5 text-muted">
                        {{ __('common.lbl_no_data') ?? 'No products found' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer: Seller + Total --}}
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div style="font-size:13px; color:#555;">The Seller</div>
            <div style="font-size:14px;">
                Total: &nbsp;<strong style="color:#3a5fa0; font-size:15px;">
                    {{ setToStringDolla($order->total_amount) }}
                </strong>
            </div>
        </div>

        <hr style="border-color:#e0e0e0; margin-top:30px;">

        {{-- Note --}}
        <div style="font-size:12px; color:#888; margin-top:10px;">
            Note: {{ $order->note ?? 'សូមពិនិត្យទំនិញឲ្យបានត្រឹមត្រូវ ដាតច្បាប់ទំនិញមិនអាចប្ដូរបានទេ' }}
        </div>

    </div>
</div>
@endsection