<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveMeterRequest extends FormRequest
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
            "serial_number" => "required|regex:/(^S?)(\d+)$/",
            "meter_type" => "required|numeric|min:1|max:2",
            "eac" => "nullable|numeric|min:2000|max:8000",
            "date_installed" => "required|date"
        ];
    }
}
