@extends('layouts.app')

@push('styles')
<style>
    .info-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 600;
        color: #8a8d93;
        letter-spacing: 0.4px;
    }
    .info-value {
        font-size: 14px;
        font-weight: 700;
        color: #333;
    }
    .product-image {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 8px;
    }
    .info-row {
        padding: 10px 0;
    }
</style>
@endpush

@section('content')

<div class="container-fluid container-p-y">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white border-bottom-0">
            <h5 class="mb-0">Product Information</h5>
        </div>

        <div class="card-body">

            <!-- Product Image -->
            <div class="mb-4">
                <img src="{{ $product->image_name }}"
                     class="product-image"
                     onError="this.onerror=null;this.src='{{ asset('/assets/img/blank-product.svg') }}';">
            </div>

            @php
                $conditions       = App\Models\Product::CONDITION;
                $type_of_machines = App\Models\Product::TYPE_OF_MACHINE;
                $statuses         = App\Models\Product::getStatuses();
            @endphp

            <!-- Fields Grid -->
            <div class="row">

                <!-- LEFT -->
                <div class="col-md-6">

                    <div class="info-row">
                        <span class="info-label">Product Name : </span>
                        <span class="info-value">{{ $product->product_name ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Product Code : </span>
                        <span class="info-value">{{ $product->product_code ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Brand : </span>
                        <span class="info-value">{{ $product->brand->name ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Color : </span>
                        <span class="info-value">{{ $product->color->name ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Storage : </span>
                        <span class="info-value">{{ $product->storage->name ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Battery Percentage : </span>
                        <span class="info-value">{{ $product->battery_percentage ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Purchase Date : </span>
                        <span class="info-value">{{ $product->purchase_date ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Selling Price : </span>
                        <span class="info-value">{{ $product->selling_price ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Product Note : </span>
                        <span class="info-value">{{ $product->note ?? '—' }}</span>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-md-6">

                    <div class="info-row">
                        <span class="info-label">Product IMEI : </span>
                        <span class="info-value">{{ $product->product_imei ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Condition : </span>
                        <span class="info-value">{{ $conditions[$product->condition] ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Series : </span>
                        <span class="info-value">{{ $product->series->name ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Model : </span>
                        <span class="info-value">{{ $product->modelType->name ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Type of Machine : </span>
                        <span class="info-value">{{ $type_of_machines[$product->type_of_machine] ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Product Percentage : </span>
                        <span class="info-value">{{ $product->percentage ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Purchase Price : </span>
                        <span class="info-value">{{ $product->purchase_price ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Product Status : </span>
                        <span class="info-value">{{ $statuses[$product->status] ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Lock By : </span>
                        <span class="info-value">{{ $product->network->name ?? '—' }}</span>
                    </div>

                </div>

            </div>

            <!-- BUTTONS -->
            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('products.index', app()->getLocale()) }}"
                   class="btn btn-outline-secondary">
                    Product Lists
                </a>
                <a href="{{ route('products.edit', ['lang' => app()->getLocale(), 'product' => $product->id]) }}"
                   class="btn btn-primary">
                    Edit
                </a>
            </div>

        </div>
    </div>

</div>

@endsection

@push('script')
@endpush