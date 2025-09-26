<?php

namespace App\Http\Requests\HouseOwner;

use Illuminate\Foundation\Http\FormRequest;

class FlatRequest extends FormRequest
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
            'building_id' => ['required', 'exists:buildings,id'],
            'flat_number' => [
                'required',
                'string',
                'max:255',
                'unique:flats,flat_number,NULL,id,building_id,' . request('building_id')
            ],
            'floor' => ['required', 'integer', 'min:0', 'max:100'],
            'rent_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:available,occupied,maintenance'],
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
            'building_id.required' => 'Building is required.',
            'building_id.exists' => 'Selected building does not exist.',
            'flat_number.required' => 'Flat number is required.',
            'flat_number.unique' => 'This flat number already exists in the selected building.',
            'floor.required' => 'Floor number is required.',
            'floor.integer' => 'Floor must be a number.',
            'floor.min' => 'Floor must be at least 0.',
            'floor.max' => 'Floor cannot exceed 100.',
            'rent_amount.required' => 'Rent amount is required.',
            'rent_amount.numeric' => 'Rent amount must be a number.',
            'rent_amount.min' => 'Rent amount must be at least 0.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be available, occupied, or maintenance.',
        ];
    }
}
