<?php

namespace App\Http\Requests;

use App\Models\DebitCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Loan;

class LoanPayPartialRequest extends FormRequest
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
            'loan_id' => [
                'required',
            ],
            'scheduled_repayment_id' => [
                'required',
                'exists:scheduled_repayments,id',
            ],
            'amount' => 'required|numeric|min:1',
            'receive_at' => 'date_format:Y-m-d H:i:s',
        ];
    }
}
