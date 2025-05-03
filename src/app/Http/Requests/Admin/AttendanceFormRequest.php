<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceFormRequest extends FormRequest
{
  public function authorize(): bool
  {
    // 認可を true にしておく（GateやPolicyで別途制御する場合はここで条件付け可能）
    return true;
  }

  public function rules(): array
  {
    return [
      'clock_in_time' => 'nullable|date_format:H:i',
      'clock_out_time' => 'nullable|date_format:H:i|after_or_equal:clock_in_time',
      'break_start_times.*' => 'nullable|date_format:H:i',
      'break_end_times.*' => 'nullable|date_format:H:i',
      'note' => 'required|string|max:255',
    ];
  }

  public function messages(): array
  {
    return [
      'clock_in_time.date_format' => '出勤時刻は「時:分」の形式で入力してください。',
      'clock_out_time.date_format' => '退勤時刻は「時:分」の形式で入力してください。',
      'clock_out_time.after_or_equal' => '退勤時刻は出勤時刻より後である必要があります。',
      'break_start_times.*.date_format' => '休憩開始時刻の形式が不正です。',
      'break_end_times.*.date_format' => '休憩終了時刻の形式が不正です。',
      'note.required' => '備考を記入してください。',
    ];
  }
}
