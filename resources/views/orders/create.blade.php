@extends('layouts.app')

@push('styles')
<style>
  .order-create-page {
    display: flex;
    height: calc(100vh - 60px);
    overflow: hidden;
    background: #f4f5fb;
  }

  /* LEFT SIDEBAR */
  .brand-sidebar {
    width: 110px;
    min-width: 110px;
    background: #fff;
    border-right: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 10px 6px;
    gap: 8px;
    overflow-y: auto;
  }
  .brand-sidebar .btn-search {
    width: 90px;
    background: #1976d2;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 7px 0;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    margin-bottom: 4px;
  }
  .brand-sidebar .btn-search:hover { background: #1565c0; }

  .brand-item {
    width: 90px;
    background: #f0f4ff;
    border: 2px solid transparent;
    border-radius: 10px;
    padding: 8px 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
  }
  .brand-item:hover, .brand-item.active {
    border-color: #1976d2;
    background: #e3f0ff;
  }
  .brand-item .brand-placeholder {
    width: 44px;
    height: 44px;
    background: #e0e0e0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: #888;
    font-weight: 700;
    text-align: center;
  }
  .brand-item span {
    font-size: 11px;
    font-weight: 600;
    color: #333;
    text-align: center;
  }

  /* MAIN PRODUCT AREA */
  .product-area {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
  }
  .product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
  }
  .product-card {
    background: #fff;
    border-radius: 12px;
    border: 2px solid transparent;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
  }
  .product-card:hover {
    border-color: #1976d2;
    box-shadow: 0 4px 16px rgba(25,118,210,0.15);
    transform: translateY(-2px);
  }
  .product-card.selected {
    border-color: #1976d2;
    background: #e3f0ff;
  }
  .product-card .product-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    background: #f5f5f5;
  }
  .product-card .product-img-placeholder {
    width: 100%;
    height: 160px;
    background: #f0f0f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #bbb;
    font-size: 12px;
    gap: 6px;
  }
  .product-card .product-img-placeholder i { font-size: 36px; }
  .product-card .product-info { padding: 10px 12px; }
  .product-card .product-name {
    font-size: 13px;
    font-weight: 700;
    color: #222;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .product-card .product-specs {
    font-size: 11px;
    color: #888;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .product-card .product-price {
    font-size: 15px;
    font-weight: 700;
    color: #1976d2;
  }
  .product-card.selected .product-price { color: #1565c0; }
  .selected-check {
    display: none;
    position: absolute;
    top: 8px;
    right: 8px;
    background: #1976d2;
    color: #fff;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }
  .product-card.selected .selected-check { display: flex; }
  .product-card-wrap { position: relative; }

  /* RIGHT SIDEBAR */
  .order-sidebar {
    width: 280px;
    min-width: 280px;
    background: #fff;
    border-left: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    padding: 16px;
    gap: 12px;
  }
  .order-sidebar .order-number {
    font-size: 13px;
    font-weight: 700;
    color: #555;
  }
  .order-sidebar .order-number span { color: #1976d2; }
  .order-sidebar label {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    margin-bottom: 4px;
    display: block;
  }
  .order-sidebar select,
  .order-sidebar textarea {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 13px;
    color: #333;
    background: #f9f9f9;
    outline: none;
    transition: border 0.2s;
  }
  .order-sidebar select:focus,
  .order-sidebar textarea:focus {
    border-color: #1976d2;
    background: #fff;
  }
  .selected-items {
    flex: 1;
    overflow-y: auto;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 8px;
    min-height: 80px;
    background: #fafafa;
  }
  .selected-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 12px;
  }
  .selected-item-row:last-child { border-bottom: none; }
  .selected-item-name {
    font-weight: 600;
    color: #333;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px;
  }
  .selected-item-price { color: #1976d2; font-weight: 700; }
  .selected-item-remove {
    background: none;
    border: none;
    color: #e53935;
    cursor: pointer;
    font-size: 14px;
    padding: 0 4px;
  }
  .empty-selection {
    text-align: center;
    color: #bbb;
    font-size: 12px;
    padding: 16px 0;
  }
  .order-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-top: 2px solid #eee;
    font-weight: 700;
    font-size: 15px;
  }
  .order-total .total-amount { color: #1976d2; font-size: 18px; }
  .order-actions { display: flex; gap: 8px; }
  .btn-bill {
    flex: 1;
    background: #f0f4ff;
    border: 1px solid #1976d2;
    color: #1976d2;
    border-radius: 8px;
    padding: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.2s;
  }
  .btn-bill:hover { background: #e3f0ff; }
  .btn-submit {
    flex: 2;
    background: #1976d2;
    border: none;
    color: #fff;
    border-radius: 8px;
    padding: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.2s;
  }
  .btn-submit:hover { background: #1565c0; }
  .btn-submit:disabled { background: #90caf9; cursor: not-allowed; }
  .search-bar { display: flex; gap: 8px; margin-bottom: 12px; }
  .search-bar input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    outline: none;
  }
  .search-bar input:focus { border-color: #1976d2; }
  .payment-fields { display: flex; flex-direction: column; gap: 8px; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('orders.store', withLang()) }}" id="orderForm">
@csrf

<div class="order-create-page">

  {{-- LEFT BRAND SIDEBAR --}}
  <div class="brand-sidebar">
    <button type="button" class="btn-search" data-bs-toggle="modal" data-bs-target="#searchModal">
      <i class='bx bx-search'></i> Search
    </button>

    <div class="brand-item active" data-brand="">
      <div class="brand-placeholder">ALL</div>
      <span>All Phones</span>
    </div>

    @foreach($brands as $brand)
    <div class="brand-item" data-brand="{{ $brand->id }}">
      <div class="brand-placeholder">{{ strtoupper(substr($brand->name, 0, 2)) }}</div>
      <span>{{ $brand->name }}</span>
    </div>
    @endforeach
  </div>

  {{-- MAIN PRODUCT GRID --}}
  <div class="product-area">
    <div class="product-grid" id="productGrid">
      @foreach($products as $product)
      <div class="product-card-wrap">
        <div class="product-card"
          data-id="{{ $product->id }}"
          data-name="{{ $product->product_name }}"
          data-imei="{{ $product->product_imei }}"
          data-price="{{ $product->selling_price }}"
          data-brand="{{ $product->brand_id }}"
          onclick="toggleProduct(this)">

          <div class="selected-check"><i class='bx bx-check'></i></div>

          @if($product->image_name)
            <img class="product-img" src="{{ $product->image_name }}" alt="{{ $product->product_name }}">
          @else
            <div class="product-img-placeholder">
              <i class='bx bx-camera'></i>
              <span>Product Image<br>Coming Soon</span>
            </div>
          @endif

          <div class="product-info">
            <div class="product-name">
              {{ $product->product_name }} [ IMEI: {{ substr($product->product_imei, -4) }} ]
            </div>
            <div class="product-specs">
              {{ $product->condition_name ?? '' }},
              {{ $product->series->name ?? '' }},
              {{ $product->storage->name ?? '' }},
              {{ $product->color->name ?? '' }},
              {{ $product->type_of_machine_name ?? '' }}
            </div>
            <div class="product-price">${{ number_format($product->selling_price, 2) }}</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- RIGHT ORDER SIDEBAR --}}
  <div class="order-sidebar">

    <div class="order-number">
      Order: <span>#{{ str_pad($nextOrderId ?? 0, 5, '0', STR_PAD_LEFT) }}</span>
    </div>

    <div>
      <label><i class='bx bx-user'></i> Customer</label>
      <select name="customer_id" required>
        <option value="">-- Select Customer --</option>
        @foreach($customers as $id => $name)
          <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
      </select>
      @error('customer_id')
        <small class="text-danger">{{ $message }}</small>
      @enderror
    </div>

    <div class="payment-fields">
      <div>
        <label>Payment Type</label>
        <select name="payment_type" required>
          <option value="">-- Select --</option>
          <option value="1">Cash</option>
          <option value="2">Bank</option>
          <option value="3">Other</option>
        </select>
      </div>
      <div>
        <label>Payment Status</label>
        <select name="payment_status" required>
          <option value="">-- Select --</option>
          <option value="1">Paid</option>
          <option value="2">Unpaid</option>
        </select>
      </div>
      <div>
        <label>Note</label>
        <textarea name="note" rows="2" placeholder="Optional note..."></textarea>
      </div>
    </div>

    <div>
      <label>Selected Products</label>
      <div class="selected-items" id="selectedItems">
        <div class="empty-selection" id="emptyMsg">No products selected</div>
      </div>
    </div>

    <div id="hiddenInputs"></div>

    <div class="order-total">
      <span>Total</span>
      <span class="total-amount" id="totalAmount">$ 0</span>
    </div>

    <div class="order-actions">
      <button type="button" class="btn-bill" onclick="window.print()">
        <i class='bx bx-receipt'></i> Bill
      </button>
      <button type="submit" class="btn-submit" id="submitBtn" disabled>
        <i class='bx bx-cart-add'></i> Submit Order
      </button>
    </div>

  </div>
</div>

</form>

{{-- Search Modal --}}
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Search Products</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="search-bar">
          <input type="text" id="searchInput" placeholder="Search by name, IMEI, code..." oninput="filterProducts()">
        </div>
        <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>IMEI</th>
                <th>Brand</th>
                <th>Storage</th>
                <th>Condition</th>
                <th>Price</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($products as $product)
              <tr class="search-row"
                data-name="{{ strtolower($product->product_name) }}"
                data-imei="{{ strtolower($product->product_imei) }}"
                data-code="{{ strtolower($product->product_code) }}">
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->product_imei }}</td>
                <td>{{ $product->brand->name ?? '-' }}</td>
                <td>{{ $product->storage->name ?? '-' }}</td>
                <td>{{ $product->condition_name ?? '-' }}</td>
                <td>${{ number_format($product->selling_price, 2) }}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary"
                    onclick="selectFromSearch({{ $product->id }})">
                    Select
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('script')
<script>
  let selectedProducts = {};


  function toggleProduct(card) {
    const id    = card.dataset.id;
    const name  = card.dataset.name;
    const price = parseFloat(card.dataset.price);

    if (selectedProducts[id]) {
      delete selectedProducts[id];
      card.classList.remove('selected');
    } else {
      selectedProducts[id] = { id, name, price };
      card.classList.add('selected');
    }
    updateOrderSidebar();
  }

  function selectFromSearch(id) {
    const card = document.querySelector('.product-card[data-id="' + id + '"]');
    if (card) {
      toggleProduct(card);
    } else {
      const p = allProducts.find(function(x) { return x.id == id; });
      if (p) {
        if (selectedProducts[id]) {
          delete selectedProducts[id];
        } else {
          selectedProducts[id] = { id: p.id, name: p.name, price: p.price };
        }
        updateOrderSidebar();
      }
    }
  }

  function updateOrderSidebar() {
    const items      = Object.values(selectedProducts);
    const container  = document.getElementById('selectedItems');
    const hiddenInputs = document.getElementById('hiddenInputs');
    const submitBtn  = document.getElementById('submitBtn');

    container.innerHTML    = '';
    hiddenInputs.innerHTML = '';

    if (items.length === 0) {
      container.innerHTML = '<div class="empty-selection">No products selected</div>';
      submitBtn.disabled  = true;
    } else {
      submitBtn.disabled = false;
      items.forEach(function(item) {
        container.innerHTML += '<div class="selected-item-row">'
          + '<span class="selected-item-name" title="' + item.name + '">' + item.name + '</span>'
          + '<span class="selected-item-price">$' + parseFloat(item.price).toFixed(2) + '</span>'
          + '<button type="button" class="selected-item-remove" onclick="removeProduct(' + item.id + ')">'
          + '<i class="bx bx-x"></i></button>'
          + '</div>';

        hiddenInputs.innerHTML += '<input type="hidden" name="productIds[]" value="' + item.id + '">';
      });
    }

    const total = items.reduce(function(sum, i) { return sum + parseFloat(i.price); }, 0);
    document.getElementById('totalAmount').textContent = '$ ' + total.toFixed(2);
  }

  function removeProduct(id) {
    delete selectedProducts[id];
    const card = document.querySelector('.product-card[data-id="' + id + '"]');
    if (card) card.classList.remove('selected');
    updateOrderSidebar();
  }

  document.querySelectorAll('.brand-item').forEach(function(item) {
    item.addEventListener('click', function() {
      document.querySelectorAll('.brand-item').forEach(function(b) {
        b.classList.remove('active');
      });
      this.classList.add('active');
      const brandId = this.dataset.brand;
      document.querySelectorAll('.product-card-wrap').forEach(function(wrap) {
        const card = wrap.querySelector('.product-card');
        if (!brandId || card.dataset.brand == brandId) {
          wrap.style.display = '';
        } else {
          wrap.style.display = 'none';
        }
      });
    });
  });

  function filterProducts() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.search-row').forEach(function(row) {
      const match = row.dataset.name.includes(q)
                 || row.dataset.imei.includes(q)
                 || row.dataset.code.includes(q);
      row.style.display = match ? '' : 'none';
    });
  }
</script>
@endpush