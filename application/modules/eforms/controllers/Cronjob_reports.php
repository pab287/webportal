<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Cronjob_reports extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model("eforms/borrowing_m", "borrowing");
        $this->load->model("eforms/loa_m", "loa");
        $this->load->model('eforms/shipping_m','shipping');
        $this->load->model("eforms/travel_order_m", "travel_order");
        $this->load->model("crs/document_model", "document");
        $this->load->model("core/Core_model", "core");
        $this->load->model("eforms/accountability_m", "accountability");
        $this->load->model("sms/Contacts_model", "contacts");
        $this->load->model("hris/employee_model", "employee");
        $this->load->model('gcctime/timesheet_model', 'timesheet');
    }

    function generate_daily_loa_summary($email=false){
        $currentDate = date("Y-m-d");
        $data = $this->loa->generateDailyLoaSummary($currentDate);
        if($data){
            $arrData = array();
            $arrData["date"] = $currentDate;
            $arrData["data"] = $data;

            $tempDatax = $this->loa->generateDailyApprovedLoaSummary($currentDate);
            if($tempDatax){
                $arrData["approved"] = $tempDatax;
            }else{
                $arrData["approved"] = "";
            }

            $messageContent = "";
            $messageContent .= $this->load->view("eforms/email_templates/daily_summary/email-summary_loa_template", $arrData, true);

            if($email){
                $module = "eforms_loa_summary";
                $email_title = "Leave of Absence";
                $content_title = "Leave of Absence Daily Report - Summary";
                $content = $messageContent;
                
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    function generate_daily_loa_morning_summary($email=false){
        $currentDate = date("Y-m-d");
        $data = $this->loa->generateDailyLoaMorningSummary($currentDate);
        if($data){
            $messageContent = "";
            $messageContent .= $this->load->view("eforms/email_templates/daily_summary/email-summary_loa_m_template", array("date"=>$currentDate, "data"=>$data), true);
            
            if($email){
                $module = "eforms_loa_morning_summary";
                $email_title = "Leave of Absence";
                $content_title = "Leave of Absence - Today";
                $content = $messageContent;
                
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    function generate_crs_email_sending($email=false){
        $currentDate = date("Y-m-d");
        $crs_data['crs_data'] = $this->document->crs_data();
        if(count($crs_data['crs_data']) > 0){
            $messageContent = "";
            $messageContent .= $this->load->view("crs/email_template/crs_online_registration", $crs_data, true);
            
            if($email){
                $module = "crs_online_registration";
                $email_title = "CRS Online Registration";
                $content_title = "CRS Online Registration";
                $content = $messageContent;
                
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    function generate_daily_shipping_summary($email=false){
		$currentDate = date("Y-m-d");
		$data = $this->shipping->generateDailyShippingSummary($currentDate);
		if($data){
			$messageContent = "";
			$messageContent .= $this->load->view("eforms/email_templates/daily_summary/email-summary_sa_template", array("date"=>$currentDate, "data"=>$data), true);
	
			if($email){
				$module = "eforms_shipping_summary";
				$email_title = "Shipping Advice";
				$content_title = "Shipping Advice Daily Report - Summary";
				$content = $messageContent;
		
				if($content){
					$sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
					if($sent){
						echo $content;
					}else{
						echo "Failed Sending Email!";
						show_error($this->email->print_debugger()); 
					}
				}else{
					echo "No email content";
				}
			}else{
				echo $messageContent;
			}
		}else{
			return false;
		}

    }
    
    function generate_daily_to_summary($email = false)
    {
        $currentDate = date("Y-m-d");
        $data = $this->travel_order->generateDailyTravelOrderSummary($currentDate);
        if ($data) {
            $messageContent = "";
            $messageContent = $this->load->view("eforms/email_templates/daily_summary/email-summary_to_template", array("date" => $currentDate, "data" => $data), true);

            if ($email) {
                $module = "eforms_travelorder_summary";
                $email_title = "Travel Order";
                $content_title = "Travel Order Daily Report - Summary";
                $content = $messageContent;

                if ($content) {
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if ($sent) {
                        echo $content;
                    } else {
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger());
                    }
                } else {
                    echo "No email content";
                }
            } else {
                echo $messageContent;
            }
        } else {
            return false;
        }
    }

    function generate_daily_to_summary_for_next_day($email = false){
        $date = date("Y-m-d");
        $currentDate = date("Y-m-d", strtotime($date . "+1 day"));
        $data = $this->travel_order->generateNextDayTravelOrderSummary();

        // $dept_heads = array();
        // $dept_heads = $this->db->get_where("gccmaster.email_template", array("name"=>"eforms_travelorder_summary_next_day"))->row("send_to");
            $messageContent = "";
            $messageContent = $this->load->view("eforms/email_templates/daily_summary/email-summary_to_next_day", array("date" => $currentDate, "data" => $data), true);
            
            if ($email) {
                $module = "eforms_travelorder_summary_next_day";
                $email_title = "Travel Order";
                $content_title = "Travel Order Summary(".$currentDate.") - Summary";
                if($data){
                    $content = $messageContent;
                }else{
                    $content = "No Travel Order scheduled to this date";
                }
                

                $overrideMailer = array();
                // $overrideMailer["send_to"] = $dept_heads;
                // $overrideMailer["email_pass"] = "user!234";
                if ($content) {
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                    if ($sent) {
                        echo $content;
                    } else {
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger());
                    }
                } else {
                    echo "No email content";
                }
            } else {
                return false;
            }
    }

    function generate_borrowing_overdue_morning_summary($email=false){
        $data = $this->borrowing->getCurrentOverdueItems();
        if($data){
            $messageContent = "";
            $messageContent .= $this->load->view("eforms/email_templates/email-bf_overdue_template", $data, false);
            
            if($email){
                $module = "eforms_borrowing_overdue_morning_summary";
                $email_title = "Borrowing Form - Overdue";
                $content_title = "Borrowing Form Overdue - Today";
                $content = $messageContent;
                
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    
    function borrowing(){
        $test = $this->contacts->sendSMS('09289573289', "asdasdasdasdasdasd");
        if($test){
            return 'nagsend';
        }else{
            return 'wala nagsend';
        }
    }

    function overdue_borrowing_telegram_notification(){
        $data = $this->borrowing->getCurrentOverdueItemsDaily();
        $employee = $this->borrowing->getCurrentOverdueItemsOfEmployee('employee');
        $txtMsgGroup = "";
        $txtMsgGroup .= "<b>DUE BORROWED ITEMS DAILY SUMMARY".chr(10);
        $txtMsgGroup .= date("Y-F-d");
        $txtMsgGroup = "The following employee(s) have <b>OVERDUE</b> borrowed items:".chr(10).chr(10);
        if($employee['data']){
            foreach($employee['data'] as $employee_data){
                $arr = array();
                $item = $this->borrowing->getBorrowedItemByEmployee($employee_data->borrower, 'employee')['data'];
                    foreach($item as $items){
                        $arr[] = "- ".$items->asset_name;
                    }
                    $items = implode("\n", $arr);
                    $txtMsg = "";
                    $txtMsg .= "You have borrowed item(s) ".chr(10);
                    $txtMsg .= $items.chr(10);
                    $txtMsg .= "Due to be returned today ".date("Y-m-d").". Kindly return the item to warehouse or extend its due date.";
                    $txtMsgGroup .= "<b><i>".$employee_data->name."</i></b>".chr(10);
                    $txtMsgGroup .= $items.chr(10);
                    
                    // $this->contacts->sendSMS($employee_data->mobile_no, $txtMsg);
                    // $this->telegram_borrowing($txtMsg, $employee_data->telegram_chat_id);
            }
            
            $txtMsgGroup .= chr(10)."Due to be returned today ".date("Y-m-d").".";
            $this->telegram_borrowing_group($txtMsgGroup);
        }else{
            echo "Failed Sending Telegram Notification!";
        }
    }

    function overdue_borrowing_telegram_notification_summary(){
        $data = $this->borrowing->getCurrentOverdueItemsSummary();
        if($data['data']){
            $txtMsgGroup = "";
            $txtMsgGroup .= "<b>BORROWED ITEMS WEEKLY SUMMARY".chr(10);
            $txtMsgGroup .= date("Y-F-d", strtotime("monday this week"))." - ".date("Y-F-d", strtotime("saturday this week"))."</b>".chr(10).chr(10);
            $txtMsgGroup .= "The following employee(s) have <b>OVERDUE</b> borrowed items:".chr(10);
                foreach($data['data'] as $employee_data){
                    $txtMsgGroup .= "<b><i>".$employee_data['name']."</i></b>".chr(10);
                    $txtMsgGroup .= strtoupper($employee_data['asset_code'])." | ".$employee_data['asset_name'].chr(10);
                    $txtMsgGroup .= "Date Overdue: ".date("Y-m-d", strtotime($employee_data['date_due'])).chr(10);
                }
                // $txtMsgGroup .= "<b><i>".$employee_data['name']."</i></b>".chr(10);
                // $txtMsgGroup .= $employee_data['reference_no']." | ".$employee_data['asset_name'].chr(10);
                echo $txtMsgGroup;
                $this->telegram_borrowing_group($txtMsgGroup);
            }else{
                echo "No email content";
            }
    }

    function generate_borrowing_overdue_daily($email=false){
        $data = $this->borrowing->getCurrentOverdueItemsDaily();
        $employee = $this->borrowing->getCurrentOverdueItemsOfEmployee('employee');
        if($employee['data']){
            $arrData = array(); 
            $messageContent = "";
            $messageContent .= $this->load->view("eforms/email_templates/email-bf_overdue_daily", $data, true);
            if($email){
                $module = "eforms_borrowing_overdue_daily";
                $email_title = "Due Borrowed Items";
                $content_title = "Due Borrowed Items - Today";
                $content = $messageContent;
                if($content){
                    
                    $txtMsgGroup = "";
                    $txtMsgGroup .= "<b>DUE BORROWED ITEMS DAILY SUMMARY".chr(10);
                    $txtMsgGroup .= date("Y-F-d");
                    $txtMsgGroup = "The following employee(s) have <b>DUE</b> borrowed items:".chr(10).chr(10);
                    foreach($employee['data'] as $employee_data){
                        $arr = array();
                        $item = $this->borrowing->getBorrowedItemByEmployee($employee_data->borrower, 'employee')['data'];
                        $sent = $this->generate_borrowing_overdue_daily_employee($email=false, $employee_data->email);
                        if($sent){
                            foreach($item as $items){
                                $arr[] = "- ".$items->asset_name;
                            }
                            $items = implode("\n", $arr);
                            $txtMsg = "";
                            $txtMsg .= "You have borrowed item(s) ".chr(10);
                            $txtMsg .= $items.chr(10);
                            $txtMsg .= "Due to be returned today ".date("Y-m-d").". Kindly return the item to warehouse or extend its due date.";
                            
                            $txtMsgGroup .= "<b><i>".$employee_data->name."</i></b>".chr(10);
                            $txtMsgGroup .= $items.chr(10);
                            
                           
                            // $this->contacts->sendSMS($employee_data->mobile_no, $txtMsg);
                            // $this->telegram_borrowing($txtMsg, $employee_data->telegram_chat_id);
                            echo $content;
                        }else{
                            echo "Failed Sending Email!";
                            show_error($this->email->print_debugger()); 
                        }
                    }
                    
                    $txtMsgGroup .= chr(10)."Due to be returned today ".date("Y-m-d").".";
                    $this->telegram_borrowing_group($txtMsgGroup);
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    function generate_borrowing_overdue_saturday_summary($email=false){
        $data = $this->borrowing->getCurrentOverdueItemsSummary();
        $employee = $this->borrowing->getCurrentOverdueItemsOfEmployee('group');
        if($data['data']){
            $messageContent = "";
            $messageContent .= $this->load->view("eforms/email_templates/email-bf_overdue_summary", $data, true);
                $txtMsgGroup = "";
                $txtMsgGroup .= "<b>OVERDUE BORROWED ITEMS WEEKLY SUMMARY".chr(10);
                $txtMsgGroup .= date("Y-F-d", strtotime("monday this week"))." - ".date("Y-F-d", strtotime("saturday this week"))."</b>".chr(10).chr(10);
                $txtMsgGroup .= "The following employee(s) have <b>OVERDUE</b> borrowed items:".chr(10);
            if($email){
                $module = "eforms_borrowing_overdue_summary";
                $email_title = "Overdue Borrowed Items";
                $content_title = "Overdue Borrowed Items - Today";
                $content = $messageContent;
                
                if($content){
                    
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if($sent){
                        
                        $arr = array();
                        foreach($data['data'] as $employee_data){
                            $txtMsgGroup .= "<b><i>".$employee_data['name']."</i></b>".chr(10);
                            $txtMsgGroup .= $employee_data['asset_name'].chr(10);
                        }
                        // $txtMsgGroup .= "<b><i>".$employee_data['name']."</i></b>".chr(10);
                        // $txtMsgGroup .= $employee_data['reference_no']." | ".$employee_data['asset_name'].chr(10);
                        echo $content;
                        $this->telegram_borrowing_group($txtMsgGroup);
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    function generate_borrowing_overdue_daily_employee($email=false){
        $data = $this->borrowing->getCurrentOverdueItemsOfEmployee('employee');
        if($data){
            $messageContent = "";
            $messageContent .= $this->load->view("eforms/email_templates/email-bf_overdue_daily_employee", $data, true);
            
            if($email){
                $module = "eforms_borrowing_overdue_daily_employee";
                $email_title = "Overdue Borrowed Items";
                $content_title = "Overdue Borrowed Items - Today";
                $content = $messageContent;
                $overrideMailer = array();
                $overrideMailer["send_to"] = $data->email;
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    function generate_asset_with_checkno($email=false){
        $this->load->model("ams/assets_model", "asset_m");
        $data = $this->asset_m->generateAssetWithCheckNo();
        if(isset($data["data"]) && count($data["data"]) > 0){
            $messageContent = "";
            $messageContent .= $this->load->view("ams/email_templates/email-asset_checkno_template", $data, true);
            
            if($email){
                $module = "ams_asset_with_checkno_summary";
                $email_title = "AMS - Asset(s) with check number";
                $content_title = "AMS - Today";
                $content = $messageContent;
                
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
        }else{
            return false;
        }
    }

    function disapproveOverdueLoa(){
        $remarks = "Disapproved due to overdue pending status.";
        $query = $this->db->query("SELECT * FROM gcceforms.loa where date_from < NOW() - INTERVAL 8 DAY AND status='Pending' AND date_from != '0000-00-00 00:00:00' AND created_dt > NOW() - INTERVAL 30 DAY");
        
            foreach($query->result() as $row){
                $update = $this->db->query("UPDATE gcceforms.loa SET status='Disapproved', disapproved_remarks='$remarks', disapproved_by='1' where id='$row->id' ");
                $employeeName = $this->loa->getEmpName($row->employee);
                    $this->db->select('users.email as email,users.username as username, dept.head as head');
                    $this->db->from('gccmaster.tblusers as users');
                    $this->db->join('gcchris.tbldepartments as dept',"users.emp_id=dept.head_id","LEFT");
                    $this->db->where("users.is_suspended=0");
                    $this->db->where("dept.description",$row->department);
                    $email = $this->db->get();
                    $head_email = $email->row_array();

                    $formatted_date_from = !empty(date_parse($row->date_from)['hour'])? date_format(date_create($row->date_from), "Y-m-d H:i") : date_format(date_create($row->date_from),"Y-m-d");
                    $formatted_date_to = !empty(date_parse($row->date_to)['hour'])? date_format(date_create($row->date_to), "Y-m-d H:i"): date_format(date_create($row->date_to),"Y-m-d");

                    if($row->date_from != "0000-00-00 00:00:00" AND $row->date_to != "0000-00-00 00:00:00"){
                        $dates = $formatted_date_from." - <br>".$formatted_date_to;
                    }else{
                        $dates = $formatted_date_from;
                    }
                    $data = array(
                        "reference" => $row->reference_no,
                        "dates" => $dates,
                        "employee" => $employeeName,
                        "reasons" => strtoupper($row->reason),
                        "head" => $head_email['head']
                    );
                $email_data[] = $data;    
            }

        /* archive loa more than 1yr
        $query_update =  $this->db->query("SELECT * FROM gcceforms.loa where date_from < NOW() - INTERVAL 30 DAY AND status='Disapproved' AND date_from != '0000-00-00 00:00:00' AND created_dt > NOW() - INTERVAL 365 DAY");
            foreach($query_update->result() as $row_update){
                $update_remaining = $this->db->query("UPDATE gcceforms.loa SET status='Pending', disapproved_remarks='$remarks', disapproved_by='SYSTEM' where id='$row_update->id' ");
            }
        end */

            //get all department heads to send email to
            $this->db->select("users.email as email");
            $this->db->from('gccmaster.tblusers as users');
            $this->db->join('gcchris.tbldepartments as dept',"users.emp_id=dept.head_id","LEFT");
            $this->db->group_by('dept.head_id');
            $this->db->where("users.is_suspended",0);
            $this->db->where("dept.description !=", "");
            $this->db->where("users.email !=", "");
            $this->db->where("users.email !=", "NO EMAIL ADDRESS");
            $email_heads = $this->db->get();
            foreach($email_heads->result() as $mails){
                $supervisor[] = $mails->email;
            }        
            $cc_to = "hr@gccph.com";
            $send_to = $supervisor;
            if(count($query->result()) > 0){
                $send = $this->email_send_loa($email_data,$send_to,$cc_to);
            }else{
                $send = "No Data to Disapprove.";
            }
            return $send;     
    }

    function email_send_loa($email_data, $send_to, $cc_to){
        $resultset = array();        
        //$emailTo = $this->sendEmailCompanyTo($companyTo);
        //if($recipient == NULL){
        // }else{
        //     $emailTo = $recipient;
        // }
        // $data = array("employee"=>$employee, "reference_no"=>$referenceNumber, "dates"=>$dates, "reasons"=>$reasons);
        $data = $email_data;    
        $message = "";
        $message .= $this->load->view("eforms/email_templates/email_loa_template", array("email_data"=>$email_data), false);
     
        $module = "eforms_loa_expired";
        $email_title = "Leave of Absence";
        $content_title = "Leave of Absence (Disapproved)";
        $content = $message;
        
        $overrideMailer = array();
        $overrideMailer["send_to"] = $send_to;

        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
        if($sent){
            $resultset["status"] = true;
            $resultset["toastr_msg"] = "Email has been sent";
            $resultset["toastr_status"] = "success";
            
            $this->core_layout->logNotification("LOA status updated, email has been sent", "success");
        }else{
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "Failed to send email!";
            $resultset["toastr_status"] = "error";
            
            $this->core_layout->logNotification("LOA status updated, failed to send email!", "error");
        }
        return $resultset;
    }

    function newAssetsEmailSending($email=false){
        $this->load->model("ams/dashboard_model", "dashboard_m");
        $data = $this->dashboard_m->getRecentlyAddedAssets_email();
        $resultset = array();        
        if(isset($data["data"]) && count($data["data"]) > 0){
            $message = "";
            $message .= $this->load->view("ams/email_templates/email-newly_added_assets_report", $data, true);
            $content = $message;

            if($email){
                $module = "eforms_new_assets_report";
                $email_title = "AMS - Recently Added Assets";
                $content_title = "AMS - Recently Added Assets";
                $content = $message;
                $overrideMailer = array();
                $overrideMailer["send_to"] = "jp04@gccph.com";
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    return false;
                }
            }else{
                echo $message;
            }
        }else{
            return false;
        }
    }

    function fix_isBorrowed_one_asset($isComponent){
        $data = $this->accountability->fixIsBorrowedOne_asset($isComponent);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function fix_isBorrowed_one_vehicle($isComponent){
        $data = $this->accountability->fixIsBorrowedOne_vehicle($isComponent);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function fix_isBorrowed_zero_asset($isComponent){
        $data = $this->accountability->fixIsBorrowedZero_asset($isComponent);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function fix_isBorrowed_zero_vehicle($isComponent){
        $data = $this->accountability->fixIsBorrowedZero_vehicle($isComponent);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function telegram_borrowing($msg, $chat_id) {
        $data = $this->borrowing->telegram_config_if_exist('borrowing', 'data');

        // $telegrambot = $data->telegram_bot_token;
        if($chat_id){
            $telegrambot = $data->telegram_bot_token;
            $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$chat_id,'text'=>$msg,'parse_mode'=>'html');
            $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
            $context=stream_context_create($options);
            $result=file_get_contents($url,false,$context);
        }else{
            return false;
        }
        return $result;
    }

    public function telegram_borrowing_group($msg) {
        $data = $this->borrowing->telegram_config_if_exist('borrowing', 'data');

        // $telegrambot = $data->telegram_bot_token;
        if($data){
            $telegrambot = $data->telegram_bot_token;
            $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$data->chat_id,'text'=>$msg,'parse_mode'=>'html');
            $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
            $context=stream_context_create($options);
            $result=file_get_contents($url,false,$context);
        }else{
            return false;
        }
        return $result;
    }

    function disconnectOverdueAccounts(){
      
      $bills_arr = array();
      $date_today = date("Y-m-d");
      $due_date_days = $this->db->get_where("hydra_billing.due_date")->row('day');
      
      $this->db->select("*");
      $this->db->from("hydra_billing.bills");
      $this->db->where("is_paid", 0);
      $this->db->where("due_date <", $date_today);
      $this->db->where("status", 1);
      $query = $this->db->get();
      $data = $query->result_array();
      foreach($data as $temp_data){
        $bills_arr[] = $temp_data['account_id'];
        $this->db->update("hydra_billing.accounts", array("is_disconnected"=>1, "disconnect_date"=>date("Y-m-d H:i:s")), array("id"=>$temp_data['account_id']));
      }
      // $overdue_accounts = $this->db->get_where("hydra_billing.bills", array("due_date <=", $date_today))->result();
      $final_data = array_unique($bills_arr);

      return $final_data;
    }

    public function generate_onboarding_active_employee($email=false, $isWeekly=false){
        $sendEmail = json_decode($email);
        $isWeeklyEmail = json_decode($isWeekly);
        
        $currentDate = date("Y-m-d", strtotime("-1 month"));
        $month = date("m", strtotime($currentDate));
        $year = date("Y", strtotime($currentDate));
        $tempTitle = "HRIS - Separated Employee(s)";

        if($isWeeklyEmail){
            $tempTitle = "HRIS - Separated Employee(s) - Weekly";
            $today = date("Y-m-d");
            $currentDate = date("Y-m-d", strtotime("last sunday", strtotime($today)));
            $currentDate = date("Y-m-d", strtotime("+1 day", strtotime($currentDate)));
            $month = date("m", strtotime($currentDate));
            $year = date("Y", strtotime($currentDate));
        }

        $result = (object) $this->employee->getOnboardingActiveEmployees($month, $year, $currentDate, $isWeeklyEmail);
        $message = $this->load->view("hris/email_templates/email-onboarding_employees", $result, true);

        if($sendEmail){
            $module = "hris_onboarding_employees";
            $email_title = $tempTitle;
            $content_title = $tempTitle;
            $content = $message;
            if($content){
                $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                if($sent){ echo $content; }
                else{
                    echo "Failed Sending Email!";
                    show_error($this->email->print_debugger()); 
                }
            }else{ return false; }
        }else{ echo $message; }
    }

    public function generate_separated_inactive_employee($email=false, $isWeekly=false){
        $sendEmail = json_decode($email);
        $isWeeklyEmail = json_decode($isWeekly);

        $currentDate = date("Y-m-d", strtotime("-1 month"));
        $month = date("m", strtotime($currentDate));
        $year = date("Y", strtotime($currentDate));
        $tempTitle = "HRIS - Separated Employee(s)";
        
        if($isWeeklyEmail){
            $tempTitle = "HRIS - Separated Employee(s) - Weekly";
            $today = date("Y-m-d");
            $currentDate = date("Y-m-d", strtotime("last sunday", strtotime($today)));
            $currentDate = date("Y-m-d", strtotime("+1 day", strtotime($currentDate)));
            $month = date("m", strtotime($currentDate));
            $year = date("Y", strtotime($currentDate));
        }

        $result = (object) $this->employee->getSeparatedInactiveEmployees($month, $year, $currentDate, $isWeeklyEmail);
        $message = $this->load->view("hris/email_templates/email-separated_employees", $result, true);

        if($sendEmail){
            $module = "hris_separated_employees";
            $email_title = $tempTitle;
            $content_title = $tempTitle;
            $content = $message;
            if($content){
                $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                if($sent){ echo $content; }
                else{
                    echo "Failed Sending Email!";
                    show_error($this->email->print_debugger()); 
                }
            }else{ return false; }
        }else{ echo $message; }
    }

    public function scheduled_resigned_inactive($date = null){
        $data = $this->employee->setScheduledEmployeeInactive($date);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function automated_approve_ot($date = null){
        $data = $this->timesheet->automated_approve_ot($date);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}