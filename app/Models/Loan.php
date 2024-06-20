<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    public const STATUS_DUE = 'due';
    public const STATUS_REPAID = 'repaid';
    public const STATUS_PARTIAL = 'partial';

    public const CURRENCY_SGD = 'SGD';
    public const CURRENCY_VND = 'VND';

    public const CURRENCIES = [
        self::CURRENCY_SGD,
        self::CURRENCY_VND,
    ];

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'terms',
        'currency_code',
        'processed_at',
        'status',
    ];

    /**
     * A Loan belongs to a User
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A Loan has many Scheduled Repayments
     *
     * @return HasMany
     */
    public function scheduledRepayments()
    {
        return $this->hasMany(ScheduledRepayment::class, 'loan_id');
    }

    public function generateScheduledRepayments()
    {
        $terms = $this->terms;
        $amount = $this->amount;
        $currencyCode = $this->currency_code;
        $processedAt = $this->processed_at;

        for ($i = 1; $i <= $terms; $i++) {
            $scheduledRepayment = new ScheduledRepayment();
            $scheduledRepayment->loan_id = $this->id;
            $scheduledRepayment->amount = $amount / $terms;
            $scheduledRepayment->due_date = date('Y-m-d', strtotime($processedAt . " +$i month"));
            $scheduledRepayment->status = ScheduledRepayment::STATUS_DUE;
            $scheduledRepayment->save();
        }
    }

    //update the status of the loan to repaid if all scheduled repayments are repaid
    public function updateLoanStatus()
    {
        if ($this->scheduledRepayments()->where('status', ScheduledRepayment::STATUS_DUE)->count() == 0) {
            $this->status = self::STATUS_REPAID;
            $this->save();
        } else {
            $this->status = self::STATUS_PARTIAL;
            $this->save();
        }
    }
}
