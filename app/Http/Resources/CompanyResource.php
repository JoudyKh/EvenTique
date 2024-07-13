<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // هون منرجع الاتربيوت اللي بدنا ياهم 
        return [
            'id' => $this->id,
            'location' => $this->location,
        ];
    }
}
