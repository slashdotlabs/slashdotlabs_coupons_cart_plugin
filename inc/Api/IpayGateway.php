<?php


namespace Slash\Api;


final class IpayGateway
{
    public static function retriveUrl($meta_data=null)
    {
        if (!$meta_data) return false;

        $live = "0";
        // Retrieve vid and hashkey from options TODO: option_name = ccart_settings
        $vid = "slashdot"; //"demo";
        $hashkey = "S1@shD0T!@bz";//"demo";

        $ipay_base_url = "https://payments.ipayafrica.com/v3/ke";
        $fields =[
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
        $generated_hash = hash_hmac('sha1',$datastring , $hashkey);

        $fields['hsh'] = $generated_hash;

        // url encode callback
        $fields['cbk'] = urlencode($fields['cbk']);

        // add other optional fields (lbk and autopay)
        $fields['autopay'] = "1";

        $fields_string = array_map(function ($value, $key) {
            return $key.'='.$value;
        }, array_values($fields), array_keys($fields));
        $fields_string = implode("&", $fields_string);

        // TODO: Log only during development

        return $ipay_base_url.'?'.$fields_string;
    }


    /**
     * @param $code string Status code from iPay redirct
     * @return mixed
     */
    public static function get_status_state($code)
    {
        $state = '';
        $process = false;
        switch ($code) {
            case 'fe2707etr5s4wq':
                $state = 'Failed transaction';
                break;
            case 'aei7p7yrx4ae34':
                $state  = 'Success';
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
        return ['process'=>$process, 'state' => $state];
    }
}