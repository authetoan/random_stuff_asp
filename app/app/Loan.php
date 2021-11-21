<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loans';

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\User');
    }
    public function repaymentSchedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\RepaymentSchedule');
    }
}
