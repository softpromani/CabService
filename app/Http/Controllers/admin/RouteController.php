<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Route as RouteModel;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Name', 'field' => 'name', 'formatter' => 'input'],
                ['title' => 'Distance', 'field' => 'distance', 'formatter' => 'number'],
                ['title' => 'Activate', 'field' => 'activate', 'formatter' => 'radio'],
            ];

            $page      = $req->query('page', 1);
            $perPage   = $req->query('size', 10);
            $sortField = $req->query('sort.0.field', 'id');
            $sortOrder = $req->query('sort.0.dir', 'asc');
            $query     = RouteModel::query();
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $data         = $query->paginate($perPage, ['*'], 'page', $page);
            $serialNumber = ($page - 1) * $perPage + 1;
            $data->getCollection()->transform(function ($item) use (&$serialNumber) {
                $item->fare_id = $serialNumber++;
                $action        = ``;
            });
            return response()->json([
                'columns'   => $columns,
                'last_page' => $data->lastPage(),
                'data'      => $data->items(),
                'total'     => $data->total(),
            ]);
        }
        return view('admin.route-setup.index');
    }
    public function store(Request $req)
    {
        $data = $req->validate([
            'name'     => 'required|string|unique:routes,name',
            'distance' => 'required|numeric']);
        RouteModel::create($data);
        return redirect()->back();

    }
}
