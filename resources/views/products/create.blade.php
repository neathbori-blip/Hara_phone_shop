@extends('layouts.app')

@section('content')

<div class="container-fluid container-p-y">

    <form action="{{ route('products.store', app()->getLocale()) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="card shadow-sm border-0">

            <!-- TITLE -->
            <div class="card-header bg-white border-0">
                <h4 class="mb-0">Register Product</h4>
            </div>

            <div class="card-body">

                <div class="row g-4">

                  
                    <div class="col-lg-3">

                        <div class="border rounded-3 p-3 text-center h-100">

                            <!-- ICON -->
                            <div class="mb-3">
                                <i class="bx bx-image text-secondary" style="font-size:70px;"></i>
                                <p class="small text-muted mt-2 mb-0">
                                    Product Image<br>Coming Soon
                                </p>
                            </div>

                            <!-- FILE INPUT -->
                            <input type="file" name="image" class="form-control mb-3">

                            <!-- BUTTONS -->
                            <button type="button" class="btn btn-primary w-100 mb-2">
                                Upload new photo
                            </button>

                            <button type="reset" class="btn btn-outline-secondary w-100">
                                Reset
                            </button>

                            <small class="text-muted d-block mt-3">
                                Allowed JPG, GIF or PNG
                            </small>

                        </div>

                    </div>

                
                    <div class="col-lg-9">

                        <div class="row g-3">

                            <!-- PRODUCT NAME -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Product Name</label>
                                <input type="text" name="product_name" class="form-control">
                            </div>

                            <!-- IMEI -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Product IMEI</label>
                                <input type="text" name="product_imei" class="form-control">
                            </div>

                            <!-- CODE -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Product Code</label>
                                <input type="text" name="product_code" class="form-control">
                            </div>

                            <!-- CONDITION -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Condition</label>
                                <select name="condition" class="form-select">
                                    @foreach($conditions as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- BRAND -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Brand</label>
                                <select name="brand" class="form-select">
                                    @foreach($brands as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- SERIES -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Series</label>
                                <select name="series" class="form-select">
                                    @foreach($series as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- COLOR -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Color</label>
                                <select name="color" class="form-select">
                                    @foreach($colors as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- MODEL -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Model</label>
                                <select name="model_type" class="form-select">
                                    @foreach($modelTypes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- STORAGE -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Storage</label>
                                <select name="storage" class="form-select">
                                    @foreach($storage as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- TYPE MACHINE -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Type of Machine</label>
                                <select name="type_machine" class="form-select">
                                    <option value="">Select</option>
                                </select>
                            </div>

                            <!-- LOCK BY -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Lock By</label>
                                <select name="lock_by" class="form-select">
                                    <option value="">Select</option>
                                </select>
                            </div>

                            <!-- BATTERY -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Battery %</label>
                                <input type="number" name="battery_percentage" class="form-control">
                            </div>

                            <!-- PRODUCT % -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Product %</label>
                                <input type="number" name="product_percentage" class="form-control">
                            </div>

                            <!-- PURCHASE -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Purchase Price</label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control">
                            </div>

                            <!-- SELLING -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Selling Price</label>
                                <input type="number" step="0.01" name="selling_price" class="form-control">
                            </div>

                            <!-- DATE -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control">
                            </div>

                            <!-- STATUS -->
                            <div class="col-md-6">
                                <label class="form-label text-uppercase small">Product Status</label>
                                <select name="status" class="form-select">
                                    @foreach($status as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <!-- BUTTON -->
                        <div class="mt-4">
                            <button class="btn btn-primary px-4">
                                Save Product
                            </button>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </form>

</div>

@endsection