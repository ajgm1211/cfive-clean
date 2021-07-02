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

    public function test_Store(){
        $inlnadPL = factory(InlandPerLocation::class)->create();

        $this->assertDatabaseHas('inland_per_locations', $inlnadPL->toArray());
    }
}
