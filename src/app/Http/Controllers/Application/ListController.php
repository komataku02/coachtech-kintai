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

        $applications = Application::with('attendance')
            ->where('user_id', $user->id)
            ->orderByDesc('request_at')
            ->get();

        return view('application.list', compact('applications'));
    }
}
