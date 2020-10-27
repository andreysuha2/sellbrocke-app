<?php

namespace App\Http\Controllers;

use App\Jobs\PaymentPayPalNotificationJob;
use App\Jobs\PaymentCheckNotificationJob;
use App\Models\Payment;
use App\Models\Order;
use App\Services\SettingService;
use Illuminate\Http\Request;
use League\Flysystem\Exception;
use PayPal\Api\Payout;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\PayoutItem;
use PayPal\Api\Currency;

class PaymentController extends Controller
{
    private $apiContext;
    private $settings;

    public function __construct()
    {
        $this->settings = SettingService::getParametersByGroup("payment");

        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->settings["PAYPAL_CLIENT_ID"],
                $this->settings["PAYPAL_CLIENT_SECRET"]
            )
        );
    }

    public function payment(Request $request)
    {
        if (empty($request->orderId)) {
            return response()->json([
                "message" => "An order's identifier isn't received!"
            ], 402);
        }

        $order = Order::find($request->orderId);

        if (empty($order->customer->paypal_email)) {
            return response()->json([
                "message" => "The payment's email isn't received!"
            ], 402);
        }

        $payouts = new Payout();
        $senderBatchHeader = new PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("You have a Payout!");

        $senderItem = new PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setNote('Thanks for your package!')
            ->setReceiver($order->customer->paypal_email)
            ->setSenderItemId($order->id)
            ->setAmount(new Currency([
                "value" => $order->prices['discounted'],
                "currency" => "USD"
            ]));

        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        try {
            $output = $payouts->create(null, $this->apiContext);
            $result = (object) $output;

            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->payout_batch_id = $result->batch_header->payout_batch_id;
            $payment->sender_batch_id = $result->batch_header->sender_batch_header->sender_batch_id;
            $payment->amount = $order->prices['discounted'];
            $payment->currency = "USD";
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

    public function paymentCheck(Request $request)
    {
        if (empty($request->orderId)) {
            return response()->json([
                "message" => "An order's identifier isn't received!"
            ], 402);
        }

        $order = Order::find($request->orderId);

        try {
            dispatch(new PaymentCheckNotificationJob($order));
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        return response()->json([
            "message" => "The notification of the payment by check was successfully put to queue and wait to send to the customer!"
        ], 200);
    }
}
