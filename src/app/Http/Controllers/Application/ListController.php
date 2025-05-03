<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Application;

class ListController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending'); // デフォルトは「承認待ち」

        $applications = Application::with('attendance')
            ->where('user_id', Auth::id())
            ->where('status', $status)
            ->latest('request_at')
            ->paginate(10);

        return view('application.list', compact('applications', 'status'));
    }
}
