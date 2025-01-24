@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>

@endsection

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"></div>
                <div class="card-body p-0">
                    <div id="example-table" class="table-bordered">

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
    ajaxURL: "{{ route('admin.driver.index') }}", 
    ajaxConfig: "GET", 
    pagination: "remote", 
    paginationSize: 10, 
    paginationSizeSelector: [10, 25, 50, 100], 
    ajaxResponse: function (url, params, response) {
        this.setColumns(response.columns);
        return response.data; 
        console.log(response.data);
    },

});

document.addEventListener("click", function (e) {
            if (e.target.classList.contains("delete-btn")) {
                var rowId = e.target.getAttribute("data-id"); 
                alert(rowId);
                if (confirm("Are you sure you want to delete this record?")) {
                    // Send DELETE request to server
                    fetch(`{{ url('admin/driver/delete') }}/${rowId}`, {
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
        
        fetch(`{{ url('admin/driver/update') }}/${rowData.id}`, {
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
