@extends('layouts.app')
@push('styles')
@endpush

@section('content')
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="pull-right">
                @can('role-create')
                    <a class="btn btn-outline-primary" href="{{ route('roles.create', withLang()) }}"> <i class='bx bx-plus-circle' ></i> Create New Role</a>
                @endcan
            </div>
        </div>
    </div>
    <!-- List Role Table -->
    <div class="card">
    <h5 class="card-header">List Role</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th style="width: 65%;">Name</th>
                    <th style="width: 25%;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ ++$i }}</td>

                        <td class="fw-semibold">
                            {{ $role->name }}
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">

                                @can('role-list')
                                    <a href="{{ route('roles.show', withLang(['role' => $role->id])) }}"
                                       class="btn btn-icon btn-outline-secondary">
                                        <i class="bx bxs-spreadsheet"></i>
                                    </a>
                                @endcan

                                @can('role-edit')
                                    <a href="{{ route('roles.edit', withLang(['role' => $role->id])) }}"
                                       class="btn btn-icon btn-outline-secondary">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                @endcan

                                @can('role-delete')
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['roles.destroy', withLang(['role' => $role->id])],
                                        'class' => 'd-inline'
                                    ]) !!}

                                        <button type="submit"
                                                class="btn btn-icon btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this role?')">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                    {!! Form::close() !!}
                                @endcan

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
    <!--/ List Role Table -->
</div>
<!-- / Content -->
@endsection
@push('script')
    <script>
        function submitForm(){
            $('.submit-delete').click();
        }
    </script>
@endpush
