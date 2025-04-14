<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:approved,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => '承認ステータスは必須です。',
            'status.in'       => '承認ステータスは「approved」または「rejected」のいずれかを指定してください。',
        ];
    }
}
