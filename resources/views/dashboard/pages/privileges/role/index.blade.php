<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'Role')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Role',
        'currentPage' => 'Role Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = ['name', 'display_name', 'description', 'Actions'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="roleTable" :columns="$columns" :filter="false"></x-lebify-table>
@endsection


<!---------------------------
Filter Options
---------------------------->


<!---------------------------
Modals
---------------------------->
<x-lebify-modal modal-id="create-modal" size="lg" submit-form-id="createForm" title="Create"></x-lebify-modal>
<x-lebify-modal modal-id="edit-modal" size="lg" submit-form-id="editForm" title="Edit"></x-lebify-modal>
<x-lebify-modal modal-id="show-modal" size="lg" :show-submit-button="false" title="Show"></x-lebify-modal>
<x-lebify-modal modal-id="attach-permissions-modal" size="lg" :show-submit-button="false"
    title="Attach Permissions"></x-lebify-modal>

<!---------------------------
Scripts
---------------------------->
@push('scripts')
    <script src="{{ asset('js/dashboard/privileges/role.js') }}" type="module" defer></script>
@endpush
