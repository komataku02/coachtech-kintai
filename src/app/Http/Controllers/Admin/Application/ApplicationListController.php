<?php

namespace App\Http\Controllers\Admin\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationListController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルトは「承認待ち」

        $applications = Application::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderByDesc('request_at')
            ->paginate(10);

        return view('admin.application.list', [
            'applications' => $applications,
            'status' => $status,
        ]);
    }
}
