@extends('layouts.app')

@push('styles')
<style>
  /* ── Reset for full-height POS layout ── */
  .layout-page   { overflow: hidden !important; }
  .content-wrapper { padding: 0 !important; }

  /* ── POS shell ── */
  .pos-wrapper {
    display: flex;
    height: calc(100vh - 64px);
    background: #f0f2f8;
    overflow: hidden;
  }

  /* scrollbar helpers */
  .no-scrollbar::-webkit-scrollbar          { display: none; }
  .thin-scrollbar::-webkit-scrollbar        { width: 4px; }
  .thin-scrollbar::-webkit-scrollbar-thumb  { background: #ccc; border-radius: 4px; }
  .cart-scrollbar::-webkit-scrollbar        { width: 3px; }
  .cart-scrollbar::-webkit-scrollbar-thumb  { background: #e0e0e0; border-radius: 3px; }

  /* fade-slide for cart items */
  @keyframes fadeSlide {
    from { opacity:0; transform:translateY(6px); }
    to   { opacity:1; transform:translateY(0); }
  }
  .animate-fade-slide { animation: fadeSlide .2s ease; }

  /* overlay hover — needs CSS child selector */
  .product-overlay { opacity:0; transition:opacity .18s; }
  .product-card:hover .product-overlay          { opacity:1; }
  .product-card.in-cart .product-overlay        { opacity:1; background:rgba(105,108,255,.25) !important; }

  /* active brand pill */
  .brand-btn.active {
    background: #696cff !important;
    border-color: #696cff !important;
    color: #fff !important;
  }
  .brand-btn.active i,
  .brand-btn.active span { color: #fff !important; }

  /* in-cart border */
  .product-card.in-cart { border-color: #696cff !important; }
</style>
@endpush

@section('content')
<div class="pos-wrapper">

  {{-- ══ LEFT SIDEBAR: brand filter ══ --}}
  <div class="no-scrollbar flex-shrink-0 overflow-y-auto"
       style="width:90px; background:#f0f2f8; display:flex; flex-direction:column; align-items:center; padding:12px 8px; gap:8px;">

    {{-- Search toggle --}}
    <button type="button" id="toggleSearch"
            class="brand-btn"
            style="width:70px; min-height:60px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; background:#fff; border:2px solid transparent; border-radius:10px; cursor:pointer; padding:8px 4px; font-size:11px; font-weight:600; color:#555; transition:all .18s; text-align:center; line-height:1.2;">
      <i class='bx bx-search' style="font-size:20px; color:#888;"></i>
      <span>Search</span>
    </button>

    {{-- All Phones --}}
    <button type="button" class="brand-btn active" data-brand="all"
            style="width:70px; min-height:60px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; border:2px solid transparent; border-radius:10px; cursor:pointer; padding:8px 4px; font-size:11px; font-weight:600; transition:all .18s; text-align:center; line-height:1.2;">
      <i class='bx bx-devices' style="font-size:20px;"></i>
      <span>All Phones</span>
    </button>

    {{-- Per brand --}}
    @foreach($brands as $brand)
    <button type="button" class="brand-btn" data-brand="{{ $brand->id }}"
            style="width:70px; min-height:60px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; background:#fff; border:2px solid transparent; border-radius:10px; cursor:pointer; padding:8px 4px; font-size:11px; font-weight:600; color:#555; transition:all .18s; text-align:center; line-height:1.2;">
      @if($brand->logo)
        <img src="{{ asset('/'.$brand->logo) }}" alt="{{ $brand->name }}" style="width:32px; height:32px; object-fit:contain;">
      @else
        <i class='bx bx-mobile-alt' style="font-size:20px; color:#888;"></i>
      @endif
      <span>{{ $brand->name }}</span>
    </button>
    @endforeach
  </div>

  {{-- ══ MAIN: search + product grid ══ --}}
  <div style="flex:1; display:flex; flex-direction:column; overflow:hidden; padding:12px 10px; gap:10px;">

    {{-- Search bar (hidden by default) --}}
    <div id="searchBar"
         style="display:none; align-items:center; gap:8px; background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:8px 14px; flex-shrink:0;">
      <i class='bx bx-search' style="color:#aaa; font-size:18px;"></i>
      <input type="text" id="searchInput"
             placeholder="Search by name or IMEI..."
             autocomplete="off"
             style="border:none; outline:none; width:100%; font-size:14px; color:#444; background:transparent;">
    </div>

    {{-- Product grid --}}
    <div class="thin-scrollbar"
         id="productGrid"
         style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; overflow-y:auto; padding-right:4px; align-content:start;">

      @forelse($products as $product)
      <div class="product-card"
           data-id="{{ $product->id }}"
           data-name="{{ $product->name }}"
           data-price="{{ $product->selling_price }}"
           data-brand="{{ $product->brand_id }}"
           data-imei="{{ $product->imei ?? '' }}"
           data-img="{{ $product->image ? asset('storage/'.$product->image) : '' }}"
           style="background:#fff; border-radius:10px; overflow:hidden; border:2px solid transparent; cursor:pointer; transition:border-color .18s, box-shadow .18s;">

        <div style="position:relative; width:100%; padding-top:80%; background:#f5f5f5; overflow:hidden;">
          @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
          @else
            <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc; font-size:12px; text-align:center; gap:6px;">
              <i class='bx bx-camera' style="font-size:36px;"></i>
              <span>Product Image<br>Coming Soon</span>
            </div>
          @endif
          <div class="product-overlay"
               style="position:absolute; inset:0; background:rgba(105,108,255,.15); display:flex; align-items:center; justify-content:center;">
            <button type="button" class="overlay-add-btn" onclick="toggleCart(this)"
                    style="background:#696cff; color:#fff; border:none; border-radius:6px; padding:7px 18px; font-size:13px; font-weight:600; cursor:pointer;">
              <i class='bx bx-plus'></i> Add
            </button>
          </div>
        </div>

        <div style="padding:9px 10px 11px;">
          <div style="font-size:13px; font-weight:600; color:#2a2a2a; line-height:1.3;">
            {{ $product->name }}
            @if($product->imei)
              <span style="font-size:12px; font-weight:400; color:#999;">[ IMEI: {{ substr($product->imei, -4) }} ]</span>
            @endif
          </div>
          <div style="font-size:11px; color:#aaa; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            {{ $product->condition ?? 'Used' }},
            {{ optional($product->modelType)->name }},
            {{ optional($product->storage)->name }},
            {{ optional($product->color)->name }},
            {{ $product->grade ?? 'Original' }}
          </div>
          <div style="font-size:15px; font-weight:700; color:#2a2a2a; margin-top:6px;">
            ${{ number_format($product->selling_price, 2) }}
          </div>
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

  {{-- ══ RIGHT PANEL: order summary ══ --}}
  <div style="width:300px; background:#fff; border-left:1px solid #e4e6ea; display:flex; flex-direction:column; flex-shrink:0;">
    <form id="orderForm" action="{{ route('sales.store', withLang()) }}" method="POST"
          style="display:flex; flex-direction:column; height:100%;">
      @csrf

      <div style="display:flex; flex-direction:column; height:100%; padding:14px 14px 0;">

        {{-- Order number --}}
        <div style="text-align:right; font-size:13px; color:#888; margin-bottom:12px; font-weight:500;">
          Order: <span style="color:#2a2a2a; font-weight:700;">#{{ str_pad($nextOrderId, 5, '0', STR_PAD_LEFT) }}</span>
        </div>

        {{-- Customer dropdown --}}
        <div style="display:flex; align-items:center; gap:8px; border:1px solid #e0e0e0; border-radius:8px; padding:8px 10px; margin-bottom:14px;">
          <i class='bx bx-user' style="font-size:20px; color:#aaa; flex-shrink:0;"></i>
          <select name="customer_id" id="customerSelect" required
                  style="flex:1; border:none; outline:none; font-size:14px; color:#444; background:transparent; cursor:pointer;">
            <option value="">{{ __('order.customer') ?? 'Select Customer' }}</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
          </select>
          <i class='bx bx-loader-circle' id="customerLoader" style="font-size:16px; color:#ccc;"></i>
        </div>

        {{-- Cart list --}}
        <div class="cart-scrollbar" id="cartList"
             style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:8px; margin-bottom:10px;">
          <div id="cartEmpty"
               style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc; font-size:13px; text-align:center; gap:8px; padding:30px 0;">
            <i class='bx bx-cart' style="font-size:38px;"></i>
            <p>No items yet.<br><small>Click a product to add.</small></p>
          </div>
        </div>

        {{-- Hidden product inputs --}}
        <div id="hiddenInputs"></div>

        {{-- Total --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-top:1px solid #f0f0f0; font-size:14px; color:#666; font-weight:600; flex-shrink:0;">
          <span>Total</span>
          <span style="font-size:20px; font-weight:800; color:#2a2a2a;">
            $ <span id="totalAmount">0</span>
          </span>
        </div>

      </div>

      {{-- Action buttons --}}
      <div style="display:flex; border-top:1px solid #f0f0f0; flex-shrink:0;">
        <button type="button" onclick="printBill()"
                style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; padding:14px 8px; background:#f5f5f5; border:none; border-right:1px solid #e8e8e8; color:#555; font-size:12px; font-weight:600; cursor:pointer; transition:background .15s;"
                onmouseover="this.style.background='#ebebeb'" onmouseout="this.style.background='#f5f5f5'">
          <i class='bx bx-printer' style="font-size:20px;"></i>
          <span>Bill</span>
        </button>
        <button type="submit" id="submitBtn"
                style="flex:2; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; padding:14px 8px; background:#696cff; border:none; color:#fff; font-size:12px; font-weight:600; cursor:pointer; transition:background .15s;"
                onmouseover="this.style.background='#5558e3'" onmouseout="this.style.background='#696cff'">
          <i class='bx bx-check-shield' style="font-size:20px;"></i>
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
    const visible = bar.style.display === 'flex';
    bar.style.display = visible ? 'none' : 'flex';
    if (!visible) document.getElementById('searchInput').focus();
  });

  // ── Search filter
  document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      const name = card.dataset.name.toLowerCase();
      const imei = card.dataset.imei.toLowerCase();
      card.style.display = (q !== '' && !name.includes(q) && !imei.includes(q)) ? 'none' : '';
    });
  });

  // ── Brand filter
  document.querySelectorAll('.brand-btn[data-brand]').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.brand-btn[data-brand]').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const brand = this.dataset.brand;
      document.querySelectorAll('.product-card').forEach(card => {
        card.style.display = (brand !== 'all' && card.dataset.brand !== brand) ? 'none' : '';
      });
      document.getElementById('searchInput').value = '';
    });
  });

  // ── Toggle add/remove
  function toggleCart(btn) {
    const card = btn.closest('.product-card');
    const id   = card.dataset.id;
    cart.find(i => i.id === id) ? removeFromCart(id) : addToCart(card);
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

    const overlayBtn = card.querySelector('.product-overlay button');
    overlayBtn.className = 'overlay-remove-btn';
    overlayBtn.style.cssText = 'background:#e53935; color:#fff; border:none; border-radius:6px; padding:7px 18px; font-size:13px; font-weight:600; cursor:pointer;';
    overlayBtn.innerHTML = `<i class='bx bx-minus'></i> Remove`;

    renderCart();
  }

  // ── Remove from cart
  function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);

    const card = document.querySelector(`.product-card[data-id="${id}"]`);
    if (card) {
      card.classList.remove('in-cart');
      const overlayBtn = card.querySelector('.product-overlay button');
      overlayBtn.className = 'overlay-add-btn';
      overlayBtn.style.cssText = 'background:#696cff; color:#fff; border:none; border-radius:6px; padding:7px 18px; font-size:13px; font-weight:600; cursor:pointer;';
      overlayBtn.innerHTML = `<i class='bx bx-plus'></i> Add`;
    }

    renderCart();
  }

  // ── Render cart
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
      div.className = 'cart-item animate-fade-slide';
      div.style.cssText = 'display:flex; align-items:center; gap:9px; padding:8px; background:#f8f8ff; border-radius:8px; border:1px solid #ebebff;';
      div.innerHTML = `
        ${item.img
          ? `<img style="width:42px;height:42px;border-radius:6px;object-fit:cover;background:#eee;flex-shrink:0;" src="${item.img}" alt="">`
          : `<div style="width:42px;height:42px;border-radius:6px;background:#eee;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
               <i class='bx bx-camera' style="font-size:18px;color:#bbb;"></i>
             </div>`
        }
        <div style="flex:1;min-width:0;">
          <div style="font-size:12px;font-weight:600;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.name}</div>
          <div style="font-size:13px;color:#696cff;font-weight:700;">$${item.price.toFixed(2)}</div>
        </div>
        <button type="button"
                style="background:none;border:none;color:#e53935;cursor:pointer;padding:3px;border-radius:4px;flex-shrink:0;"
                onclick="removeFromCart('${item.id}')" title="Remove">
          <i class='bx bx-x' style="font-size:18px;"></i>
        </button>
      `;
      list.appendChild(div);

      const input = document.createElement('input');
      input.type  = 'hidden';
      input.name  = 'productIds[]';
      input.value = item.id;
      hiddenInputs.appendChild(input);
    });

    totalEl.textContent = total.toFixed(2);
  }

  // ── Print bill
  function printBill() {
    if (cart.length === 0) { alert('Cart is empty!'); return; }
    window.print();
  }
</script>
@endpush