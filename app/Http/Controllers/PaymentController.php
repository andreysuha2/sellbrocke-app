<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
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

            return response()->json([
                'msg' => 'The payment was successfully sent!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'msg' => $e->getMessage()
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
}
