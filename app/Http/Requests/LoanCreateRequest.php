<?php

namespace App\Http\Requests;

use App\Models\DebitCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Loan;

class LoanCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|integer',
            'currency_code' => [
              'required',
              Rule::in(Loan::CURRENCIES),
            ],
            //rule terms only 3 and 6 month
            'terms' => [
              'required',
              Rule::in([3, 6]),
            ],
            'processed_at' => 'required|date',
        ];
    }
}
