<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepaymentSchedule extends Model
{
    protected $table='repayment_schedule';

    public function repaymentDetail(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\RepaymentDetail');
    }
}
