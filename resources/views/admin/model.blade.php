@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>

@endsection
@section('content')

<form action="{{isset($editmodel) ? route('admin.master.updateModel', $editmodel->id) : route('admin.master.model_store') }}" method="post" enctype="multipart/form-data">
    @csrf
    @isset($editmodel)
    @method('PUT')
    @endisset

    <div class="card p-3">
        <h4 class="mb-4">Add Model</h4>

        <div class="row g-3">
            <div class="col-md-4">
                <label for="brand_id" class="mb-2">Brand </label>
                <select name="brand_id" id="brand_id"  class="form-control">
                    <option  selected disabled>Select Brand</option>
                    @foreach ($brands as  $brand)
                    <option value="{{ $brand->id }}"
                        {{ old('brand_id', isset($editmodel) ? $editmodel->brand_id : '') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->brand_name }}
                    </option>

                    @endforeach


                </select>
                {{-- <x-input-box name="brand_id" label="Brand ID"  value="{{ isset($editmodel) ? $editmodel->brand_id : '' }}"/> --}}
            </div>
            <div class="col-md-4">
                <x-input-box name="model_name" label="Model Name"  value="{{ isset($editmodel) ? $editmodel->model_name : '' }}"/>
            </div>

        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"></div>
                <div class="card-body p-0">
                    <div id="example-table-theme" class="table-bordered">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('script-area')

<script>
    $(document).ready(function(){
   var table = new Tabulator("#example-table-theme", {
    layout: "fitColumns",
    theme: "bootstrap4",
    ajaxURL: "{{ route('admin.master.model') }}", // URL for the Laravel controller
    ajaxConfig: "GET", // HTTP request type
    pagination: "remote", // Enable remote pagination
    paginationSize: 10, // Number of rows per page
    paginationSizeSelector: [10, 25, 50, 100], // Page size options
    ajaxResponse: function (url, params, response) {
        this.setColumns(response.columns);
        return response.data; // Return the response data for Tabulator to process
    },

});

document.addEventListener("click", function (e) {
            if (e.target.classList.contains("delete-btn")) {
                var rowId = e.target.getAttribute("data-id"); // Get row ID from data attribute
                alert(rowId);
                if (confirm("Are you sure you want to delete this record?")) {
                    // Send DELETE request to server
                    fetch(`{{ url('admin/master-setup/models/delete') }}/${rowId}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}", // Include CSRF token
                            "Content-Type": "application/json",
                        },
                    })
                        .then((response) => {
                            if (response.ok) {
                                table.deleteRow(rowId); // Remove row from Tabulator table
                                alert("Record deleted successfully!");
                            } else {
                                alert("Failed to delete record. Please try again.");
                            }
                        })
                        .catch((error) => console.error("Error:", error));
                }
            }
        });

        fetch(`{{ url('admin/master-setup/models/update') }}/${rowData.id}`, {
            method: "PUT", // Use PUT for updates
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            body: JSON.stringify({
                name: rowData.name, // Send updated fields
            }),
        })
            .then((response) => {
                if (response.ok) {
                    alert("Record updated successfully!");
                } else {
                    alert("Failed to update record. Please try again.");
                }
            })
            .catch((error) => console.error("Error:", error));
    },
);


</script>

@endsection
