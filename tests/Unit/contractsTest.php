<?php

namespace Tests\Unit;

use App\Contract;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class contractsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testcontracts()
    {
        $this->withoutExceptionHandling();

        $contracts = factory(Contract::class)->create();

        $this->seeInDatabase('contracts', $contracts->toArray());
    }

    public function testcontractsupdated()
    {
        $model = factory(Contract::class)->create();

        $contracts = Contract::find($model->id);

        $contracts->name = 'TDD CARGOFIVE';

        $contracts->update();

        // $model

        $this->seeInDatabase('contracts', [
            'name' => 'TDD CARGOFIVE',
        ]);
    }
}
