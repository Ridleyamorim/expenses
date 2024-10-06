<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules()
    {
        return [
            'description' => 'sometimes|required|string|max:191',
            'date' => 'sometimes|required|date|before_or_equal:today',
            'value' => 'sometimes|required|numeric|min:0',
        ];
    }
}
