<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AttendanceFormRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'clock_in_time' => 'nullable|date_format:H:i',
      'clock_out_time' => 'nullable|date_format:H:i|after_or_equal:clock_in_time',
      'note' => 'required|string|max:255',
      'break_start_times.*' => 'nullable|date_format:H:i',
      'break_end_times.*' => 'nullable|date_format:H:i',
    ];
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      $starts = $this->input('break_start_times', []);
      $ends = $this->input('break_end_times', []);

      foreach ($starts as $i => $start) {
        $end = $ends[$i] ?? null;

        if ($start && !$end) {
          $validator->errors()->add("break_end_times.$i", '休憩終了時刻を入力してください。');
        }

        if (!$start && $end) {
          $validator->errors()->add("break_start_times.$i", '休憩開始時刻を入力してください。');
        }

        if ($start && $end) {
          try {
            $startTime = Carbon::createFromFormat('H:i', $start);
            $endTime = Carbon::createFromFormat('H:i', $end);
            if ($endTime->lessThanOrEqualTo($startTime)) {
              $validator->errors()->add("break_end_times.$i", '休憩終了は開始より後の時刻にしてください。');
            }
          } catch (\Exception $e) {
          }
        }
      }
    });
  }

  public function messages(): array
  {
    return [
      'clock_in_time.date_format' => '出勤時間の形式が不正です（H:i）',
      'clock_out_time.date_format' => '退勤時間の形式が不正です（H:i）',
      'clock_out_time.after_or_equal' => '退勤時間は出勤時間より後にしてください。',
      'note.required' => '備考を記入してください。',
      'note.max' => '備考は255文字以内で入力してください。',
      'break_start_times.*.date_format' => '休憩開始時刻の形式が不正です（H:i）',
      'break_end_times.*.date_format' => '休憩終了時刻の形式が不正です（H:i）',
    ];
  }
}
