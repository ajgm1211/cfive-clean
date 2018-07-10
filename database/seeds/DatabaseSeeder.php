<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PriceTypesTableSeeder::class);
        $this->call(PriceSubtypesTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
        $this->call(CalculationtypeTableSeeder::class);
        $this->call(CarriersTableSeeder::class);
        $this->call(HarborsTableSeeder::class);
        $this->call(TypedestinyTableSeeder::class);
        $this->call(StatusQuotesTableSeeder::class);
        $this->call(HarborsCopyTableSeeder::class);
    }
}
