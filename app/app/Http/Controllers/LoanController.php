<?php

namespace App\Http\Controllers;

use App\Http\Services\LoanService;
use App\Loan;
use App\RepaymentDetail;
use App\RepaymentSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private $loanService ;

    public function __construct(){
       $this->loanService = new LoanService();
    }

    public function registerLoan(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'total' => 'required|numeric',
            'loan_term' => 'required|gt:0',
            'collect_day' => 'required',
            'interest_rate' => 'required|gt:0',
            'disbursement_date' => 'required|date',
        ]);
        if(Carbon::createFromTimeString($request->disbursement_date) < now()) return response()->json(["message" => "invalid Disbursement date"],400);
        $loan = $this->loanService->registerLoan($request->user(),$request->total,$request->loan_term,$request->collect_day,$request->interest_rate,$request->disbursement_date);
        $this->loanService->generateRepaymentSchedule($loan);
        $loan->repaymentSchedules = $loan->repaymentSchedules()->get();
        return response()->json($loan);
    }

    public function approveLoan(Request $request,Loan $loan){
        //@TODO thinking about process for disbursement_date
        if($loan->disbursement_date < now()) return response()->json(["message" => "invalid loan"],400);
        $this->loanService->approve($request->user(),$loan);
        return response()->json(["message" => "Success"]);
    }

    public function repaymentLoan(Request $request,Loan $loan){
        $request->validate([
            'total' => 'required|numeric',
        ]);
        $this->loanService->repaymentPeriod($loan,$request->total);
        return response()->json(["message" => "Success"]);
    }
}
