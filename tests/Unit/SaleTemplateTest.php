<?php

namespace Tests\Feature;
use App\SaleTermV3;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class SaleTemplateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSaleTemplate()
    {

        $this->withoutExceptionHandling();

        $contracts = factory(Contract::class)->create();

        $this->seeInDatabase('contracts', $contracts->toArray());
    }
}
