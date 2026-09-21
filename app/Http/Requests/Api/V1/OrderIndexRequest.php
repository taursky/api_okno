<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'created_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'created_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:created_from',
            ],

            'updated_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'updated_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:updated_from',
            ],

            'status' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}
