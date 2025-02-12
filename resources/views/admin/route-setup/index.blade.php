@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>

@endsection
@section('content')
<div class="card">
    <div class="card-header">
        Add New Route
    </div>
    <div class="card-body">
        <form action="{{ route('admin.master.route-setup.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-4 mt-3">
                    <x-input-box name="name" required="true"/>
                </div>
                <div class="col-4 mt-3">
                    <x-input-box type="number" name="distance" required="true"/>
                </div>
                <div class="col-4 mt-3">
                    <button type="submit" class="btn btn-primary mt-4">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection