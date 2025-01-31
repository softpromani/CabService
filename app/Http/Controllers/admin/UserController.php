<?php
namespace App\Http\Controllers\admin;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $columns = [
                 ['title' => 'ID', 'field' => 'user_id'], 
                 ['title' => 'User Image', 'field' => 'image', 'formatter' => 'html'],
                 ['title' => 'Name', 'field' => 'full_name', 'headerFilter' => "input"],
                 ['title' => 'Email', 'field' => 'email', 'headerFilter' => "input"],
                 ['title' => 'Phone', 'field' => 'phone', 'headerFilter' => "input"],
                 ['title' => 'Role', 'field' => 'role_name', 'headerFilter' => "input"],
                 ['title' => 'Action', 'field' => 'action', 'formatter' => 'html'],
             ];
     
             $query = User::role('User');
     
             $page      = $request->query('page', 1);
             $perPage   = $request->query('size', 10);
             $sortField = $request->query('sort.0.field', 'id');
             $sortOrder = $request->query('sort.0.dir', 'asc');
     
             if ($sortField && $sortOrder) {
                 $query->orderBy($sortField, $sortOrder);
             }
     
             $users = $query->paginate($perPage, ['*'], 'page', $page);
     
             
             $serialNumber = ($page - 1) * $perPage + 1;
     
             $users->getCollection()->transform(function ($item) use (&$serialNumber) {
                 $item->user_id = $serialNumber++; 
     
                 $item->image = $item->user_image
                     ? '<img src="' . Storage::url($item->user_image) . '" alt="User Image" style="height: 40px;">'
                     : '<span class="text-muted">No Image</span>';
     
                 $item->action = '
                 <i class="fa-solid fa-trash text-danger delete_alert" data-id="' . $item->id . '" data-alert_message="Are you sure want to delete Customer?" data-alert_title="Delete"
                     data-alert_type="warning" data-alert_url="' . route('admin.customer.destroy', $item->id) . '"></i>&nbsp;&nbsp;
                 <a href="' . route('admin.userProfile', ['id' => $item->id]) . '" class="btn  btn-sm"><i class="fa-solid fa-eye text-primary"></i> </a>&nbsp;
                 <a href="' . route('admin.editUser', ['id' => $item->id]) . '" class="btn  btn-sm"><i class="fa-solid fa-pen-to-square text-warning"></i> </a>';
     
                 return $item;
             });
     
             return response()->json([
                 'columns'   => $columns,
                 'last_page' => $users->lastPage(),
                 'data'      => $users->items(),
                 'total'     => $users->total(),
             ]);
         }
     
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
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
    }

    public function user_suspend_status(Request $request)
    {
        try {
            $res = User::findOrFail($request->id)->update([$request->field => $request->status]);
            return response()->json(['success' => true], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'something went wrong'], 500);
        }
    }
}
