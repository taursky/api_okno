<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'ids' => [
                'nullable',
                'string',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],

            'available_for_order' => [
                'nullable',
                'boolean',
            ],

            'manufacturer_id' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'created_from' => [
                'nullable',
                'date',
            ],

            'created_to' => [
                'nullable',
                'date',
                'after_or_equal:created_from',
            ],

            'updated_from' => [
                'nullable',
                'date',
            ],

            'updated_to' => [
                'nullable',
                'date',
                'after_or_equal:updated_from',
            ],

            'lang' => [
                'nullable',
                Rule::in([
                    'ru',
                    'en',
                    'ja',
                    'zh',
                ]),
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'id',
                    'created_at',
                    'updated_at',
                    'price',
                    'reference',
                ]),
            ],

            'direction' => [
                'nullable',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ];
    }
}
