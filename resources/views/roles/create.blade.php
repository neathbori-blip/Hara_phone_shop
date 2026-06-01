@extends('layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y ">
    <div class="row">

        <div class="col-lg-12 mb-3">
            <a class="btn btn-outline-secondary" href="{{ route('roles.index',withLang())}}" >
                Back
            </a>

            <div class="col-xl">
                <div class="card mb-4">
                <div class="card-header">
                    <h5>Create New Role</h5>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => ['roles.store', withLang()], 'method' => 'POST']) !!}

                    <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter role name" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="">Permission</label>
                    @error('permission')
                    <span class="tex-danger d-block">{{$message}}</span> 
                    @enderror <br>

                    @foreach($groupedPermissions as $group => $permissions)
                        <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px;">

                            {{-- Parent Checkbox --}}
                            <input type="checkbox" id="group-{{ $group }}" onchange="selectAll('{{ $group }}', this)">
                            <lab for="group-{{ $group }}"><b>{{ ucfirst($group) }}</b></lab> <br>

                            {{-- Children Checkboxes --}}
                            <div style="margin-left: 20px;">
                                @foreach($permissions as $value)
                                    <input type="checkbox" class="child {{ $group }}" name="permission[]" id="permission-{{ $value->id }}" value="{{ $value->id }}">
                                    <label for="permission-{{ $value->id }}">{{ $value->name }}</label>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    {{-- Submit --}}
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                        {!! Form::close() !!}

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

            </div>
        </div>

    </div>
</div>