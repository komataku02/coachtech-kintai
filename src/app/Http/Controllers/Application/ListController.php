<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;

class ListController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $applications = Application::where('user_id', Auth::id())
            ->with('attendance') // 勤怠情報も一緒に取得
            ->latest('request_at') // 新しい申請順
            ->get();

        return view('application.list', compact('applications'));
    }
}
