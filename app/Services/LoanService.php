<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\ScheduledRepayment;
use App\Models\User;

class LoanService
{
    /**
     * Create a Loan
     *
     * @param  User  $user
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  int  $terms
     * @param  string  $processedAt
     *
     * @return Loan
     */
    public function createLoan(User $user, int $amount, string $currencyCode, int $terms, string $processedAt): Loan
    {
        //create a new loan
        $loan = new Loan();
        $loan->user_id = $user->id;
        $loan->amount = $amount;
        $loan->currency_code = $currencyCode;
        $loan->terms = $terms;
        $loan->processed_at = $processedAt;
        $loan->status = Loan::STATUS_DUE;
        $loan->save();

        //generate scheduled repayments for the loan
        $loan->generateScheduledRepayments();

        return $loan;
    }

    /**
     * Repay Scheduled Repayments for a Loan
     *
     * @param  Loan  $loan
     * @param  int  $amount
     * @param  string  $receivedAt
     * @param  string  $scheduledRepaymentId
     *
     * @return ReceivedRepayment
     */
    public function repayLoan(Loan $loan, int $amount, string $receivedAt, string  $scheduledRepaymentId): ReceivedRepayment
    {
        $scheduled_repayment= $loan->scheduledRepayments()
            ->where('loan_id', $loan->id)
            ->where('id', $scheduledRepaymentId)
            ->where('status', ScheduledRepayment::STATUS_DUE)
            ->first();

        //update the status of the scheduled repayments
        $loan->scheduledRepayments()
        ->where('loan_id', $loan->id)
        ->where('id', $scheduled_repayment->id)
        ->where('status', ScheduledRepayment::STATUS_DUE)
        ->update(['status' => ScheduledRepayment::STATUS_REPAID]);

        //update the status of the loan
        $loan->updateLoanStatus();
        
        //create a new received repayment
        $receivedRepayment = new ReceivedRepayment();
        $receivedRepayment->loan_id = $loan->id;
        $receivedRepayment->scheduled_repayment_id = $scheduled_repayment->id;
        $receivedRepayment->amount = $amount;
        $receivedRepayment->received_at = $receivedAt;
        $receivedRepayment->save();

        return $receivedRepayment;
    }

    /**
     * Repay a Loan
     *
     * @param  Loan  $loan
     * @param  int  $amount
     * @param  string  $receivedAt
     *
     * @return ReceivedRepayment
     */

    public function repayAllLoan(Loan $loan, int $amount, string $receivedAt): ReceivedRepayment
    {
        //update the status of the scheduled repayments
        $loan->scheduledRepayments()
            ->where('loan_id', $loan->id)
            ->where('status', ScheduledRepayment::STATUS_DUE)
            ->update(['status' => ScheduledRepayment::STATUS_REPAID]);

        //update the status of the loan
        $loan->status = Loan::STATUS_REPAID;
        $loan->save();

        //create a new received repayment
        $receivedRepayment = new ReceivedRepayment();
        $receivedRepayment->loan_id = $loan->id;
        $receivedRepayment->amount = $amount;
        $receivedRepayment->received_at = $receivedAt;
        $receivedRepayment->save();

        return $receivedRepayment;
    }
}
