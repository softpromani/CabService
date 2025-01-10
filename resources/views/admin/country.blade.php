@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
<style>
    /*Theme the Tabulator element*/
#example-table-theme{
    background-color:#ccc;
    border: 1px solid #333;
    border-radius: 10px;
}

/*Theme the header*/
#example-table-theme .tabulator-header {
    background-color:#333;
    color:#141313;
}

/*Allow column header names to wrap lines*/
#example-table-theme .tabulator-header .tabulator-col,
#example-table-theme .tabulator-header .tabulator-col-row-handle {
    white-space: normal;
}

/*Color the table rows*/
#example-table-theme .tabulator-tableholder .tabulator-table .tabulator-row{
    color:#fff;
    background-color: #666666;
}

/*Color even rows*/
    #example-table-theme .tabulator-tableholder .tabulator-table .tabulator-row:nth-child(even) {
    background-color: #444;
}
</style>
@endsection
@section('content')

<form action="{{ route('admin.master.country') }}" method="post">
    @csrf
    <div class="card p-3">
        <h4 class="mb-4">Add Country</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Country Name</label>
                <input type="text" class="form-control" name="name" id="examplename" aria-describedby="emailHelp">
            </div>
            <div class="col-md-4">
                <label for="Code" class="form-label">Country Code</label>
                <input type="text" class="form-control" name="code" id="exampleCode">
            </div>
            <div class="col-md-4">
                <label for="sname" class="form-label">Short Name</label>
                <input type="text" class="form-control" name="sname" id="examplesname">
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
});


</script>




@endsection
