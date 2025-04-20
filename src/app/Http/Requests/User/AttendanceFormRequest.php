<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceFormRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    // 一旦は update 用ルールをベースに入れておきます
    return [
      'clock_in_time' => 'nullable|date_format:H:i',
      'clock_out_time' => 'nullable|date_format:H:i|after_or_equal:clock_in_time',
      'note' => 'required|string|max:255',
      'break_start_times.*' => 'nullable|date_format:H:i',
      'break_end_times.*' => 'nullable|date_format:H:i',
    ];
  }

  public function messages(): array
  {
    return [
      'clock_in_time.date_format' => '出勤時間の形式が不正です（H:i）',
      'clock_out_time.date_format' => '退勤時間の形式が不正です（H:i）',
      'clock_out_time.after_or_equal' => '退勤時間は出勤時間より後にしてください。',
      'note.required' => '備考を記入してください。',
      'break_start_times.*.date_format' => '休憩開始時刻の形式が不正です（H:i）',
      'break_end_times.*.date_format' => '休憩終了時刻の形式が不正です（H:i）',
    ];
  }
}
