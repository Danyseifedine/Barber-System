<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'BusinessHour')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Business Hours',
        'currentPage' => 'Business Hours Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = ['day_of_week', 'open_time', 'close_time', 'is_closed', 'actions'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="businessHourTable" :columns="$columns" :showCheckbox="false" :filter="false" :create="false"></x-lebify-table>
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
    <script src="{{ asset('js/dashboard/businessHour.js') }}" type="module" defer></script>
@endpush
