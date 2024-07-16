<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'discounted_packages' => $this->discounted_packages,
            'activation' => $this->activation,
            'category_id' => $this->category_id,
            // 'company_id' => $this->company_id,
            'images' => ImageResource::collection($this->images),
        ];
    }
}
