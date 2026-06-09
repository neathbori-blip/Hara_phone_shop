@extends('layouts.app')

@push('styles')
<style>
  .select2-container .select2-selection--single { height: 44px !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; }
  .select2-container .select2-selection--single .select2-selection__rendered { line-height: 44px !important; padding-left: 14px !important; color: #374151; font-size: 14px; }
  .select2-container .select2-selection--single .select2-selection__arrow { height: 44px !important; }
  .select2-container--open .select2-selection--single { border-color: #696cff !important; box-shadow: 0 0 0 3px rgba(105,108,255,0.1) !important; }
  @keyframes rowIn {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .row-animate { animation: rowIn 0.2s ease; }
</style>
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

  <div class="flex items-center justify-between mb-4">
    <h4 class="text-lg font-bold text-gray-800 mb-0">
      {{ __('order.create_sale') ?? 'Register Sale' }}
    </h4>
  </div>

  <form id="orderForm" action="{{ route('sales.store', withLang()) }}" method="POST">
    @csrf

    <div class="card rounded-xl border border-gray-100 shadow-sm mb-4">
      <div class="card-body p-5">
        <div class="row g-3">

          {{-- Sale Date --}}
          <div class="col-md-6">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">
              {{ __('order.sale_date') ?? 'Sale Date' }}
            </label>
            <input type="date"
                   name="order_date"
                   value="{{ date('Y-m-d') }}"
                   required
                   class="form-control rounded-lg border-gray-200 text-sm"
                   style="height:44px">
          </div>

          {{-- Customer --}}
          <div class="col-md-6">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">
              {{ __('order.customer') ?? 'Customer' }}
            </label>
            <select name="customer_id"
                    id="customerSelect"
                    class="form-select rounded-lg border-gray-200 text-sm">
              <option value="">{{ __('order.walk_in_customer') ?? 'Walk in Customer' }}</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Product Name --}}
          <div class="col-12">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">
              {{ __('order.product_name') ?? 'Product Name' }}
            </label>
            <select id="productSelect" class="form-select rounded-lg border-gray-200 text-sm">
              <option value="">{{ __('order.select_product') ?? 'Select Product' }}</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}"
                        data-name="{{ $product->product_name }}"
                        data-imei="{{ $product->product_imei ?? '-' }}"
                        data-price="{{ $product->selling_price }}"
                        data-detail="{{ $product->condition_name.', '.optional($product->modelType)->name.', '.optional($product->storage)->name.', '.optional($product->color)->name }}">
                  {{ $product->product_name }}
                  @if($product->product_imei) [ IMEI: {{ $product->product_imei }} ] @endif
                </option>
              @endforeach
            </select>
          </div>

        </div>
      </div>
    </div>

    {{-- Card 2: Product Table --}}
    <div class="card rounded-xl border border-gray-100 shadow-sm mb-4">
      <div class="card-body p-0">

        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
          <span class="text-sm font-semibold text-gray-600">
            {{ __('order.order_items') ?? 'Order Items' }}
          </span>
          <span id="itemCount" class="text-xs bg-indigo-50 text-indigo-500 font-semibold px-2.5 py-1 rounded-full">
            0 {{ __('order.items') ?? 'items' }}
          </span>
        </div>

        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr class="bg-gray-50">
                <th class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-3 px-5" style="width:50px">#</th>
                <th class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-3 px-4">
                  {{ __('order.product_imei') ?? 'Product IMEI' }}
                </th>
                <th class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-3 px-4">
                  {{ __('order.product_name') ?? 'Product Name' }}
                </th>
                <th class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-3 px-4">
                  {{ __('order.product_detail') ?? 'Product Detail' }}
                </th>
                <th class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-3 px-4 text-end">
                  {{ __('order.price') ?? 'Price ($)' }}
                </th>
                <th class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-3 px-4 text-center" style="width:80px">
                  {{ __('order.actions') ?? 'Actions' }}
                </th>
              </tr>
            </thead>
            <tbody id="productTableBody">
              <tr id="emptyRow">
                <td colspan="6" class="text-center py-12">
                  <i class='bx bx-package text-4xl text-gray-200 block mb-1'></i>
                  <span class="text-sm text-gray-300">
                    {{ __('common.lbl_no_data') ?? 'NO DATA AVAILABLE' }}
                  </span>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="border-t border-gray-100">
                <td colspan="3" class="py-4 px-5"></td>
                <td class="py-4 px-4 text-end text-sm font-semibold text-gray-500 uppercase tracking-wider">
                  {{ __('order.total') ?? 'Total' }} :
                </td>
                <td class="py-4 px-4 text-end">
                  <span class="text-lg font-extrabold text-indigo-600" id="totalAmount">$0.00</span>
                </td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>

      </div>
    </div>

    {{-- Card 3: Note --}}
    <div class="card rounded-xl border border-gray-100 shadow-sm mb-5">
      <div class="card-body p-5">
        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">
          {{ __('order.note') ?? 'Note' }}
        </label>
        <textarea name="note"
                  rows="4"
                  placeholder="{{ __('order.note_placeholder') ?? 'Add a note...' }}"
                  class="form-control rounded-lg border-gray-200 text-sm resize-none"></textarea>
      </div>
    </div>

    <div id="hiddenInputs"></div>

    <div class="flex items-center gap-3">
      <button type="submit"
              class="btn btn-primary flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold">
        <i class='bx bx-check-circle'></i>
        {{ __('order.submit_order') ?? 'Submit Order' }}
      </button>
      <a href="{{ route('sales.index', withLang()) }}"
         class="btn btn-outline-secondary flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold no-underline">
        <i class='bx bx-x'></i>
        {{ __('button.cancel') ?? 'Cancel' }}
      </a>
    </div>

  </form>
