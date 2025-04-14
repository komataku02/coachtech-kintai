<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'attendance_id' => 'required|exists:attendances,id',
            'request_reason' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_id.required' => '勤怠情報が正しくありません。',
            'attendance_id.exists' => '勤怠情報が存在しません。',
            'request_reason.required' => '申請理由は必須です。',
            'request_reason.string' => '申請理由は文字列で入力してください。',
            'request_reason.max' => '申請理由は500文字以内で入力してください。',
        ];
    }
}
