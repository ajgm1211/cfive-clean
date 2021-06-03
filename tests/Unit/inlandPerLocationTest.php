<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\InlandPerLocation;
use Illuminate\Support\Facades\Auth;

class inlandPerLocationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_Store(){
        $inlnadPL = factory(InlandPerLocation::class)->create();

        $this->assertDatabaseHas('inland_location', $inlnadPL->toArray());
    }
}
