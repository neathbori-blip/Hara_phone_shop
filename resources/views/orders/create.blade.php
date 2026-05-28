@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Orders /</span> Create New Order
  </h4>

  <form action="" method="POST" id="orderForm">
    @csrf
    <div class="row">

      {{-- Left Column --}}
      <div class="col-md-8">

        {{-- Product Selection --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Select Products</h5>
          </div>
          <div class="card-body">
            <input type="text" id="productSearch" class="form-control mb-3" placeholder="Search product by name, IMEI, code...">
            <div class="table-responsive" style="max-height: 400px; overflow-y:auto;">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th></th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>IMEI</th>
                    <th>Brand</th>
                    <th>Storage</th>
                    <th>Condition</th>
                    <th>Price</th>
                  </tr>
                </thead>
                <tbody id="productTable">
                  @foreach($products as $product)
                  <tr class="product-row" 
                      data-name="{{ strtolower($product->product_name) }}"
                      data-imei="{{ strtolower($product->product_imei) }}"
                      data-code="{{ strtolower($product->product_code) }}">
                    <td>
                      <input type="checkbox" class="form-check-input product-checkbox"
                        name="productIds[]"
                        value="{{ $product->id }}"
                        data-price="{{ $product->selling_price }}"
                        data-name="{{ $product->product_name }}">
                    </td>
                    <td>{{ $product->product_code }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->product_imei }}</td>
                    <td>{{ $product->brand->name ?? '-' }}</td>
                    <td>{{ $product->storage->name ?? '-' }}</td>
                    <td>{!! $product->condition_label_badges_name !!}</td>
                    <td>${{ number_format($product->selling_price, 2) }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Selected Products Summary --}}
        <div class="card mb-4" id="selectedProductsCard" style="display:none!important;">
          <div class="card-header">
            <h5 class="mb-0">Selected Products</h5>
          </div>
          <div class="card-body p-0">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>Product</th>
                  <th class="text-end">Price</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="selectedProductsList"></tbody>
              <tfoot>
                <tr>
                  <th>Total</th>
                  <th class="text-end" id="totalAmount">$0.00</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>

      {{-- Right Column --}}
      <div class="col-md-4">

        {{-- Customer --}}
        <div class="card mb-4">
          <div class="card-header"><h5 class="mb-0">Customer</h5></div>
          <div class="card-body">
            <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
              <option value="">-- Select Customer --</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                  {{ $customer->name }}
                </option>
              @endforeach
            </select>
            @error('customer_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Payment --}}
        <div class="card mb-4">
          <div class="card-header"><h5 class="mb-0">Payment</h5></div>
          <div class="card-body">

            <div class="mb-3">
              <label class="form-label">Payment Type</label>
              <select name="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                <option value="">-- Select --</option>
                <option value="1" {{ old('payment_type') == 1 ? 'selected' : '' }}>Cash</option>
                <option value="2" {{ old('payment_type') == 2 ? 'selected' : '' }}>Bank</option>
                <option value="3" {{ old('payment_type') == 3 ? 'selected' : '' }}>Other</option>
              </select>
              @error('payment_type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Payment Status</label>
              <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" required>
                <option value="">-- Select --</option>
                <option value="1" {{ old('payment_status') == 1 ? 'selected' : '' }}>Paid</option>
                <option value="2" {{ old('payment_status') == 2 ? 'selected' : '' }}>Unpaid</option>
              </select>
              @error('payment_status')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Note</label>
              <textarea name="note" class="form-control" rows="3" placeholder="Optional note...">{{ old('note') }}</textarea>
            </div>

          </div>
        </div>

        {{-- Submit --}}
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
            <i class="bx bx-save me-1"></i> Create Order
          </button>
          <a href="{{ route('sales.index', withLang()) }}" class="btn btn-outline-secondary">
            Cancel
          </a>
        </div>

      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
  const checkboxes = document.querySelectorAll('.product-checkbox');
  const submitBtn  = document.getElementById('submitBtn');
  const selectedList = document.getElementById('selectedProductsList');
  const totalEl   = document.getElementById('totalAmount');
  const card      = document.getElementById('selectedProductsCard');

  function updateSelected() {
    selectedList.innerHTML = '';
    let total = 0;
    let count = 0;

    checkboxes.forEach(cb => {
      if (cb.checked) {
        count++;
        const price = parseFloat(cb.dataset.price);
        total += price;
        selectedList.innerHTML += `
          <tr>
            <td>${cb.dataset.name}</td>
            <td class="text-end">$${price.toFixed(2)}</td>
            <td><button type="button" class="btn btn-sm btn-danger py-0" onclick="uncheckProduct(${cb.value})">✕</button></td>
          </tr>`;
      }
    });

    totalEl.textContent = '$' + total.toFixed(2);
    submitBtn.disabled  = count === 0;
    card.style.display  = count > 0 ? 'block' : 'none';
  }

  function uncheckProduct(id) {
    const cb = document.querySelector(`.product-checkbox[value="${id}"]`);
    if (cb) { cb.checked = false; updateSelected(); }
  }

  checkboxes.forEach(cb => cb.addEventListener('change', updateSelected));

  // Search filter
  document.getElementById('productSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-row').forEach(row => {
      const match = row.dataset.name.includes(q) ||
                    row.dataset.imei.includes(q) ||
                    row.dataset.code.includes(q);
      row.style.display = match ? '' : 'none';
    });
  });
</script>
@endpush
@endsection