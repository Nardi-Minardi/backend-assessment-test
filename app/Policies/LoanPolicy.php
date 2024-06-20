<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LoanPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ?Loan $loan = null): Response
    {
      if ($loan === null) {
        return Response::allow();
      } else {
        return $user->is($loan->user)
            ? Response::allow()
            : Response::deny('You do not own this Loan');
      }
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Loan $loan): Response
    {
        return $user->is($loan->user)
            ? Response::allow()
            : Response::deny('You do not own this Loan');
    }
}
