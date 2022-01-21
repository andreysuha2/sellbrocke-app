<?php

namespace App\Jobs;

use App\Mail\OrderMailCustomer;
use App\Mail\OrderMail;
use App\Services\SettingService as Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class OrderCreateNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $customer;
    private $adminEmail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order, $customer)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->adminEmail = Config::getParameter('ADMIN_EMAIL');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send email for the administrator with order's details
        Mail::to($this->adminEmail)->queue(new OrderMail($this->order));

        // Send email for the customer with order's details
        Mail::to($this->customer->email)->queue(new OrderMailCustomer($this->order));
    }
}
