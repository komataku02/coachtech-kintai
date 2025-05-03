<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;

class StaffListController extends Controller
{
    public function index()
    {
        $staff = User::where('role', 'user')->get();
        return view('admin.staff.index', compact('staff'));
    }
}
