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
      $clockIn = $this->input('clock_in_time');
      $clockOut = $this->input('clock_out_time');

      $clockInTime = $clockOutTime = null;

      try {
        if ($clockIn) {
          $clockInTime = Carbon::createFromFormat('H:i', $clockIn);
        }
        if ($clockOut) {
          $clockOutTime = Carbon::createFromFormat('H:i', $clockOut);
        }
      } catch (\Exception $e) {
      }

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

            if ($clockInTime && $startTime->lt($clockInTime)) {
              $validator->errors()->add("break_start_times.$i", '休憩時間が勤務時間外です。');
            }

            if ($clockOutTime && ($startTime->gt($clockOutTime) || $endTime->gt($clockOutTime))) {
              $validator->errors()->add("break_end_times.$i", '休憩時間が勤務時間外です。');
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
      'attendance_id.required' => '勤怠情報が正しくありません。',
      'attendance_id.exists' => '勤怠情報が存在しません。',

      'note.required' => '備考を記入してください',
      'note.string' => '備考は文字列で入力してください',
      'note.max' => '備考は255文字以内で入力してください',

      'clock_in_time.date_format' => '出勤時間の形式が不正です（例：09:00）',
      'clock_out_time.date_format' => '退勤時間の形式が不正です（例：18:00）',
      'clock_out_time.after_or_equal' => '出勤時間もしくは退勤時間が不適切な値です',

      'break_start_times.*.date_format' => '休憩開始時刻の形式が不正です（例：12:00）',
      'break_end_times.*.date_format' => '休憩終了時刻の形式が不正です（例：13:00）',
    ];
  }
}
