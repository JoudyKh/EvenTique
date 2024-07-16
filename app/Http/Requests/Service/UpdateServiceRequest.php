<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'price' => 'numeric',
            'category_id' => 'exists:categories,id',
            'ar_name' => '',
            'en_name' => '',
            'ar_description' => '',
            'en_description' => '',
            'discounted_packages' => 'boolean',
            'images' => 'array',
            'remove_images' => 'array',
            'remove_images.*' => 'exists:images,id,model_id, ' . $this->route('service')->id,
        ];
    }
}
