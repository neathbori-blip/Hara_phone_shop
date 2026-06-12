@extends('layouts.app')

@push('styles')
<style>
    .input-suffix {
        position: relative;
    }
    .input-suffix input {
        padding-right: 32px;
    }
    .input-suffix .suffix {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 13px;
        pointer-events: none;
    }
</style>
@endpush

@section('content')

<div class="container-fluid container-p-y">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', ['lang' => app()->getLocale(), 'product' => $product->id]) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white border-bottom-0">
                <h5 class="mb-0">Edit Product</h5>
            </div>

            <div class="card-body">

                <!-- IMAGE ROW -->
                <div class="d-flex align-items-start gap-3 mb-4">

                    <div class="text-center">
                        <img id="preview-image"
                             src="{{ $product->image_name }}"
                             style="width:70px; height:70px; object-fit:cover;"
                             class="rounded"
                             onError="this.onerror=null;this.src='{{ asset('/assets/img/blank-product.svg') }}';">
                        <div class="small text-muted mt-1" style="font-size:11px;">
                            Product Image
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" name="image" id="imageInput" class="d-none">
                            <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick="document.getElementById('imageInput').click();">
                                Upload new photo
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="resetBtn">
                                Reset
                            </button>
                        </div>
                        <small class="text-muted">Allowed JPG, GIF or PNG.</small>
                    </div>

                </div>

                <!-- FIELDS GRID -->
                <div class="row g-3">

                    <!-- Product Name | Product IMEI -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Product Name</label>
                        <input type="text"
                               name="product_name"
                               class="form-control"
                               value="{{ old('product_name', $product->product_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Product IMEI</label>
                        <input type="text"
                               name="product_imei"
                               class="form-control"
                               value="{{ old('product_imei', $product->product_imei) }}">
                    </div>

                    <!-- Product Code | Condition -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Product Code</label>
                        <input type="text"
                               name="product_code"
                               class="form-control"
                               value="{{ old('product_code', $product->product_code) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Condition</label>
                        <select name="condition" class="form-select">
                            @foreach($conditions as $key => $value)
                                <option value="{{ $key }}" {{ old('condition', $product->condition) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brand | Series -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($brands as $id => $name)
                                <option value="{{ $id }}" {{ old('brand_id', $product->brand_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Series</label>
                        <select name="series_id" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($series as $id => $name)
                                <option value="{{ $id }}" {{ old('series_id', $product->series_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Color | Model -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Color</label>
                        <select name="color_id" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($colors as $id => $name)
                                <option value="{{ $id }}" {{ old('color_id', $product->color_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Model</label>
                        <select name="model_type_id" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($modelTypes as $id => $name)
                                <option value="{{ $id }}" {{ old('model_type_id', $product->model_type_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Storage | Type of Machine | Lock By -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Storage</label>
                        <select name="storage_id" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($storage as $id => $name)
                                <option value="{{ $id }}" {{ old('storage_id', $product->storage_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-uppercase small fw-semibold">Type of Machine</label>
                        <select name="type_of_machine" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($type_of_machines as $key => $value)
                                <option value="{{ $key }}" {{ old('type_of_machine', $product->type_of_machine) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-uppercase small fw-semibold">Lock By</label>
                        <select name="network_id" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($networks as $id => $name)
                                <option value="{{ $id }}" {{ old('network_id', $product->network_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Battery % | Product % -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Battery Percentage</label>
                        <div class="input-suffix">
                            <input type="number"
                                   name="battery_percentage"
                                   class="form-control"
                                   value="{{ old('battery_percentage', $product->battery_percentage) }}">
                            <span class="suffix">%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Product Percentage</label>
                        <div class="input-suffix">
                            <input type="number"
                                   name="product_percentage"
                                   class="form-control"
                                   value="{{ old('product_percentage', $product->percentage) }}">
                            <span class="suffix">%</span>
                        </div>
                    </div>

                    <!-- Purchase Price | Selling Price -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Purchase Price</label>
                        <div class="input-suffix">
                            <input type="number"
                                   step="0.01"
                                   name="purchase_price"
                                   class="form-control"
                                   value="{{ old('purchase_price', $product->purchase_price) }}">
                            <span class="suffix">$</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Selling Price</label>
                        <div class="input-suffix">
                            <input type="number"
                                   step="0.01"
                                   name="selling_price"
                                   class="form-control"
                                   value="{{ old('selling_price', $product->selling_price) }}">
                            <span class="suffix">$</span>
                        </div>
                    </div>

                    <!-- Purchase Date | Product Status -->
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Purchase Date</label>
                        <input type="date"
                               name="purchase_date"
                               class="form-control"
                               value="{{ old('purchase_date', $product->purchase_date) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-semibold">Product Status</label>
                        <select name="status" class="form-select">
                            <option value="">Select an option</option>
                            @foreach($status as $key => $value)
                                <option value="{{ $key }}" {{ old('status', $product->status) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Note -->
                    <div class="col-md-12">
                        <label class="form-label text-uppercase small fw-semibold">Product Note</label>
                        <textarea name="note"
                                  class="form-control"
                                  rows="3">{{ old('note', $product->note) }}</textarea>
                    </div>

                </div>

                <!-- BUTTONS -->
                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('products.show', ['lang' => app()->getLocale(), 'product' => $product->id]) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        Update Product
                    </button>
                </div>

            </div>
        </div>

    </form>

</div>

@endsection

@push('script')
<script>
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('preview-image').src = event.target.result;
        };
        if (e.target.files[0]) {
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    document.getElementById('resetBtn').addEventListener('click', function () {
        document.getElementById('imageInput').value = '';
        document.getElementById('preview-image').src = '{{ $product->image_name }}';
    });
</script>
@endpush