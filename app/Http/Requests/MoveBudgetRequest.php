<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveBudgetRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'target_budget_id' => 'required|exists:budgets,id|different:budget',
            'remarks' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'target_budget_id.different' => 'The target budget must be different from the source budget.',
        ];
    }
}
