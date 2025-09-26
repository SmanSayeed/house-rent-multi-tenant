<?php

namespace App\Http\Requests\HouseOwner;

use Illuminate\Foundation\Http\FormRequest;

class TenantAssignmentRequest extends FormRequest
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
            'tenant_id' => ['required', 'exists:users,id'],
            'flat_id' => ['required', 'exists:flats,id'],
            'building_id' => ['required', 'exists:buildings,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,terminated'],
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
            'tenant_id.required' => 'Tenant is required.',
            'tenant_id.exists' => 'Selected tenant does not exist.',
            'flat_id.required' => 'Flat is required.',
            'flat_id.exists' => 'Selected flat does not exist.',
            'building_id.required' => 'Building is required.',
            'building_id.exists' => 'Selected building does not exist.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date must be today or later.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after start date.',
            'monthly_rent.required' => 'Monthly rent is required.',
            'monthly_rent.numeric' => 'Monthly rent must be a number.',
            'monthly_rent.min' => 'Monthly rent must be at least 0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active, inactive, or terminated.',
        ];
    }
}
