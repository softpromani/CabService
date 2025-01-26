
$(document).ready(function () {
    
    // Handle the change event for the toggle-status radio button
    $(document).on('change', '.confirmation_alert', function () {
        let Aid = $(this).data('id'); // Get the city ID
        let status = $(this).is(':checked') ? 1 : 0; // Determine the status
        let confirmationMessage = $(this).data('alert_message')?$(this).data('alert_message'):'Are you sure want to make changes';
        let title = $(this).data('alert_title');
        let type = $(this).data('alert_type');
        let status_field=$(this).data('status_field');
        let url=$(this).data('alert_url');
        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: title,
            text: confirmationMessage,
            icon: type,
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Make an AJAX request to update the status
                $.ajax({
                    url: url, // Use your named route
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
                        id: Aid, // City ID
                        status: status, // New status
                        field:status_field,
                    },
                    success: function (response) {
                        if (response.success) {
                            // Show a success alert
                            Swal.fire('Success', response.message, 'success');
                        } else {
                            // Show an error alert
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        // Handle general errors
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    }
                });
            } else {
                // If the user cancels, reset the checkbox to its original state
                $(this).prop('checked', !status);
            }
        });
    });

    // delete confirmation
    $(document).on('click', '.delete_alert', function () {
        let Aid = $(this).data('id'); // Get the city ID
        let confirmationMessage = $(this).data('alert_message')?$(this).data('alert_message'):'Are you sure want to Delete?';
        let title = $(this).data('alert_title');
        let type = $(this).data('alert_type');
        let url=$(this).data('alert_url');
        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: title,
            text: confirmationMessage,
            icon: type,
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Make an AJAX request to update the status
                $.ajax({
                    url: url, // Use your named route
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
                        id: Aid, // City ID
                    },
                    success: function (response) {
                        if (response.success) {
                            // Show a success alert
                            Swal.fire('Success', response.message, 'success');
                            window.location.reload();
                        } else {
                            // Show an error alert
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        // Handle general errors
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    }
                });
            } 
        });
    });
});
