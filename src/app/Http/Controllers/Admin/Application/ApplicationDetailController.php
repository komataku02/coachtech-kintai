<?php

namespace App\Http\Controllers\Admin\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationDetailController extends Controller
{
    public function show($id)
    {
        $application = Application::with(['user', 'attendance'])->findOrFail($id);

        return view('admin.application.show', compact('application'));
    }

    public function approve(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        // すでに承認済みの場合はリダイレクト
        if ($application->status === 'approved') {
            return redirect()->route('admin.application.detail', $id)
                ->with('message', 'この申請はすでに承認されています。');
        }
        $application->status = 'approved';
        $application->approved_at = now();
        $application->save();

        return redirect()->route('admin.application.list',)->with('message', '申請を承認しました');
    }
}
