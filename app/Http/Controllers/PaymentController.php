<?php

namespace App\Http\Controllers;

use App\Jobs\PaymentPayPalNotificationJob;
use App\Jobs\PaymentCheckNotificationJob;
use App\Models\Payment;
use Illuminate\Http\Request;
use League\Flysystem\Exception;
use PayPal\Api\Payout;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\PayoutItem;
use PayPal\Api\Currency;

class PaymentController extends Controller
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                getenv('PAYPAL_CLIENT_ID'),
                getenv('PAYPAL_CLIENT_SECRET')
            )
        );
    }

    public function payment(Request $request)
    {
        // TODO: Replace dummy data and use order's data
        $order = new \stdClass();
        $order->customer = new \stdClass();
        $order->customer->email = 'web.jungle@gmail.com';
        $order->customer->first_name = 'Ben Hocks';
        $order->customer->paypal_email = 'ben.hocks@gmail.com';
        $order->prices = [
            'discounted' => 205.00
        ];

        $paymentDetails = (object) $request;

        if (empty($paymentDetails->email)) {
            return null;
        }

        $payouts = new Payout();
        $senderBatchHeader = new PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("You have a Payout!");

        $senderItem = new PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setNote('Thanks for your package!')
            ->setReceiver($paymentDetails->email)
            ->setSenderItemId($paymentDetails->order_id)
            ->setAmount(new Currency([
                "value" => $paymentDetails->value,
                "currency" => $paymentDetails->currency
            ]));

        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        try {
            $output = $payouts->create(null, $this->apiContext);
            $result = (object) $output;

            $payment = new Payment();
            $payment->order_id = $paymentDetails->order_id;
            $payment->payout_batch_id = $result->batch_header->payout_batch_id;
            $payment->sender_batch_id = $result->batch_header->sender_batch_header->sender_batch_id;
            $payment->amount = $paymentDetails->value;
            $payment->currency = $paymentDetails->currency;
            $payment->status = $result->batch_header->batch_status;
            $payment->save();

            $this->status($result->batch_header->payout_batch_id);

            dispatch(new PaymentPayPalNotificationJob($order));

            return response()->json([
                'message' => 'The payment was successfully sent!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function status($payoutBatchId)
    {
        $output = \PayPal\Api\Payout::get($payoutBatchId, $this->apiContext);
        $result = (object) $output;

        $payment = Payment::where('payout_batch_id', '=', $payoutBatchId)->first();
        $payment->status = $result->batch_header->batch_status;
        $payment->save();
    }

    public function paymentCheck()
    {
        try {
            // TODO: Replace dummy data and use order's data
            $order = new \stdClass();
            $order->customer = new \stdClass();
            $order->customer->email = 'web.jungle@gmail.com';
            $order->customer->first_name = 'John';
            $order->customer->last_name = 'Doe';
            $order->customer->paypal_email = 'john.doe@gmail.com';
            $order->customer->address = 'Sixth Avenue';
            $order->customer->city = 'New York';
            $order->customer->state = 'NY';
            $order->customer->zip = '10001';
            $order->prices = [
                'discounted' => 350.00
            ];

            dispatch(new PaymentCheckNotificationJob($order));
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
