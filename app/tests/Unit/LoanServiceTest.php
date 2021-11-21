<?php

namespace Tests\Unit;

use App\Http\Services\LoanService;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterLoan()
    {
        $user = factory(User::class)->create();
        $loanService = new LoanService();
        $total = 100000000;
        $loan_term = 12;
        $collect_day = "monday";
        $interest_rate = "0.1";
        $disbursement_date = "2021-12-01T20:42:34Z";
        $loan = $loanService->registerLoan($user,$total, $loan_term, $collect_day, $interest_rate, $disbursement_date);
        $this->assertDatabaseHas('loans',['id' => $loan->id,'user_id' => $user->id,"total" => $total,"loan_term" => $loan_term,"collect_day" => $collect_day,"interest_rate" => $interest_rate,"disbursement_date" => Carbon::createFromTimeString($disbursement_date)->toDateString()]);
    }
}
