@extends('layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="pull-right">
                @can('role-create')
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#basicModal">
                        <i class='bx bx-plus-circle'></i> {{ __('common.lbl_add_new') }}
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">{{ __('category.series.list') }}</h5>
        <div class="table-responsive text-nowrap">
            <table class="table mb-5">
                <thead>
                    <tr>
                        <th class="col-md-5">Product Storages</th>
                        <th class="col-md-5">{{ __('report.product.total_product') }}</th>
                        <th class="col-md-5">{{ __('report.sale.total') }}</th>
                        <th class="col-md-5">{{ __('report.loan.total') }}</th>
                        <th class="col-md-5">{{ __('report.stock.instock') }}</th>
                        <th class="col-md-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($storages as $storage)
                    <tr>
                        <td>{{ $storage->name }}</td>
                        <td>{{ $storage->products_count }}</td>
                        <td>{{ $storage->products_status_sold_count }}</td>
                        <td>{{ $storage->products_status_loan_count }}</td>
                        <td>{{ $storage->products_status_instock_count }}</td>
                        <td>
                            <a href="#" class="btn btn-icon btn-outline-secondary edit-storages"
                                data-bs-toggle="modal"
                                data-bs-target="#editstorages"
                                data-id="{{ $storage->id }}"
                                data-value="{{ $storage->name }}">
                                <span class="tf-icons bx bx-edit-alt"></span>
                            </a>
                            <form action="{{ route('storage.destroy', array_merge(withLang(), ['id' => $storage->id])) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-icon btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to delete {{ $storage->name }}?')">
                                    <span class="tf-icons bx bx-trash"></span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('storage.store', withLang()) }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('category.add_new') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col mb-3">
                            <label for="name" class="form-label">{{ __('category.storage.product_storages') }}</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Ex: 16GB, 32GB, 1TB">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('button.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('button.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Modal Create -->

    <!-- Modal Edit -->
    <div class="modal fade" id="editstorages" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('category.storage.edit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="storage-id" value="">
                    <div class="col mb-3">
                        <label for="storage-name" class="form-label">{{ __('category.storage.edit') }}</label>
                        <input type="text" id="storage-name" class="form-control" placeholder="Ex: 16GB, 32GB, 1TB">
                    </div>
                    <div id="myDiv" style="display: none" class="text-danger">{{ __('category.storage.exists') }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('button.close') }}</button>
                    <button type="button" class="btn btn-primary" id="save-edit-btn">{{ __('button.save') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Edit -->
</div>
@endsection

@push('script')
<script>
$(document).ready(function () {

    $('.edit-storages').click(function () {
        var id = $(this).data('id');
        var name = $(this).data('value');
        $('#storage-id').val(id);
        $('#storage-name').val(name);
        $('#myDiv').hide();
    });

    $('#save-edit-btn').click(function () {
        var id = $('#storage-id').val();
        var name = $('#storage-name').val();

        $.ajax({
            url: '{{ route('storage.update', withLang()) }}',
            method: 'POST',
            data: { name: name, id: id },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.isUnique) {
                    $('#myDiv').show();
                } else {
                    location.reload();
                }
            },
            error: function () {
                alert('Update failed.');
            }
        });
    });

});
</script>
@endpush