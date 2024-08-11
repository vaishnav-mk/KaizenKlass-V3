<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "title" => ["required","string","in:End-Sem,CT-1,CT-2,CT-3,CT-4"],
            "exam_date" =>"nullable|date_format:Y-m-d H:i:s",
            "subject_uuid" => "string|required"
        ];
    }
}