</div>
@endsection

@push('script')
<script>
  let cart = [];

  $(document).ready(function() {
    $('#customerSelect').select2();
    $('#productSelect').select2();

    // ── Select product → add to table
    $('#productSelect').on('change', function () {
      const option = this.options[this.selectedIndex];
      if (!option.value) return;

      const id     = option.value;
      const name   = option.dataset.name;
      const imei   = option.dataset.imei;
      const price  = parseFloat(option.dataset.price);
      const detail = option.dataset.detail;

      if (cart.find(i => i.id === id)) {
        alert('This product is already added.');
        $(this).val('').trigger('change');
        return;
      }

      cart.push({ id, name, imei, price, detail });
      $(this).val('').trigger('change');
      renderTable();
    });
  });

  // ── Remove row
  function removeProduct(id) {
    cart = cart.filter(i => i.id !== id);
    renderTable();
  }

  // ── Render table rows
  function renderTable() {
    const tbody        = document.getElementById('productTableBody');
    const emptyRow     = document.getElementById('emptyRow');
    const hiddenInputs = document.getElementById('hiddenInputs');
    const totalEl      = document.getElementById('totalAmount');
    const itemCount    = document.getElementById('itemCount');

    tbody.querySelectorAll('.product-row').forEach(el => el.remove());
    hiddenInputs.innerHTML = '';

    if (cart.length === 0) {
      emptyRow.style.display = '';
      totalEl.textContent   = '$0.00';
      itemCount.textContent = '0 items';
      return;
    }

    emptyRow.style.display = 'none';
    itemCount.textContent  = cart.length + ' item' + (cart.length > 1 ? 's' : '');
    let total = 0;

    cart.forEach((item, index) => {
      total += item.price;

      const tr = document.createElement('tr');
      tr.className = 'product-row row-animate';
      tr.innerHTML = `
        <td class="py-3 px-5 text-sm text-gray-400">${index + 1}</td>
        <td class="py-3 px-4">
          <span class="text-xs bg-gray-100 text-gray-500 font-mono px-2 py-1 rounded">
            ${item.imei}
          </span>
        </td>
        <td class="py-3 px-4 text-sm font-semibold text-gray-800">${item.name}</td>
        <td class="py-3 px-4 text-xs text-gray-400">${item.detail}</td>
        <td class="py-3 px-4 text-end text-sm font-bold text-gray-800">
          $${item.price.toFixed(2)}
        </td>
        <td class="py-3 px-4 text-center">
          <button type="button"
                  onclick="removeProduct('${item.id}')"
                  class="btn btn-icon btn-outline-danger btn-sm rounded-lg"
                  title="Remove">
            <i class='bx bx-trash'></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);

      const input = document.createElement('input');
      input.type  = 'hidden';
      input.name  = 'productIds[]';
      input.value = item.id;
      hiddenInputs.appendChild(input);
    });

    totalEl.textContent = '$' + total.toFixed(2);
  }

  // ── Validate on submit
  document.getElementById('orderForm').addEventListener('submit', function (e) {
    if (cart.length === 0) {
      e.preventDefault();
      alert('Please add at least one product.');
    }
  });
</script>
@endpush