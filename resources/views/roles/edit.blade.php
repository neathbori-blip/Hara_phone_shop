@extends('layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <a class="btn btn-outline-secondary mb-3"
       href="{{ route('roles.index', withLang()) }}">
        <i class='bx bxs-chevrons-left'></i>&nbsp; Back
    </a>

    <div class="row">
        <div class="col-lg-12 mb-3">

            <div class="col-xl">
                <div class="card mb-4">

                    <div class="card-header">
                        <h5>Edit Role</h5>
                    </div>

                    <div class="card-body">

                        {!! Form::model($role, [
                            'method' => 'PATCH',
                            'route' => ['roles.update', withLang(['role' => $role->id])]
                        ]) !!}

                        {{-- Role Name --}}
                        <div class="mb-3">
                            <label>Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $role->name) }}"
                                placeholder="Enter role name"
                                required
                            >

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Permissions --}}
                        <div class="mb-3">
                            <label>Permission</label>

                            @error('permission')
                                <span class="text-danger d-block">
                                    {{ $message }}
                                </span>
                            @enderror

                            <br>

                            @foreach($groupedPermissions as $group => $permissions)
                                <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px;">

                                    {{-- Parent Checkbox --}}
                                    <input
                                        type="checkbox"
                                        id="group-{{ $group }}"
                                        onchange="selectAll('{{ $group }}', this)"
                                    >

                                    <label for="group-{{ $group }}">
                                        <b>{{ ucfirst($group) }}</b>
                                    </label>

                                    <br>

                                    {{-- Child Permissions --}}
                                    <div style="margin-left: 20px;">
                                        @foreach($permissions as $value)

                                            <input
                                                type="checkbox"
                                                class="child {{ $group }}"
                                                name="permission[]"
                                                id="permission-{{ $value->id }}"
                                                value="{{ $value->id }}"
                                                {{ in_array($value->id, old('permission', $rolePermissions)) ? 'checked' : '' }}
                                            >

                                            <label for="permission-{{ $value->id }}">
                                                {{ $value->name }}
                                            </label>

                                            <br>

                                        @endforeach
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function selectAll(group, parent) {
        document.querySelectorAll('.' + group).forEach(function(child) {
            child.checked = parent.checked;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {

        @foreach($groupedPermissions as $group => $permissions)
            let children{{ $loop->index }} = document.querySelectorAll('.{{ $group }}');
            let parent{{ $loop->index }} = document.getElementById('group-{{ $group }}');

            parent{{ $loop->index }}.checked =
                [...children{{ $loop->index }}].every(cb => cb.checked);
        @endforeach

    });
</script>
@endpush