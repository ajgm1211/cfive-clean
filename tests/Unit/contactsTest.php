<?php

// namespace Tests\Unit;

// use Tests\TestCase;
// use App\Contact;
// use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\DatabaseTransactions;

// class ContacsTest extends TestCase
// {
//     use DatabaseTransactions;

//     /**
//      * A basic test example.
//      *
//      * @return void
//      */
//     public function testcontacts()
//     {
//         $contacts = factory(Contact::class)->create();
//         $this->seeInDatabase('contacts', $contacts->toArray());
//     }


//     public function testcontactsupdated()
//     {
//         $model = factory(Contact::class)->create();

//         $contacts = Contact::find($model->id);

//         $contacts->first_name = 'luisito';

//         $contacts->update();

//         // $model

//         $this->seeInDatabase('contacts', [
//             'first_name' => 'luisito'
//         ]);
//     }
// }
