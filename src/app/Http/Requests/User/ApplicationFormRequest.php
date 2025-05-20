<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ApplicationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_id' => 'required|exists:attendances,id',
            'note' => 'required|string|max:255',
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
            $starts = $this->input('break_start_times', []);
            $ends = $this->input('break_end_times', []);
            $clockOut = $this->input('clock_out_time');

            $clockOutTime = null;
            if ($clockOut) {
                try {
                    $clockOutTime = Carbon::createFromFormat('H:i', $clockOut);
                } catch (\Exception $e) {
                }
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

                        if ($clockOutTime && ($startTime->gt($clockOutTime) || $endTime->gt($clockOutTime))) {
                            $validator->errors()->add("break_end_times.$i", '出勤時間もしくは退勤時間が不適切な値です');
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

            'note.required' => '備考を入力してください。',
            'note.string' => '備考は文字列で入力してください。',
            'note.max' => '備考は255文字以内で入力してください。',

            'clock_in_time.date_format' => '出勤時間の形式が不正です（例：09:00）。',
            'clock_out_time.date_format' => '退勤時間の形式が不正です（例：18:00）。',
            'clock_out_time.after_or_equal' => '退勤時間は出勤時間より後にしてください。',

            'break_start_times.*.date_format' => '休憩開始時刻の形式が不正です（例：12:00）。',
            'break_end_times.*.date_format' => '休憩終了時刻の形式が不正です（例：13:00）。',
            'note.required' => '備考を入力してください。',
            'note.max' => '備考は255文字以内で入力してください。',
        ];
    }
}
