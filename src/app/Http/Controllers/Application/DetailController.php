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

        // 自分の申請以外は見れないようにする
        if ($application->user_id !== Auth::id()) {
            abort(403, 'この申請情報にアクセスする権限がありません。');
        }

        return view('application.show', compact('application'));
    }
}
