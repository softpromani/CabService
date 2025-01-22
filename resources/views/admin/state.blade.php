@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>


@endsection
@section('content')

<form action="{{isset($editstate) ? route('admin.master.updateState', $editstate->id) : route('admin.master.state') }}" method="post">
    @csrf
    @isset($editstate)
    @method('PUT')
    @endisset
    <div class="card p-3">
        <h4 class="mb-4">Add State</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="country_id" class="mb-2">Country ID</label>
                <select name="country_id" id="country_id"  class="form-control">
                    <option  selected disabled>Select Country</option>
                    @foreach ($countries as  $country)
                    <option value="{{ $country->id }}"
                        {{ old('country_id', isset($editstate) ? $editstate->country_id : '') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>

                    @endforeach


                </select>
            </div>

            <div class="col-md-4">
                <x-input-box name="state_name" label="State Name" value="{{ isset($editstate) ? $editstate->state_name : '' }}"/>
            </div>

            <div class="col-md-4">
                <x-input-box name="short_name" label="Short Name" value="{{ isset($editstate) ? $editstate->short_name : '' }}"/>
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
                    <div class="card-body">
                        <div id="example-table-theme">

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
    ajaxURL: "{{ route('admin.master.state') }}", // URL for the Laravel controller
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
                    fetch(`{{ url('admin/master-setup/states/delete') }}/${rowId}`, {
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
});
</script>




@endsection
