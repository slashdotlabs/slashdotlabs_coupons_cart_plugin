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
    private $paymentModel;
    private $vendor_id;
    private $hashkey;
    private $mode;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel = new PaymentsModel();

        // Get saved options
        $plugin_options = get_option('coupons_plugin');
        if (!empty($plugin_options) && array_key_exists('ipay', $plugin_options)){
            $ipay_options = $plugin_options['ipay'];
            $this->mode = $ipay_options['live'] ? "1" : "0";
            $this->vendor_id = $ipay_options['vendor_id'];
            $this->hashkey = $ipay_options['hashkey'];
        }
    }

    public function retriveUrl(array $meta_data)
    {
        $ipay_base_url = "https://payments.ipayafrica.com/v3/ke";
        $fields = [
            "live" => $this->mode,
            "oid" => $meta_data['order_id'],
            "inv" => null,
            "ttl" => $meta_data['total_amount'],
            "tel" => $meta_data['phone_number'],
            "eml" => $meta_data['email'],
            "vid" => $this->vendor_id,
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
        $generated_hash = hash_hmac('sha1', $datastring, $this->hashkey);

        $fields['hsh'] = $generated_hash;

        // url encode callback
        $fields['cbk'] = urlencode($fields['cbk']);

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

            if(isset($_GET['debug'])) {
                $logger = new Logger();
                $logger->log(json_encode(['fields_return' => $response]));
            }

            // Check if payment is already processed
            $record = $this->paymentModel->getByOrderId($response['id']);
            if($record->status !== "initiated") throw new Exception("Your coupon payment is already processed. You can exit this window now :)");

            // Verify status with iPay IPN
            $verified_status = $this->verify_payment_status($this->vendor_id, $response);

            $status_res = $this->get_status_state($verified_status);
            // Update transaction
            $update_data = [
                'status' => $status_res['process'] ? 'completed' : 'cancelled',
                'amount' => $response['mc'],
                'transaction_ref' => $response['txncd'],
                'payment_type' => $response['channel'],
                'payment_date' => date('Y-m-d'),
            ];
            $updated = $this->paymentModel->update($update_data, ['order_id' => $response['id']]);
            if ($updated === false) throw new Exception('Could not process request');

            // Fetch whole payment record
            $record = $this->paymentModel->getByOrderId($response['id']);

            if (!$status_res['process']) throw new Exception($status_res['state']);

            $email_sent = $this->send_customer_email($record);
            if (!$email_sent) throw new Exception("Error forwarding customer coupon email");

            echo $this->twig->render('partials/payment_success.twig',
                [
                    'name' => $record->fname . " " . $record->lname,
                    'coupon' => $record->customer_coupon,
                    'email' => $record->email
                ]);
        } catch (Exception $exception) {
            echo $this->twig->render('partials/payment_error.twig', ['content' => $exception->getMessage()]);
        }
        exit;
    }

    private function verify_payment_status(string $vendor_id, array $response)
    {
        $val1 = $response["id"];
        $val2 = $response["ivm"];
        $val3 = $response["qwh"];
        $val4 = $response["afd"];
        $val5 = $response["poi"];
        $val6 = $response["uyt"];
        $val7 = $response["ifd"];

        $ipnurl = "https://www.ipayafrica.com/ipn/?vendor=" . $vendor_id . "&id=" . $val1 . "&ivm=" .
            $val2 . "&qwh=" . $val3 . "&afd=" . $val4 . "&poi=" . $val5 . "&uyt=" . $val6 . "&ifd=" . $val7;
        $fp = fopen($ipnurl, "rb");
        $status = stream_get_contents($fp, -1, -1);
        fclose($fp);
        return $status;
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

    /**
     * @param  $record
     * @return bool
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function send_customer_email($record)
    {
        $customer_fullname = $record->fname . " " . $record->lname;
        $to = $record->email;
        $subject = "$customer_fullname, your coupon {$record->customer_coupon} is ready";
        $message = $this->twig->render("mail/customer_coupon.twig",
            [
                "record" => $record,
                "year" => date("Y"),
                "copy_url" => "medios.co.ke",
                "copy_name" => "MEDIOS LIMITED",
                "copy_address" => "Suite 108, Blue Violets Plaza, Kindaruma Road. Nairobi"
            ]);
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        return wp_mail($to, $subject, $message, $headers);
    }
}