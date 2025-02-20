<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Gateway_model extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }


        public function sendTwoFactorSms($mobile, $message) {
            $result = array();
            if (!preg_match('/^09\d{9}$/', $mobile)) {
                return array(
                    'status' => false,
                    'output' => false,
                    'message' => 'Invalid mobile number format. Must be 11 digits'
                );
            }

            $this->db->select('sms_ip,sms_pass,sms_user')
                        ->from('gccsms.tblsms')
                        ->where('is_connected', 1)
                        ->where('sms_user','CONYX');
            $query = $this->db->get()->row();
            $ch = curl_init();
            $parameters = array(
                'apikey' => $query->sms_pass,
                'number' => $mobile,
                'message' => $message,
                'sendername' => $query->sms_user,
            );
            curl_setopt($ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            if(curl_errno($ch)) {
                $result['status'] = false;
                $result['output'] = false;
                $result['message'] = 'Message sending failed. ' . curl_error($ch);
                return $result;
            }
            curl_close($ch);
            $data = json_decode($output, true);

            if (!in_array($data[0]['status'], ['Failed', 'Refunded'])) {
                $result['status'] = true;
                $result['output'] = $data;
                $result['message'] = 'Message sent successfully';
            } else {
                $result['status'] = false;
                $result['output'] = false;
                $result['message'] = 'Message sending failed. ' . $data[0]['status'];
            }
            return $result;
        }
    }