<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Gateway_model extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }


        public function sendTwoFactorSms($mobile, $message){
            $result = array();
            $this->db->select('sms_ip,sms_pass,sms_user')->from('gccsms.tblsms')->where('is_connected', 1)->where('sms_user','CONYX');
            $query = $this->db->get()->row();
            $ch = curl_init();
            $parameters = array(
                'apikey' => $query->sms_pass,
                'number' => $mobile,
                'message' => $message,
                'sendername' => $query->sms_user,
            );
            curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close ($ch);
            $data = json_decode($output, true);
            $data = $data[0];
            if (!in_array($data['status'], ['Failed', 'Refunded'])) {
                $result['status'] = true;
                $result['output'] = $data;
            } else {
                $result['status'] = false;
                $result['output'] = false;
            }
            return $result;
        }


    }