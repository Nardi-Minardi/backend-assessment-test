<?php

namespace App\Http\Requests;

use App\Models\DebitCard;
use Illuminate\Foundation\Http\FormRequest;

class DebitCardUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_active' => 'required|boolean',
        ];
    }
}
