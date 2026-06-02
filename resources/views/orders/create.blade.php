@extends('layouts.app')

@push('styles')
<style>
  /* ── Reset for full height POS layout ── */
  .layout-page { overflow: hidden !important; }
  .content-wrapper { padding: 0 !important; }

  .pos-wrapper {
    display: flex;
    height: calc(100vh - 64px);
    background: #f0f2f8;
    overflow: hidden;
  }

  /* ══════════════════════════════
     LEFT SIDEBAR — brand pills
  ══════════════════════════════ */
  .pos-sidebar {
    width: 90px;
    background: #f0f2f8;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 8px;
    gap: 8px;
    overflow-y: auto;
    flex-shrink: 0;
  }
  .pos-sidebar::-webkit-scrollbar { display: none; }

  .brand-btn {
    width: 70px;
    min-height: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    background: #fff;
    border: 2px solid transparent;
    border-radius: 10px;
    cursor: pointer;
    padding: 8px 4px;
    font-size: 11px;
    font-weight: 600;
    color: #555;
    transition: all 0.18s;
    text-align: center;
    line-height: 1.2;
  }
  .brand-btn i { font-size: 20px; color: #888; }
  .brand-btn img { width: 32px; height: 32px; object-fit: contain; }
  .brand-btn:hover { border-color: #696cff; color: #696cff; }
  .brand-btn:hover i { color: #696cff; }
  .brand-btn.active { background: #696cff; border-color: #696cff; color: #fff; }
  .brand-btn.active i { color: #fff; }

  /* ══════════════════════════════
     MAIN — product grid
  ══════════════════════════════ */
  .pos-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 12px 10px;
    gap: 10px;
  }

  /* search bar at top of main */
  .pos-search {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 8px 14px;
    flex-shrink: 0;
  }
  .pos-search i { color: #aaa; font-size: 18px; }
  .pos-search input {
    border: none;
    outline: none;
    width: 100%;
    font-size: 14px;
    color: #444;
    background: transparent;
  }

  /* grid */
  .product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    overflow-y: auto;
    padding-right: 4px;
    align-content: start;
  }
  .product-grid::-webkit-scrollbar { width: 4px; }
  .product-grid::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

  .product-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    transition: border-color 0.18s, box-shadow 0.18s;
  }
  .product-card:hover { border-color: #696cff33; box-shadow: 0 2px 12px rgba(105,108,255,0.12); }
  .product-card.in-cart { border-color: #696cff; }
  .product-card.hidden { display: none; }

  /* image */
  .product-img-wrap {
    position: relative;
    width: 100%;
    padding-top: 80%;
    background: #f5f5f5;
    overflow: hidden;
  }
  .product-img-wrap img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .no-image {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 12px;
    text-align: center;
    gap: 6px;
  }
  .no-image i { font-size: 36px; }

  /* click overlay */
  .product-overlay {
    position: absolute;
    inset: 0;
    background: rgba(105,108,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.18s;
  }
  .product-card:hcontent: center;
    color: #ccc;
    font-size: 12px;
    text-align:over .product-overlay { opacity: 1; }
  .product-card.in-cart .product-overlay { opacity: 1; background: rgba(105,108,255,0.25); }
  .overlay-add-btn {
    background: #696cff;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }
  .overlay-remove-btn {
    background: #e53935;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }

  /* info */
  .product-info { padding: 9px 10px 11px; }
  .product-name { font-size: 13px; font-weight: 600; color: #2a2a2a; line-height: 1.3; }
  .product-imei { font-size: 12px; font-weight: 400; color: #999; }
  .product-meta { font-size: 11px; color: #aaa; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .product-price { font-size: 15px; font-weight: 700; color: #2a2a2a; margin-top: 6px; }

  /* ══════════════════════════════
     RIGHT PANEL — order summary
  ══════════════════════════════ */
  .pos-right {
    width: 300px;
    background: #fff;
    border-left: 1px solid #e4e6ea;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
  }

  .pos-right-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 14px 14px 0;
  }

  /* order number */
  .order-num-row {
    text-align: right;
    font-size: 13px;
    color: #888;
    margin-bottom: 12px;
    font-weight: 500;
  }
  .order-num-row span { color: #2a2a2a; font-weight: 700; }

  /* customer */
  .customer-row {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 14px;
  }
  .customer-row i { font-size: 20px; color: #aaa; flex-shrink: 0; }
  .customer-row select {
    flex: 1;
    border: none;
    outline: none;
    font-size: 14px;
    color: #444;
    background: transparent;
    cursor: pointer;
  }

  /* cart */
  .cart-list {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 10px;
  }
  .cart-list::-webkit-scrollbar { width: 3px; }
  .cart-list::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 3px; }

  .cart-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 13px;
    text-align: center;
    gap: 8px;
    padding: 30px 0;
  }
  .cart-empty-state i { font-size: 38px; }

  .cart-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px;
    background: #f8f8ff;
    border-radius: 8px;
    border: 1px solid #ebebff;
    animation: fadeSlide 0.2s ease;
  }
  @keyframes fadeSlide {
    from { opacity:0; transform: translateY(6px); }
    to   { opacity:1; transform: translateY(0); }
  }
  .cart-item-img {
    width: 42px; height: 42px;
    border-radius: 6px;
    object-fit: cover;
    background: #eee;
    flex-shrink: 0;
  }
  .cart-item-no-img {
    width: 42px; height: 42px;
    border-radius: 6px;
    background: #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .cart-item-no-img i { font-size: 18px; color: #bbb; }
  .cart-item-info { flex: 1; min-width: 0; }
  .cart-item-name { font-size: 12px; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .cart-item-price { font-size: 13px; color: #696cff; font-weight: 700; }
  .cart-item-remove {
    background: none; border: none;
    color: #e53935; cursor: pointer;
    padding: 3px; border-radius: 4px;
    flex-shrink: 0;
    transition: background 0.15s;
  }
  .cart-item-remove:hover { background: #ffeaea; }

  /* total row */
  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-top: 1px solid #f0f0f0;
    font-size: 14px;
    color: #666;
    font-weight: 600;
    flex-shrink: 0;
  }
  .total-val {
    font-size: 20px;
    font-weight: 800;
    color: #2a2a2a;
  }

  /* action buttons */
  .pos-actions {
    display: flex;
    gap: 0;
    flex-shrink: 0;
    border-top: 1px solid #f0f0f0;
  }
  .btn-bill-pos {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 14px 8px;
    background: #f5f5f5;
    border: none;
    border-right: 1px solid #e8e8e8;
    color: #555;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
  }
  .btn-bill-pos i { font-size: 20px; }
  .btn-bill-pos:hover { background: #ebebeb; }

  .btn-submit-pos {
    flex: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 14px 8px;
    background: #696cff;
    border: none;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
  }
  .btn-submit-pos i { font-size: 20px; }
  .btn-submit-pos:hover { background: #5558e3; }
  .btn-submit-pos:disabled { background: #b0b2ff; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="pos-wrapper">

  {{-- ── LEFT SIDEBAR: brand filter ── --}}
  <div class="pos-sidebar">

    {{-- Search button --}}
    <button type="button" class="brand-btn" id="toggleSearch">
      <i class='bx bx-search'></i>
      <span>Search</span>
    </button>

    {{-- All Phones --}}
    <button type="button" class="brand-btn active" data-brand="all">
      <i class='bx bx-devices'></i>
      <span>All Phones</span>
    </button>

    {{-- Per brand --}}
    @foreach($brands as $brand)
    <button type="button" class="brand-btn" data-brand="{{ $brand->id }}">
      @if($brand->logo)
        <img src="{{ asset('/'.$brand->logo) }}" alt="{{ $brand->name }}">
      @else
        <i class='bx bx-mobile-alt'></i>
      @endif
      <span>{{ $brand->name }}</span>
    </button>
    @endforeach

  </div>

  {{-- ── MAIN: search + grid ── --}}
  <div class="pos-main">

    {{-- Search bar (hidden by default, toggle on click) --}}
    <div class="pos-search" id="searchBar" style="display:none;">
      <i class='bx bx-search'></i>
      <input type="text" id="searchInput" placeholder="Search by name or IMEI..." autocomplete="off">
    </div>

    {{-- Product Grid --}}
    <div class="product-grid" id="productGrid">
      @forelse($products as $product)
      <div class="product-card"
           data-id="{{ $product->id }}"
           data-name="{{ $product->name }}"
           data-price="{{ $product->selling_price }}"
           data-brand="{{ $product->brand_id }}"
           data-imei="{{ $product->imei ?? '' }}"
           data-img="{{ $product->image ? asset('storage/'.$product->image) : '' }}">

        <div class="product-img-wrap">
          @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
          @else
            <div class="no-image">
              <i class='bx bx-camera'></i>
              <span>Product Image<br>Coming Soon</span>
            </div>
          @endif
          <div class="product-overlay">
            <button type="button" class="overlay-add-btn" onclick="toggleCart(this)">
              <i class='bx bx-plus'></i> Add
            </button>
          </div>
        </div>

        <div class="product-info">
          <div class="product-name">
            {{ $product->name }}
            @if($product->imei)
              <span class="product-imei">[ IMEI: {{ substr($product->imei, -4) }} ]</span>
            @endif
          </div>
          <div class="product-meta">
            {{ $product->condition ?? 'Used' }},
            {{ optional($product->modelType)->name }},
            {{ optional($product->storage)->name }},
            {{ optional($product->color)->name }},
            {{ $product->grade ?? 'Original' }}
          </div>
          <div class="product-price">${{ number_format($product->selling_price, 2) }}</div>
        </div>
      </div>
      @empty
        <div style="grid-column:1/-1; text-align:center; padding:60px; color:#aaa;">
          <i class='bx bx-package' style="font-size:48px;"></i>
          <p>{{ __('common.lbl_no_data') }}</p>
        </div>
      @endforelse
    </div>

  </div>

  {{-- ── RIGHT PANEL: order summary ── --}}
  <div class="pos-right">
    <form id="orderForm" action="{{ route('sales.store', withLang()) }}" method="POST">
      @csrf

      <div class="pos-right-inner">

        {{-- Order number --}}
        <div class="order-num-row">
          Order: <span>#{{ str_pad($nextOrderId, 5, '0', STR_PAD_LEFT) }}</span>
        </div>

        {{-- Customer dropdown --}}
        <div class="customer-row">
          <i class='bx bx-user'></i>
          <select name="customer_id" id="customerSelect" required>
            <option value="">{{ __('order.customer') ?? 'Select Customer' }}</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
          </select>
          <i class='bx bx-loader-circle' id="customerLoader" style="font-size:16px;color:#ccc;"></i>
        </div>

        {{-- Cart items list --}}
        <div class="cart-list" id="cartList">
          <div class="cart-empty-state" id="cartEmpty">
            <i class='bx bx-cart'></i>
            <p>No items yet.<br><small>Click a product to add.</small></p>
          </div>
        </div>

        {{-- Hidden product inputs --}}
        <div id="hiddenInputs"></div>

        {{-- Total --}}
        <div class="total-row">
          <span>Total</span>
          <span class="total-val">$ <span id="totalAmount">0</span></span>
        </div>

      </div>

      {{-- Action buttons --}}
      <div class="pos-actions">
        <button type="button" class="btn-bill-pos" onclick="printBill()">
          <i class='bx bx-printer'></i>
          <span>Bill</span>
        </button>
        <button type="submit" class="btn-submit-pos" id="submitBtn">
          <i class='bx bx-check-shield'></i>
          <span>Submit Order</span>
        </button>
      </div>

    </form>
  </div>

</div>
@endsection

@push('script')
<script>
  let cart = [];

  // ── Toggle search bar
  document.getElementById('toggleSearch').addEventListener('click', function () {
    const bar = document.getElementById('searchBar');
    const visible = bar.style.display !== 'none';
    bar.style.display = visible ? 'none' : 'flex';
    if (!visible) document.getElementById('searchInput').focus();
  });

  // ── Search filter
  document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      const name = card.dataset.name.toLowerCase();
      const imei = card.dataset.imei.toLowerCase();
      card.classList.toggle('hidden', q !== '' && !name.includes(q) && !imei.includes(q));
    });
  });

  // ── Brand filter
  document.querySelectorAll('.brand-btn[data-brand]').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.brand-btn[data-brand]').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const brand = this.dataset.brand;
      document.querySelectorAll('.product-card').forEach(card => {
        card.classList.toggle('hidden', brand !== 'all' && card.dataset.brand !== brand);
      });
      // clear search
      document.getElementById('searchInput').value = '';
    });
  });

  // ── Toggle add/remove from card overlay
  function toggleCart(btn) {
    const card = btn.closest('.product-card');
    const id   = card.dataset.id;

    if (cart.find(i => i.id === id)) {
      removeFromCart(id);
    } else {
      addToCart(card);
    }
  }

  // ── Add to cart
  function addToCart(card) {
    const id    = card.dataset.id;
    const name  = card.dataset.name;
    const price = parseFloat(card.dataset.price);
    const imei  = card.dataset.imei;
    const img   = card.dataset.img;

    cart.push({ id, name, price, imei, img });
    card.classList.add('in-cart');

    // change overlay button to Remove
    const overlayBtn = card.querySelector('.product-overlay button');
    overlayBtn.className = 'overlay-remove-btn';
    overlayBtn.innerHTML = `<i class='bx bx-minus'></i> Remove`;

    renderCart();
  }

  // ── Remove from cart
  function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);

    // reset card state
    const card = document.querySelector(`.product-card[data-id="${id}"]`);
    if (card) {
      card.classList.remove('in-cart');
      const overlayBtn = card.querySelector('.product-overlay button');
      overlayBtn.className = 'overlay-add-btn';
      overlayBtn.innerHTML = `<i class='bx bx-plus'></i> Add`;
    }

    renderCart();
  }

  // ── Render cart list
  function renderCart() {
    const list         = document.getElementById('cartList');
    const emptyState   = document.getElementById('cartEmpty');
    const hiddenInputs = document.getElementById('hiddenInputs');
    const totalEl      = document.getElementById('totalAmount');

    list.querySelectorAll('.cart-item').forEach(el => el.remove());
    hiddenInputs.innerHTML = '';

    if (cart.length === 0) {
      emptyState.style.display = 'flex';
      totalEl.textContent = '0';
      return;
    }

    emptyState.style.display = 'none';
    let total = 0;

    cart.forEach(item => {
      total += item.price;

      const div = document.createElement('div');
      div.className = 'cart-item';
      div.innerHTML = `
        ${item.img
          ? `<img class="cart-item-img" src="${item.img}" alt="">`
          : `<div class="cart-item-no-img"><i class='bx bx-camera'></i></div>`
        }
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-price">$${item.price.toFixed(2)}</div>
        </div>
        <button type="button" class="cart-item-remove" onclick="removeFromCart('${item.id}')" title="Remove">
          <i class='bx bx-x' style="font-size:18px"></i>
        </button>
      `;
      list.appendChild(div);

      const input   = document.createElement('input');
      input.type    = 'hidden';
      input.name    = 'productIds[]';
      input.value   = item.id;
      hiddenInputs.appendChild(input);
    });

    totalEl.textContent = total.toFixed(2);
  }

  // ── Print bill
  function printBill() {
    if (cart.length === 0) {
      alert('Cart is empty!');
      return;
    }
    window.print();
  }
</script>
@endpush