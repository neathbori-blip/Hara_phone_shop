@extends('layouts.app')
@push('styles')
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Success / Error Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="pull-right">
                @can('role-create')
                    <button type="button" class="btn btn-outline-primary"
                            data-bs-toggle="modal" data-bs-target="#basicModal">
                        <i class='bx bx-plus-circle'></i> {{ __('common.lbl_add_new') }}
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">{{ __('category.model_type.list') }}</h5>
        <div class="table-responsive text-nowrap">
            <table class="table mb-5">
                <thead>
                    <tr>
                        <th class="col-md-5">Product Models</th>
                        <th class="col-md-5">{{ __('report.product.total_product') }}</th>
                        <th class="col-md-5">{{ __('report.sale.total') }}</th>
                        <th class="col-md-5">{{ __('report.loan.total') }}</th>
                        <th class="col-md-5">{{ __('report.stock.instock') }}</th>
                        <th class="col-md-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($model_types as $model_type)
                    <tr>
                        <td>{{ $model_type->name }}</td>
                        <td>{{ $model_type->products_count }}</td>
                        <td>{{ $model_type->products_status_sold_count }}</td>
                        <td>{{ $model_type->products_status_loan_count }}</td>
                        <td>{{ $model_type->products_status_instock_count }}</td>
                        <td>
                            {{-- Edit Button --}}
                            <a href="#"
                               class="btn btn-icon btn-outline-secondary edit-model-type"
                               data-bs-toggle="modal"
                               data-bs-target="#editModelType"
                               data-id="{{ $model_type->id }}"
                               data-value="{{ $model_type->name }}">
                                <span class="tf-icons bx bx-edit-alt"></span>
                            </a>

                            {{-- Delete Button --}}
                            <form action="{{ route('model_type.destroy', ['lang' => app()->getLocale(), 'id' => $model_type->id]) }}"
                                  method="POST"
                                  style="display:inline"
                                  onsubmit="return confirm('តើអ្នកពិតជាចង់លុបមែនទេ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-outline-danger">
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

    {{-- Modal Create --}}
    <div class="col-lg-4 col-md-6">
        <div class="mt-3">
            <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('model_type.store', withLang()) }}" method="post">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('category.model_type.new') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="name" class="form-label">{{ __('category.model_type.models') }}</label>
                                        <input type="text" id="name" name="name"
                                               class="form-control"
                                               placeholder="Ex: ZA, ZP, LLA">
                                    </div>
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
        </div>
    </div>
    {{-- End Modal Create --}}

    {{-- Modal Edit --}}
    <div class="col-lg-4 col-md-6">
        <div class="mt-3">
            <div class="modal fade" id="editModelType" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('model_type.update', withLang()) }}" method="post">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('category.model_type.edit') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-3">
                                        <input type="hidden" id="brand-id" name="id" value="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="brand-name" class="form-label">{{ __('category.model_type.new') }}</label>
                                        <input type="text" id="brand-name" name="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="Ex: ZA, ZP, LLA">
                                    </div>
                                    <div id="myDiv" style="display:none" class="text-danger">{{ __('category.model_type.exists') }}</div>
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
        </div>
    </div>
    {{-- End Modal Edit --}}

</div>
@endsection

@push('script')
<script>
    $(document).ready(function () {

        $('.edit-model-type').click(function () {
            var id   = $(this).attr("data-id");
            var name = $(this).attr("data-value");
            $('#editModelType #brand-id').val(id);
            $('#editModelType #brand-name').val(name);
        });

        $('#editModelType form').submit(function (e) {
            e.preventDefault();
            var name = $('#editModelType #brand-name').val();
            var id   = $('#editModelType #brand-id').val();

            $.ajax({
                url: '{{ route('model_type.update', withLang()) }}',
                method: 'POST',
                data: { name: name, id: id },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.isUnique) {
                        // unique — do nothing
                    } else {
                        $('#editModelType form')[0].submit();
                    }
                },
                error: function () {
                    var div = document.getElementById('myDiv');
                    div.style.display = (div.style.display === 'none' || div.style.display === '') ? 'block' : 'none';
                }
            });
        });

    });
</script>
@endpush