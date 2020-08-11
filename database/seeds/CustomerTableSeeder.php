<?php

use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Customer::class, 100)->create()->each(function ($customer) {
            $ordersCount = rand(0, 10);
            $this->makeOrders($customer, $ordersCount);
        });
    }

    private function makeOrders(\App\Models\Customer $customer, $ordersCount) {
        $statuses = [ "open", "closed", "canceled" ];
        for ($i = 0; $i < $ordersCount; $i++) {
            $customer->orders()->create([ "status" => $statuses[rand(0, 2)] ]);
        }
    }
}
