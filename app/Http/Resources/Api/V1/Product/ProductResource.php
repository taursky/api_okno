<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = $request->query('lang', 'ru');

        $translation = $this->langs
            ->firstWhere('lang', $lang)
            ?? $this->langs->firstWhere('lang', 'en')
            ?? $this->langs->first();

        return [
            'id' => $this->id,

            'reference' => $this->reference,

            'name' => $translation?->name,

            'description' => $translation?->description,

            'price' => $this->price,

            'weight' => $this->weight,

            'active' => (bool) $this->active,

            'available_for_order' => (bool) $this->available_for_order,

            'has_option' => (bool) $this->has_option,

            'manufacturer_id' => $this->manufacturer_id,

            'default_category_id' => $this->default_category_id,

            'translations' => $this->whenLoaded(
                'langs',
                fn () => $this->langs
                    ->map(fn ($translation) => [
                        'lang' => $translation->lang,
                        'name' => $translation->name,
                        'description' => $translation->description,
                    ])
                    ->values()
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
