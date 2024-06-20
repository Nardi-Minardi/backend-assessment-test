<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ScheduledRepaymentResource;


class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'terms' => $this->terms,
            'currency_code' => $this->currency_code,
            'processed_at' => $this->processed_at,
            'status' => $this->status,
            'scheduled_repayments' => ScheduledRepaymentResource::collection($this->scheduledRepayments),
        ];
    }
}
