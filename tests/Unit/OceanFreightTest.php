<?php

namespace Tests\Feature;
use App\Rate;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class OceanFreightTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_if_ocean_freight_is_stored()
    {

        $this->withoutExceptionHandling();

        $OceanFreight = factory(Rate::class)->create();

        $this->seeInDatabase('rates', $OceanFreight->toArray());
    }
}