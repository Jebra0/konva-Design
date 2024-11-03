<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'product_name' => 'required|string|max:250',
            'product_price' => 'required|integer',
            'product_quantity' => 'required|integer',

            'options' => 'required|array',
            'options.*.opt_name' => 'required|string',
            'options.*.opt_values' => 'required|array',
            'options.*.opt_values.*' => 'array|size:2',
            'options.*.opt_values.*.value' => 'required|string',
            'options.*.opt_values.*.price' => 'required|integer',
        ];
    }
}
