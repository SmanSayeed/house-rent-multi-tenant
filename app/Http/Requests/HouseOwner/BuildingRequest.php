<?php

namespace App\Http\Requests\HouseOwner;

use Illuminate\Foundation\Http\FormRequest;

class BuildingRequest extends FormRequest
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
        $buildingId = $this->route('building') ? $this->route('building')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
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
            'name.required' => 'Building name is required.',
            'name.max' => 'Building name must not exceed 255 characters.',
            'address.required' => 'Building address is required.',
            'address.max' => 'Building address must not exceed 1000 characters.',
            'city.required' => 'City is required.',
            'city.max' => 'City must not exceed 255 characters.',
            'state.required' => 'State is required.',
            'state.max' => 'State must not exceed 255 characters.',
            'postal_code.required' => 'Postal code is required.',
            'postal_code.max' => 'Postal code must not exceed 20 characters.',
            'country.max' => 'Country must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 2000 characters.',
        ];
    }
}
