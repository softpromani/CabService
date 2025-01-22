@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection
@section('content')

<form action="{{isset($editcity) ? route('admin.master.updateCity', $editcity->id) : route('admin.master.city') }}" method="post">
    @csrf
    @isset($editcity)
    @method('PUT')
    @endisset
    <div class="card p-3">
        <h4 class="mb-4">Add City</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="state_id" class="mb-2">State </label>
                <select name="state_id" id="state_id"  class="form-control">
                    <option  selected disabled>Select State</option>
                    @foreach ($states as  $state)
                    <option value="{{ $state->id }}"
                        {{ old('state_id', isset($editcity) ? $editcity->state_id : '') == $state->id ? 'selected' : '' }}>
                        {{ $state->state_name }}
                    </option>

                    @endforeach

                </select>
            </div>
            <div class="col-md-4">
                <x-input-box name="city_name" label="City Name" value="{{ isset($editcity) ? $editcity->city_name : '' }}"/>
            </div>

            <div class="col-md-4">
                <x-input-box name="pin_code" label="Pin Code" value="{{ isset($editcity) ? $editcity->pin_code : '' }}"/>
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
                        <div id="example-table">

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
   var table = new Tabulator("#example-table", {
    ajaxURL: "{{ route('admin.master.city') }}", // URL for the Laravel controller
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
                    fetch(`{{ url('admin/master-setup/cities/delete') }}/${rowId}`, {
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
