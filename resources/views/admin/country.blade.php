@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>

@endsection
@section('content')

<form action="{{ isset($editcountry) ? route('admin.master.updateCountry', $editcountry->id) : route('admin.master.country') }}" method="post">
    @csrf
    @isset($editcountry)
    @method('PUT')
    @endisset

    <div class="card p-3">
        <h4 class="mb-4">Add Country</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <x-input-box name="name" label="Country Name" value="{{ isset($editcountry) ? $editcountry->name : '' }}"/>
            </div>
            <div class="col-md-4">
                <x-input-box name="code" label="Country Code" value="{{ isset($editcountry) ? $editcountry->code : '' }}"/>
            </div>
            <div class="col-md-4">
                <x-input-box name="sname" label="Short Name" value="{{ isset($editcountry) ? $editcountry->sname : '' }}"/>
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
    ajaxURL: "{{ route('admin.master.country') }}", // URL for the Laravel controller
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
                    fetch(`{{ url('admin/master-setup/countries/delete') }}/${rowId}`, {
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

        fetch(`{{ url('admin/master-setup/countries/update') }}/${rowData.id}`, {
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
