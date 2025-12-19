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
            if(str_contains(strtolower($data[0]), 'not sufficient')){
                $result['status'] = false;
                $result['output'] = $data;
                $result['message'] = 'Your current balance of credits is not sufficient. This transaction requires credits.';
            }
            elseif (!in_array( $data[0]['status'], ['Failed', 'Refunded']) ) {
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

        function sendPlaySMS($phone, $msg){
            $this->db->select("modem,sms_ip, sms_port, sms_user, sms_pass, department_id, exclude");
            $this->db->from("gccsms.tblsms");
            $this->db->where("is_connected",'1');
            $this->db->where("sms_user",'VOP');
            $sms = $this->db->get()->row_array();
            if($sms && $phone){
                if (substr($phone, 0, 1) === '9') {
                    $phone = '0' . $phone;
                }    
                $user = $sms['sms_user'];
                $password = $sms['sms_pass'];
                $playsms_url = "https://" . $sms['sms_ip'] . ":" . $sms['sms_port'] . "/index.php?app=ws";
                $url = '&u='.$user;
                $url.= '&h='.$password;
                $url.= '&op=pv';
                $url.= '&smsc='.$sms['modem'];
                $url.= '&to='.$phone;
                $url.= '&msg='.urlencode($msg);
                $urltouse =  $playsms_url.$url;
                $arrContextOptions=array(
                  "ssl"=>array(
                       "verify_peer"=>false,
                       "verify_peer_name"=>false,
                  ),
              );
              $response_data = @file_get_contents($urltouse, false, stream_context_create($arrContextOptions));
              if ($response_data && strpos($response_data, '"status":"OK"') !== false){
                  $response['status'] = true;
                  $response['data'] = $response_data;
              } else {
                  $response['status'] = false;
                  $response['data'] = $response_data;
              }
            }else{
                $response['data']=[];
                $response['status'] = false;
            }
        
            return($response);
        }

    }