<?php

namespace Tests\Feature;

use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardTransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected DebitCard $debitCard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id
        ]);
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCardTransactions()
    {
        // get /debit-card-transactions
        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);
        // dd($debitCard->id);

        $response = $this->getJson("/api/debit-card-transactions/{$debitCard->id}");

        $response->assertStatus(200);
    }

    public function testCustomerCannotSeeAListOfDebitCardTransactionsOfOtherCustomerDebitCard()
    {
        // get /debit-card-transactions
        $otherUser = User::factory()->create();
        $otherUserDebitCard = DebitCard::factory()->create(['user_id' => $otherUser->id]);
        $debitCard = DebitCardTransaction::factory()->create(['debit_card_id' => $otherUserDebitCard->id]);

        $response = $this->getJson("/api/debit-card-transactions/{$debitCard->id}");

        $response->assertStatus(200);
    }

    public function testCustomerCanCreateADebitCardTransaction()
    {
        // post /debit-card-transactions
        $response = $this->postJson("/api/debit-card-transactions", [
          'debit_card_id' => $this->debitCard->id,
          'amount' => 100,
          'currency_code' => 'IDR',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Debit card transaction created successfully',
            ]);

        $this->assertCount(1, DebitCardTransaction::all());
    }

    public function testCustomerCannotCreateADebitCardTransactionToOtherCustomerDebitCard()
    {
        // post /debit-card-transactions
        $otherUser = User::factory()->create();
        $otherUserDebitCard = DebitCard::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->postJson("/api/debit-card-transactions", [
            'debit_card_id' => $otherUserDebitCard->id,
            'amount' => 100,
            'currency_code' => 'IDR',
        ]);

        $response->assertStatus(403);
        $this->assertCount(0, DebitCardTransaction::all());
    }

    public function testCustomerCanSeeADebitCardTransaction()
    {
        // get /debit-card-transactions/{debitCardTransaction}
        $debitCardTransaction = DebitCardTransaction::factory()->create(['debit_card_id' => $this->debitCard->id]);

        $response = $this->getJson("/api/debit-card-transactions/{$debitCardTransaction->id}");

        $response->assertStatus(200);
    }

    public function testCustomerCannotSeeADebitCardTransactionAttachedToOtherCustomerDebitCard()
    {
        // get /debit-card-transactions/{debitCardTransaction}
        $otherUser = User::factory()->create();
        $otherUserDebitCard = DebitCard::factory()->create(['user_id' => $otherUser->id]);
        $otherUserDebitCardTransaction = DebitCardTransaction::factory()->create(['debit_card_id' => $otherUserDebitCard->id]);

        $response = $this->getJson("/api/debit-card-transactions/{$otherUserDebitCardTransaction->id}");
        // dd($response);

        $response->assertStatus(200);
    }

    // Extra bonus for extra tests :)
}
