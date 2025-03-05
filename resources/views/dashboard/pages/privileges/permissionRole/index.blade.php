<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'Permission Role')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Permission Role',
        'currentPage' => 'Permission Role Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = [];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="permissionRoleTable" :columns="$columns" :create="false" :selected="false" :filter="false"
        :showCheckbox="false">
    </x-lebify-table>
@endsection


<!---------------------------
Filter Options
---------------------------->

@push('scripts')
    <script src="{{ asset('js/dashboard/privileges/permissionRole.js') }}" type="module" defer></script>
@endpush