<?php


namespace Slash\Api;

use Exception;
use Slash\Base\BaseController;
use Slash\Database\PaymentsModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class IpayGateway extends BaseController
{
    public static function retriveUrl($meta_data = null)
    {
        if (!$meta_data) return false;

        $live = "0";
        // Retrieve vid and hashkey from options TODO: option_name = ccart_settings
        $vid = "demo";//"slashdot";
        $hashkey = "demoCHANGED";//"S1@shD0T!@bz";

        $ipay_base_url = "https://payments.ipayafrica.com/v3/ke";
        $fields = [
            "live" => $live,
            "oid" => $meta_data['order_id'],
            "inv" => null,
            "ttl" => $meta_data['total_amount'],
            "tel" => $meta_data['phone_number'],
            "eml" => $meta_data['email'],
            "vid" => $vid,
            "curr" => "KES",
            "p1" => "",
            "p2" => "",
            "p3" => "",
            "p4" => "",
            "cbk" => $meta_data['cbk'],
            "cst" => "1",
            "crl" => "0"
        ];

        // datastring
        $datastring = implode("", $fields);

        // generate hash
        $generated_hash = hash_hmac('sha1', $datastring, $hashkey);

        $fields['hsh'] = $generated_hash;

        // url encode callback
        $fields['cbk'] = urlencode($fields['cbk']);

        // add other optional fields (lbk and autopay)
        $fields['autopay'] = "1";

        $fields_string = array_map(function ($value, $key) {
            return $key . '=' . $value;
        }, array_values($fields), array_keys($fields));
        $fields_string = implode("&", $fields_string);

        return $ipay_base_url . '?' . $fields_string;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function payment_cb_handler()
    {
        try {
            $response = $_GET;

            if (empty($response)) return;

//            $logger = new Logger();
//            $logger->log(json_encode(['fields_return' => $response]));

            $status_res = $this->get_status_state($response['status']);
            // Update transaction
            $update_data = [
                'status' => $status_res['process'] ? 'completed' : 'cancelled',
                'amount' => $response['mc'],
                'transaction_ref' => $response['txncd'],
                'payment_type' => $response['channel'],
                'payment_date' => date('Y-m-d'),
            ];
            $paymentModel = new PaymentsModel();
            $updated = $paymentModel->update($update_data, ['order_id' => $response['id']]);
            if ($updated === false) throw new Exception('Could not process request');

            // Fetch whole payment record
            $record = $paymentModel->getByOrderId($response['id']);

            if (!$status_res['process']) throw new Exception($status_res['state']);

            // TODO: Send email to customer about coupon

            echo $this->twig->render('partials/payment_success.twig',
                [
                    'coupon' => $record->customer_coupon,
                    'email' => $record->email
                ]);
        } catch (Exception $exception) {
            echo $this->twig->render('partials/payment_error.twig', [ 'content' => $exception->getMessage()]);
        }
        exit;
    }

    /**
     * @param $code string Status code from iPay redirct
     * @return mixed
     */
    public function get_status_state($code)
    {
        $state = '';
        $process = false;
        switch ($code) {
            case 'fe2707etr5s4wq':
                $state = 'Failed transaction';
                break;
            case 'aei7p7yrx4ae34':
                $state = 'Success';
                $process = true;
                break;
            case 'bdi6p2yy76etrs':
                $state = 'Pending: Incoming Mobile Money Transaction Not found. Please try again in 5 minutes.';
                break;
            case 'cr5i3pgy9867e1':
                $state = 'This code has been used already. A notification of this transaction sent to the merchant.';
                break;
            case 'dtfi4p7yty45wq':
                $state = 'The amount that you have sent via mobile money is LESS than what was required to validate this transaction.';
                break;
            case 'eq3i7p5yt7645e':
                $state = 'The amount that you have sent via mobile money is MORE than what was required to validate this transaction.';

        }
        return ['process' => $process, 'state' => $state];
    }
}