<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules()
    {
        return [
            'description' => 'required|string|max:191',
            'date' => 'required|date|before_or_equal:today',
            'value' => 'required|numeric|min:0',
        ];
    }
}
