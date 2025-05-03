<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

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
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_id.required'  => '勤怠情報が正しくありません。',
            'attendance_id.exists'    => '勤怠情報が存在しません。',
            'note.required'           => '備考を入力してください。',
            'note.string'             => '備考は文字列で入力してください。',
            'note.max'                => '備考は255文字以内で入力してください。',
        ];
    }
}
