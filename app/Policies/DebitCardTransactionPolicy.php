<?php

namespace App\Policies;

use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * Class DebitCardTransactionPolicy
 */
class DebitCardTransactionPolicy
{
    use HandlesAuthorization;

    /**
     * View a Debit Transaction
     *
     * @param User           $user
     * @param DebitCardTransaction $debitCardTransaction
     *
     * @return bool
     */
    public function view(User $user, DebitCardTransaction $debitCardTransaction): Response
    {
        return $user->is($debitCardTransaction->debitCard->user)
            ? Response::allow()
            : Response::deny('You do not own this debit card transaction.');
    }

    /**
     * Create a Debit card transaction
     *
     * @param User      $user
     * @param DebitCard $debitCard
     *
     * @return bool
     */
    public function create(User $user, DebitCard $debitCard): Response
    {
        return $user->is($debitCard->user) 
            ? Response::allow()
            : Response::deny('You do not own this debit card.');
    }
}
