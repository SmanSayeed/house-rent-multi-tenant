<?php

namespace App\Http\Requests\HouseOwner;

use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
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
            'flat_id' => ['required', 'exists:flats,id'],
            'category_id' => ['required', 'exists:bill_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['required', 'in:pending,paid,overdue'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'flat_id.required' => 'Flat is required.',
            'flat_id.exists' => 'Selected flat does not exist.',
            'category_id.required' => 'Bill category is required.',
            'category_id.exists' => 'Selected bill category does not exist.',
            'title.required' => 'Bill title is required.',
            'title.max' => 'Bill title must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.',
            'due_date.required' => 'Due date is required.',
            'due_date.date' => 'Due date must be a valid date.',
            'due_date.after_or_equal' => 'Due date must be today or later.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be pending, paid, or overdue.',
        ];
    }
}
