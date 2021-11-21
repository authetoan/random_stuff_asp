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
        $payload = new \stdClass();
        $payload->total = 100000000;
        $payload->loan_term = 12;
        $payload->collect_day = "monday";
        $payload->interest_rate = "0.1";
        $payload->disbursement_date = "2021-12-01T20:42:34Z";
        $loan = $loanService->registerLoan($user,$payload);
        $this->assertDatabaseHas('loans',['id' => $loan->id,'user_id' => $user->id,"total" => $payload->total,"loan_term" => $payload->loan_term,"collect_day" => $payload->collect_day,"interest_rate" => $payload->interest_rate,"disbursement_date" => Carbon::createFromTimeString($payload->disbursement_date)->toDateString()]);
    }
}
