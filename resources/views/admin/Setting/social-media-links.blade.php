@extends('admin.includes.master')
@section('title', 'Social Media Links')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4 fw-bold"><span class="text-muted fw-light">Business Setup /</span> Social Media</h4>

    <!-- Basic Layout -->
    <div class="row">
        <div class="col-xl-12">
            <div class="mb-4 card">
                <div class="card-body">
                    <form action="{{ isset($editsocialmedia) ? route('admin.setting.socialmedia.update', $editsocialmedia->id) :
                        route('admin.setting.socialmedia.store') }}" method="POST">
                        @csrf
                        @isset($editsocialmedia)
                        @method('PATCH')
                        @endisset
                        <div class="row">
                            <div class="mb-3 col-4">
                                <label class="form-label" for="name">Name</label>
                                <select class="form-control" name="name" >
                                    <option value="" selected disabled>--Select--</option>
                                    <option value="instagram" {{ isset($editsocialmedia->name) ? ($editsocialmedia->name == 'instagram' ? 'selected':''):''}}>Instagram</option>
                                    <option value="facebook" {{ isset($editsocialmedia->name) ? ($editsocialmedia->name == 'facebook' ? 'selected':''):''}}>Facebook</option>
                                    <option value="twitter" {{ isset($editsocialmedia->name) ? ($editsocialmedia->name == 'twitter' ? 'selected':''):''}}>Twitter</option>
                                    <option value="linkedin" {{ isset($editsocialmedia->name) ? ($editsocialmedia->name == 'linkedin' ? 'selected':''):''}}>Linkedin</option>
                                </select>
                            </div>
                            <div class="mb-3 col-8">
                                <label class="form-label" for="link">Social Media Link</label>
                                <input type="text" class="form-control" id="link" name="link"
                                    placeholder="link"  value="{{ isset($editsocialmedia) ? $editsocialmedia->link : '' }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ajax Sourced Server-side -->
    <div class="card">
        <h5 class="card-header">Social Media Links</h5>
        <div class="card-datatable text-nowrap card-body">
            <table class="table ">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Sr.No</th>
                        <th scope="col">Name</th>
                        <th scope="col">Link</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($socialmedias as $socialmedia)
                    <tr>
                        <th scope="row">{{ $loop->index+1 }}</th>
                        <td>{{ $socialmedia->name }}</td>
                        <td>{{ $socialmedia->link }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggle-status"
                                 name="status_{{ $socialmedia->id }}" data-id="{{ $socialmedia->id }}"
                                  {{ $socialmedia->status == 1 ? 'checked' : '' }} />

                              </div>

                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="p-0 btn dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                        href="{{ route('admin.setting.socialmedia.edit', $socialmedia->id) }}"><i
                                            class="ti ti-pencil me-1"></i> Edit</a>

                                    <form action="{{ route('admin.setting.socialmedia.destroy', $socialmedia->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this Social Media?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item" href="javascript:void(0);"><i
                                                class="ti ti-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No data found!</td>
                    </tr>

                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
    <!--/ Ajax Sourced Server-side -->

</div>
@endsection
@section('script-area')
<!-- Page JS -->
<script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
<script>
    $(document).ready(function () {
        // Handle the change event for the toggle-status radio button
        $(document).on('change', '#toggle-status', function () {
            let socialmediaId = $(this).data('id'); // Get the board ID
            let status = $(this).is(':checked') ? 1 : 0; // Determine the status

            // Make an AJAX request to update the status
            $.ajax({
                url: '{{ route("admin.setting.socialmedia.update_status") }}', // Use your named route
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                    id: socialmediaId, // Board ID
                    status: status, // New status
                },
                success: function (response) {
                    if (response.success) {
                        // Show a success alert
                        alert(response.message);
                    } else {
                        // Show an error alert
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr) {
                    // Handle general errors
                    alert('Something went wrong. Please try again.');
                }
            });
        });
    });
</script>
@endsection
@section('script-area')
    <script src="{{ asset('assets/js/forms-selects.js') }}"></script>
    <script src="{{ asset('assets/js/forms-tagify.js') }}"></script>
    <script src="{{ asset('assets/js/forms-typeahead.js') }}"></script>
@endsection
