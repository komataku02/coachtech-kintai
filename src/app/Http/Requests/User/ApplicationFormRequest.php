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
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end' => 'nullable|date_format:H:i',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockOut = $this->input('clock_out_time');

            if (!$clockOut) {
                return;
            }

            try {
                $clockOutTime = Carbon::createFromFormat('H:i', $clockOut);
            } catch (\Exception $e) {
                return;
            }

            $breaks = $this->input('breaks', []);
            foreach ($breaks as $id => $break) {
                if (!empty($break['start'])) {
                    try {
                        $start = Carbon::createFromFormat('H:i', $break['start']);
                        if ($start->gt($clockOutTime)) {
                            $validator->errors()->add("breaks.$id.start", '出勤時間もしくは退勤時間が不適切な値です');
                        }
                    } catch (\Exception $e) {
                        // 無視：formatバリデーションがすでに担当
                    }
                }

                if (!empty($break['end'])) {
                    try {
                        $end = Carbon::createFromFormat('H:i', $break['end']);
                        if ($end->gt($clockOutTime)) {
                            $validator->errors()->add("breaks.$id.end", '出勤時間もしくは退勤時間が不適切な値です');
                        }
                    } catch (\Exception $e) {
                        // 無視
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
            'clock_out_time.after_or_equal' => '出勤時間もしくは退勤時間が不適切な値です',
        ];
    }
}
