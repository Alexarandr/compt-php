<?php

namespace Tests\Feature;

use App\Models\Counter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create default counter for each test
        Counter::create(['id' => 1, 'value' => 0]);
    }

    /** @test */
    public function can_get_counter_value()
    {
        $response = $this->get('/api/counter/count');
        $response->assertStatus(200);
        $response->assertJsonStructure(['value']);
        $response->assertJson(['value' => 0]);
    }

    /** @test */
    public function can_increment_counter()
    {
        $response = $this->post('/api/counter/increment', ['amount' => 5]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['value', 'message']);
        $response->assertJson(['value' => 5]);
    }

    /** @test */
    public function increments_with_default_amount_of_one()
    {
        $response = $this->post('/api/counter/increment');
        $response->assertStatus(200);
        $response->assertJson(['value' => 1]);
    }

    /** @test */
    public function rejects_invalid_increment_amount()
    {
        $response = $this->post('/api/counter/increment', ['amount' => -5]);
        $response->assertStatus(422);
        $response->assertJsonStructure(['error', 'messages']);
    }

    /** @test */
    public function can_reset_counter()
    {
        // First increment
        $this->post('/api/counter/increment', ['amount' => 10]);
        $this->assertEquals(10, Counter::find(1)->value);

        // Then reset
        $response = $this->delete('/api/counter/reset');
        $response->assertStatus(200);
        $response->assertJson(['value' => 0]);
        $this->assertEquals(0, Counter::find(1)->value);
    }

    /** @test */
    public function counter_persists_across_requests()
    {
        $this->post('/api/counter/increment', ['amount' => 3]);
        $this->post('/api/counter/increment', ['amount' => 2]);
        
        $response = $this->get('/api/counter/count');
        $response->assertJson(['value' => 5]);
    }
}
