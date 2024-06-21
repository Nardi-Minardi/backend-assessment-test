<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use Carbon\Carbon;

class DebitCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCards()
    {
        // get /debit-cards
        DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/debit-cards');
        // dd($response->json());

        $response->assertStatus(200);
    }

    public function testCustomerCannotSeeAListOfDebitCardsOfOtherCustomers()
    {
        // get /debit-cards
        DebitCard::factory()->create(); // DebitCard for another user

        $response = $this->getJson('/api/debit-cards');
        // dd($response->json());

        $response->assertStatus(200);
    }

    public function testCustomerCanCreateADebitCard()
    {
        // post /debit-cards
        $response = $this->postJson('api/debit-cards', ['type' => 'visa']);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Debit card created successfully',
                // 'data' => DebitCard::first()->toArray(),
            ]);

        $this->assertCount(1, DebitCard::all());
    }

    public function testCustomerCanSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("api/debit-cards/{$debitCard->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $debitCard->id,
                'number' => $debitCard->number, 
                'type' => $debitCard->type,
                'expiration_date' => Carbon::parse($debitCard->expiration_date)->format('Y-m-d H:i:s'),
                'is_active' => $debitCard->is_active,
            ]);
        
    }

    public function testCustomerCannotSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create(); // DebitCard for another user

        $response = $this->getJson("api/debit-cards/{$debitCard->id}");

        $response->assertStatus(403);
    }

    public function testCustomerCanActivateADebitCard()
    {
        // put api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id, 
            'disabled_at' => now()
        ]);

        $response = $this->putJson("api/debit-cards/{$debitCard->id}", ['is_active' => true]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Debit card updated',
            ]);
    }

    public function testCustomerCanDeactivateADebitCard()
    {
        // put api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("api/debit-cards/{$debitCard->id}", ['is_active' => false]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Debit card updated',
            ]);
    }

    public function testCustomerCannotUpdateADebitCardWithWrongValidation()
    {
        // put api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("api/debit-cards/{$debitCard->id}", ['is_active' => 'invalid_value']);

        $response->assertStatus(422);
    }

    public function testCustomerCanDeleteADebitCard()
    {
        // delete api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("api/debit-cards/{$debitCard->id}");
    
        $response->assertStatus(200);
    
        $this->assertSoftDeleted('debit_cards', ['id' => $debitCard->id]);
    }

    public function testCustomerCannotDeleteADebitCardWithTransaction()
    {
        // delete api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);
        $transaction = DebitCardTransaction::factory()->create(['debit_card_id' => $debitCard->id]);

        $response = $this->deleteJson("api/debit-cards/{$debitCard->id}");

        $response->assertStatus(200);
    }

    // Extra bonus for extra tests :)
}
