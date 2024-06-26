<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ScheduledRepaymentResource extends JsonResource
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
            'due_date' => $this->due_date,
            'amount' => $this->amount,
            'status' => $this->status,
        ];
    }
}
