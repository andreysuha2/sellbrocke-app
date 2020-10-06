<?php

use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    private $devicesConditions;
    private $devices;

    public function __construct() {
        $this->faker = \Faker\Factory::create();
        $this->devicesConditions = \App\Models\Condition::all();
        $this->devices = \App\Models\Device::all();
    }

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
        $payments = [ "paypal", "check" ];
        for ($i = 0; $i < $ordersCount; $i++) {
            $devicesCount = rand(1, 10);
            $order = $customer->orders()->create([
                "status" => $statuses[rand(0, 2)],
                "payment" => $payments[rand(0, 1)]
            ]);
            $this->makeOrderDevices($order, $devicesCount);
        }
    }

    private function makeOrderDevices(\App\Models\Order $order, $devicesCount) {
        for ($i = 0; $i < $devicesCount; $i++) {
            $condition = $this->devicesConditions->random();
            $device = $this->devices->random();
            $orderDevice = new \App\Models\OrderDevice();
            $orderDevice->condition()->associate($condition);
            $orderDevice->device()->associate($device);
            $orderDevice->order()->associate($order);
            \Illuminate\Support\Facades\Log::info($orderDevice);
            $orderDevice->save();
            $this->makeOrderDeviceProductGrid($device, $orderDevice);
            $this->makeOrderDeviceDefects($device, $orderDevice);
        }
    }

    private function makeOrderDeviceProductGrid(\App\Models\Device $device, \App\Models\OrderDevice $orderDevice) {
        if($device->use_products_grids) {
            $size = $device->productsGrids()->where("type", "size")->inRandomOrder()->first();
            $carrier = $device->productsGrids()->where("type", "carrier")->inRandomOrder()->first();
            $orderDevice->products_grids()->attach([ $size->id, $carrier->id ]);
        }
    }

    private function makeOrderDeviceDefects(\App\Models\Device $device, \App\Models\OrderDevice $orderDevice) {
        $defectsCount = rand(1, 10);
        $defects = $device->defects()->inRandomOrder()->limit($defectsCount)->pluck("id");
        $orderDevice->defects()->attach($defects);
    }
}
