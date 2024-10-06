<?php

namespace Tests\Unit;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\TestCase;

class ExpenseTest extends TestCase
{
    public function testUserCanCreateExpense()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/expenses', [
            'description' => 'Test Expense',
            'date' => now()->toDateString(),
            'value' => 100.00,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['description' => 'Test Expense'])
        ;
    }
}
