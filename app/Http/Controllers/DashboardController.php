<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Auth, DB;

    class DashboardController extends Controller{

        /** index */
            public function index(Request $request){
                $users = DB::table('users as u')->select(DB::Raw("COUNT(".'u.id'.") as count"))->where(['u.status' => 'active'])->first();
                $products = DB::table('products as p')->select(DB::Raw("COUNT(".'p.id'.") as count"))->first();
                $tasks = DB::table('task as t')->select(DB::Raw("COUNT(".'t.id'.") as count"))->whereRaw("find_in_set(".auth()->user()->id.", t.user_id)")->first();
                $notices = DB::table('notices as n')->select(DB::Raw("COUNT(".'n.id'.") as count"))->first();

                $data = ['users' => $users->count, 'products' => $products->count, 'tasks' => $tasks->count, 'notices' => $notices->count];

                return view('dashboard', ['data' => $data]);
            }
        /** index */
    }