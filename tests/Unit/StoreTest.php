<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTest extends TestCase
{

    /**
     * Ignore middleware for testing propose
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAddStoreSuccess()
    {
        $response = $this->json('POST', '/api/v1/add-store', [
            'name' => 'The Best Food',
            'latitude' => 55.9608884,
            'longitude' => -3.3224186,
            'status' => 'open',
            'store_type' => 'Restaurant',
            'max_distance' => 5
        ]);
        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
            ]);
    }

    public function testAddStoreInvalidLatitude()
    {
        $response = $this->json('POST', '/api/v1/add-store', [
            'name' => 'The Best Food',
            'latitude' => 99999.9608884,
            'longitude' => -3.3224186,
            'status' => 'open',
            'store_type' => 'Restaurant',
            'max_distance' => 5
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
            ]);
    }

}
