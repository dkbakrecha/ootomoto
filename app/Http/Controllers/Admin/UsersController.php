<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffCredential;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::latest()
                ->where('user_type', '=', 2)
                ->where(function ($query) {
                    $query->where('status', '=', 1)
                    ->orWhere('status', '=', 3);
                })
                ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        User::find($user->id)->delete();

        return redirect('admin/users')->with('success', 'User has been deleted Successfully');
    }

    /**
     * Function to list provider
     */
    public function providers() {
        $users = User::latest()
                ->leftJoin('areas', 'areas.id', '=', 'users.area_id')
                ->select('users.*', 'areas.name as area_name')
                ->where('user_type', '=', 0)
                ->where(function ($query) {
                    $query->where('status', '=', 1)
                    ->orWhere('status', '=', 3);
                })
                ->get();
        //prd($services);
        return view('admin.users.providers', compact('users'));
    }

    /**
     * Function to Supervisor
     */
    public function supervisors() {
        $users = User::latest()
                ->where('user_type', '=', 1)
                ->where('isAdmin', '=', 1)
                ->where(function ($query) {
                    $query->where('status', '=', 1)
                    ->orWhere('status', '=', 3);
                })
                ->with(['shop'])
                ->get();
        //prd($users);
        return view('admin.users.supervisors', compact('users'));
    }

    /**
     * Function to Supervisor 
     */
    public function staff_list(Request $request) {
        $shop_id = $request->id;
        $users = User::latest()
                ->where('user_type', '=', 1)
                ->where('isAdmin', '=', 0)
                ->where('shop_id', '=', $shop_id)
                ->where(function ($query) {
                    $query->where('status', '=', 1)
                    ->orWhere('status', '=', 3);
                })
                ->pluck('name', 'id');

        return response()->json(['data' => $users]);
    }

    //Add new supervisor form admin
    public function supervisor_store(Request $request) {
        //prd($request->all());
        $staff = User::findOrFail($request->staff_id);
        $staff->isAdmin = 1;
        $_password = "@stf#" . mt_rand(1000, 9999);
        $staff->password = Hash::make($_password);
        $staff->status = 1;
        $staff->save();

        $staffArr = array();
        $staffArr['name'] = $staff->name;
        $staffArr['email'] = $staff->email;
        $staffArr['password'] = $_password;

        Mail::to($staff->email)->send(new StaffCredential($staffArr));

        return response()->json(['success' =>  __('messages.supervisor_add_success')]);
    }

    //Add new supervisor form admin
    public function supervisor_update(Request $request) {
        //prd($request->all());
        $staff = User::findOrFail($request->id);
        $msg = 'There is nothing to change.';
        if (!isset($request->isAdmin)) {
            $staff->isAdmin = 0;
            $staff->status = 3;
            $msg =  __('messages.supervisor_remove_success');
        }

        $staff->save();

        return response()->json(['success' => $msg]);
    }

    public function getSupervisor(Request $request) {
        $supervisorID = $request->id;
        $supData = User::findOrFail($supervisorID);

        return response()->json(['data' => $supData]);
    }

}
