<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
    public function show($id)
    {
        $application = Application::with('attendance')->findOrFail($id);

        if ($application->user_id != Auth::id()) {
            return redirect()->route('application.list')->with('error', '他のユーザーの申請情報にはアクセスできません。');
        }

        return view('application.show', compact('application'));
    }
}
