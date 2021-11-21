<?php
namespace App\Http\Services;


use App\Loan;
use App\RepaymentDetail;
use App\RepaymentSchedule;
use App\User;
use Carbon\Carbon;

class LoanService{

    public function registerLoan(User $user,$payload): Loan
    {
        //@TODO register Loan Process
        //@TODO Add more type of Loan : weekly,monthly,yearly ,
        $loan = new Loan();
        $loan->total = $payload->total;
        $loan->loan_term = $payload->loan_term;
        $loan->collect_day = $payload->collect_day;
        $loan->interest_rate = $payload->interest_rate;
        $loan->disbursement_date = Carbon::createFromTimeString($payload->disbursement_date);
        $user->loans()->save($loan);
        return $loan;
    }

    public function generateRepaymentSchedule($loan): bool
    {
        //@TODO Add more type of repayment schedule ,ex : repayment-schedule-with-declining-balance
        $principal_per_period = $loan->total / $loan->loan_term;
        $interest_per_period = $loan->total * $loan->interest_rate;
        $total_per_period = $principal_per_period+$interest_per_period;
        $total = $loan->total;
        for($i=1;$i<=$loan->loan_term;$i++){
            $total = $total - $principal_per_period;
            $repayment_schedule = new RepaymentSchedule();
            $repayment_schedule->period = $i;
            $repayment_schedule->remaining =round($total);
            $repayment_schedule->principal = $principal_per_period;
            $repayment_schedule->interest = $interest_per_period;
            $repayment_schedule->total = $total_per_period;
            $loan->repaymentSchedules()->save($repayment_schedule);
        }
        return true;
    }

    public function approve(User $user,Loan $loan): bool
    {
        //@TODO Approve process
        //@TODO Add user permission
        $loan->user_approve_id = $user->id;
        $loan->save();
        return true;
    }

    public function getLastRepaymentPeriod(Loan $loan)
    {
        return $loan->repaymentSchedules()->where('is_received_full_payment',0)->orderBy('period')->first();
    }

    public function repaymentPeriod(Loan $loan,$total){
        //@TODO need to add user money account to reduce from it
        $last_repayment_period = $this->getLastRepaymentPeriod($loan);
        if(!($last_repayment_period)) return true;
        $repayment_detail_total = $last_repayment_period->repaymentDetail()->sum('total');
        $period_total_left = $last_repayment_period->total - $repayment_detail_total;
        $total_payment_left = $total - $period_total_left;
        $repaymentDetail = new RepaymentDetail();
        if($total_payment_left <0){
            $repaymentDetail->total = $total;
            $last_repayment_period->repaymentDetail()->save($repaymentDetail);
            return $total_payment_left;
        }
        else
        {
            $repaymentDetail->total = $period_total_left;
            $last_repayment_period->repaymentDetail()->save($repaymentDetail);
            $last_repayment_period->is_received_full_payment =1;
            $last_repayment_period->save();
            $this->repaymentPeriod($loan,$total_payment_left);
        }
    }
}
