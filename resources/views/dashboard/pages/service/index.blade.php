<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'Service')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Service',
        'currentPage' => 'Service Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = ['service_name', 'duration_minutes', 'price', 'status', 'actions'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="serviceTable" :columns="$columns" :filter="false"></x-lebify-table>
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

<!---------------------------
Scripts
---------------------------->
@push('scripts')
    <script src="{{ asset('js/dashboard/service.js') }}" type="module" defer></script>
@endpush
