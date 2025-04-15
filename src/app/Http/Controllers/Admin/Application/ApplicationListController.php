<?php

namespace App\Http\Controllers\Admin\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;

class ApplicationListController extends Controller
{
    public function index()
    {
        // 全申請を取得（勤怠情報も含める）
        $applications = Application::with('attendance', 'user')
            ->latest('request_at')
            ->get();

        return view('admin.application.list', compact('applications'));
    }
}
