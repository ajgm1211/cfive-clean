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
        $this->call(CountriesTableSeeder::class);
        $this->call(AirportsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PriceTypesTableSeeder::class);
        $this->call(PriceSubtypesTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
        $this->call(CalculationtypeTableSeeder::class);
        $this->call(AirlinesTableSeeder::class);
        $this->call(CarriersTableSeeder::class);
        $this->call(HarborsTableSeeder::class);
        $this->call(TypedestinyTableSeeder::class);
        $this->call(StatusQuotesTableSeeder::class);
        $this->call(HarborsCopyTableSeeder::class);
        $this->call(IncotermTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(CalculationtypelclTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(DirectionsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(CompanyUsersTableSeeder::class);
        $this->call(ScheduleTypeTableSeeder::class);
        $this->call(StatusAlertsTableSeeder::class);
//        $this->call(ContractApisTableSeeder::class);
        $this->call(GroupContainersTableSeeder::class);
        $this->call(ContainersTableSeeder::class);
        $this->call(ContainerCalculationsTableSeeder::class);
    }
}
