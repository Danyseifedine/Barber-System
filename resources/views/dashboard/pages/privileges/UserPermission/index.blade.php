<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'User Permission')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'User Permission',
        'currentPage' => 'User Permission Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = ['name', 'email', 'permission', 'actions'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="userPermissionTable" :columns="$columns" :create="false">


        {{-- start Filter Options --}}

    @section('filter-options')

        <label class="form-check form-check-sm form-check-custom form-check-solid">
            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select Status"
                name="permission_id">
                <option></option>
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                @endforeach
            </select>
        </label>
    @endsection

    {{-- End Filter Options --}}

</x-lebify-table>
@endsection


<!---------------------------
Filter Options
---------------------------->


<!---------------------------
Modals
---------------------------->
<x-lebify-modal modal-id="edit-modal" size="lg" submit-form-id="editForm" :show-submit-button="false"
title="Edit"></x-lebify-modal>

<!---------------------------
Scripts
---------------------------->
@push('scripts')
<script src="{{ asset('js/dashboard/privileges/userPermission.js') }}" type="module" defer></script>
@endpush