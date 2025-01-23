<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Handle AJAX request
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Driver Image', 'field' => 'image', 'formatter' => 'html'],
                ['title' => 'Name', 'field' => 'full_name', 'headerFilter' => "input"],
                ['title' => 'Email', 'field' => 'email', 'headerFilter' => "input"],
                ['title' => 'Phone', 'field' => 'phone', 'headerFilter' => "input"],
                ['title' => 'Role', 'field' => 'role_name', 'headerFilter' => "input"],
            ];

            // Query drivers
            $query = User::role('User');

            // Pagination and sorting
            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $users = $query->paginate($perPage, ['*'], 'page', $page);

            $users->getCollection()->transform(function ($item) {
                $item->image = $item->user_image
                ? '<img src="' . Storage::url($item->user_image) . '" alt="Driver Image" style="height: 40px;">'
                : '<span class="text-muted">No Image</span>';
                return $item;
            });
            // Return response
            return response()->json([
                'columns'   => $columns,
                'last_page' => $users->lastPage(),
                'data'      => $users->items(),
                'total'     => $users->total(),
            ]);
        }

        // For non-AJAX requests, render the view
        $users = User::role('User')->where('is_profile', 1)->get();
        return view('admin.user.user', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
