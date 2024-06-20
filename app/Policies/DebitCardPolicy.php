<?php

namespace App\Policies;

use App\Models\DebitCard;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * Class DebitCardPolicy
 */
class DebitCardPolicy
{
    use HandlesAuthorization;

    /**
     * View Debit cards or a specific Debit Card
     *
     * @param User           $user
     * @param DebitCard|null $debitCard
     *
     * @return bool
     */
    public function view(User $user, ?DebitCard $debitCard = null): Response
    {
        if (!$debitCard) {
            return Response::allow();
        }

        return $user->is($debitCard->user)
            ? Response::allow()
            : Response::deny('You do not own this Debit Card');
    }

    /**
     * Create a Debit card
     *
     * @param User  $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * View Debit cards or a specific Debit Card
     *
     * @param User           $user
     * @param DebitCard $debitCard
     *
     * @return bool
     */
    public function update(User $user, DebitCard $debitCard): Response
    {
        return $user->is($debitCard->user) 
            ? Response::allow()
            : Response::deny('You do not own this Debit Card');
    }

    /**
     * View Debit cards or a specific Debit Card
     *
     * @param User           $user
     * @param DebitCard $debitCard
     *
     * @return bool
     */
    public function delete(User $user, DebitCard $debitCard): Response
    {
        return $user->is($debitCard->user)
            // && $debitCard->debitCardTransactions()->doesntExist() 
            ? Response::allow()
            : Response::deny('You do not own this Debit Card');
    }
}
