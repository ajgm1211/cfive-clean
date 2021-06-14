<?php

namespace Tests\Feature;
use App\QuoteV2;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class QuotesFclTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_if_exists_Quote()
    {

        $this->withoutExceptionHandling();
        $quote= factory(QuoteV2::class)->create();

        $this->seeInDatabase('quote_v2s',['quote_id' => $quote->quote_id]);
        // $this->seeInDatabase('quote_v2s', ['quote_id' => 'CA-1']);
    }
}