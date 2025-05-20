<?php

namespace App\Http\Requests\Admin;

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
      'break_start_times.*' => 'nullable|date_format:H:i',
      'break_end_times.*' => 'nullable|date_format:H:i',
      'note' => 'required|string|max:255',
    ];
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
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

        if ($clockInTime && $clockOutTime && $clockOutTime->lt($clockInTime)) {
          $validator->errors()->add('clock_out_time', '出勤時間もしくは退勤時間が不適切な値です。');
        }
      } catch (\Exception $e) {
      }

      $starts = $this->input('break_start_times', []);
      $ends = $this->input('break_end_times', []);

      foreach ($starts as $i => $start) {
        $end = $ends[$i] ?? null;

        try {
          if ($start && $end) {
            $startTime = Carbon::createFromFormat('H:i', $start);
            $endTime = Carbon::createFromFormat('H:i', $end);

            if ($endTime->lt($startTime)) {
              $validator->errors()->add("break_end_times.$i", '休憩終了は開始より後の時刻にしてください。');
            }

            if ($clockInTime && $startTime->lt($clockInTime)) {
              $validator->errors()->add("break_start_times.$i", '休憩時間が勤務時間外です。');
            }

            if ($clockOutTime && ($startTime->gt($clockOutTime) || $endTime->gt($clockOutTime))) {
              $validator->errors()->add("break_end_times.$i", '休憩時間が勤務時間外です。');
            }
          }
        } catch (\Exception $e) {
        }
      }
    });
  }


  public function messages(): array
  {
    return [
      'clock_in_time.date_format' => '出勤時刻は「時:分」の形式で入力してください。',
      'clock_out_time.date_format' => '退勤時刻は「時:分」の形式で入力してください。',
      'clock_out_time.after_or_equal' => '退勤時刻は出勤時刻より後である必要があります。',
      'break_start_times.*.date_format' => '休憩開始時刻の形式が不正です（H:i）',
      'break_end_times.*.date_format' => '休憩終了時刻の形式が不正です（H:i）',
      'break_end_times.*.after' => '休憩終了は開始より後の時刻にしてください。',
      'note.required' => '備考を記入してください。',
    ];
  }
}
