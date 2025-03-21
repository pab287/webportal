<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
      GC & C | Change Password
    </title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
          WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
    </script>

	<script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
    <!--end::Web font -->
        <!--begin::Base Styles -->
    <link href="<?php echo base_url(); ?>assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Base Styles -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/favicon.ico" />

    <!--begin::Base Scripts -->
    <script src="<?php echo base_url(); ?>assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
    <!--end::Base Scripts -->   
        <!--begin::Page Snippets -->
    <!-- <script src="<?php echo base_url(); ?>assets/snippets/pages/user/login.js" type="text/javascript"></script> -->
    <!--end::Page Snippets -->

    <!--begin::Page Vendors --> 

	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <!--end::Page Vendors --> 
    <style>
        .form-group .password-container {
            position: relative;
        }

        .form-group .m-input {
            padding-right: 35px; /* Adjust based on the size of your icon */
        }

        .form-group .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
  </head>

  	<body class="align-items-center justify-content-center">
        <div class="row">
            <div class="col" id="passkey">
                <div class="m-portlet m-login__signin">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<span class="m-portlet__head-icon"><img src="<?= base_url('assets/logo.png')?>" width="23%"></img></span>
							</div>
						</div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <h4 class="m-portlet__head-text mt-2">
                                    Mandatory Password Update on First Login
                                    </h4>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <form class="m-form" id="changepasswordform">
                        <div class="m-portlet__body">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="username">
                        <input type="hidden" name="old_password" >
                            <div class="form-group m-form__group">
                                <label>
                                   You need to update your password because this is the first time you are signing in.
                                </label>
                            </div>
                            <div class="form-group m-form__group">
                            <label>New Password <span style="color: red;">*</span></label>
                                <div class="password-container">
                                    <input id="newPasswordInput" type="password" class="form-control m-input" name="password_confirmation" data-validation="required length strength symbol" data-validation-length="min8" data-validation-strength="3" autocomplete="off">
                                    <span id="newPasswordToggle" class="password-toggle"><i class="fa fa-eye"></i></span>
                                </div>
                            </div>
                            <span class="m-form__help">
                                <ul>
                                    <li>Password must contain numbers.</li>
                                    <li>Password must contain uppercase letters.</li>
                                    <li>Password must have at least one symbol (e.g. !@#).</li>
                                    <li>Password must be greater than 8 characters.</li>
                                </ul>
                            </span>
                            <div class="form-group m-form__group">
                                <label>Confirm Password <span style="color: red;">*</span></label>
                                <div class="password-container">
                                    <input id="confirmPasswordInput" type="password" class="form-control m-input" name="password" data-validation="confirmation">
                                    <span id="confirmPasswordToggle" class="password-toggle"><i class="fa fa-eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="m-portlet__foot">
                            <div class="form-group m-form__group">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success">
                                        Change password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
		</div>

        </div>
<script type="text/javascript">
	const sessionData = <?= json_encode($session  ?? []) ?>;
	if (!sessionData.modal) {
		window.location.href = '<?php echo base_url("login"); ?>';
	}

    $('.password-toggle').on('click', function(e) {
        e.preventDefault();
        var $pwd = $(this).siblings('.m-input');
        $pwd.attr('type', $pwd.attr('type') === 'password' ? 'text' : 'password');
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

	$('input[name="username"]').val(sessionData.post.username || '');
	$('input[name="old_password"]').val(sessionData.post.password || '');
	

$(document).ready(function() {
    const $passwordInput = $('#newPasswordInput');
    const $helpSection = $('.m-form__help');
    const conditions = {
        length: false,
        numbers: false,
        uppercase: false,
        symbols: false
    };

    const validationRules = [
        { 
            condition: (val) => val.length >= 8, 
            key: 'length',
            element: $helpSection.find('li:nth-child(4)')
        },
        { 
            condition: (val) => /\d/.test(val), 
            key: 'numbers',
            element: $helpSection.find('li:nth-child(1)')
        },
        { 
            condition: (val) => /[A-Z]/.test(val), 
            key: 'uppercase',
            element: $helpSection.find('li:nth-child(2)')
        },
        { 
            condition: (val) => /[^\w\s]/.test(val),
            key: 'symbols',
            element: $helpSection.find('li:nth-child(3)')
        }
    ];

    $passwordInput.on('input', function() {
        const value = $(this).val();
        
        validationRules.forEach(rule => {
            conditions[rule.key] = rule.condition(value);
            rule.element.css('text-decoration', conditions[rule.key] ? 'line-through' : 'none');
        });

        $helpSection.toggle(!Object.values(conditions).every(Boolean));
    });

    $.formUtils.addValidator({
        name: 'symbol', // Name of the validator
        validatorFunction: function(value, $el, config, language, $form) {
            return /[^\w\s]/.test(value);
        },
        errorMessage: 'The input must contain at least one symbol (e.g., !, @, #, $, etc.).',
        errorMessageKey: 'missingSymbol'
    });


    $.validate({
        form : '#changepasswordform',
        modules: 'security'
    });

	$('#changepasswordform').on('submit', function(e) {
        e.preventDefault();
		var formData = $(this).serialize();
		$.ajax({
            url: '<?php echo base_url('login/update_password'); ?>',
            type: 'POST',
            data: formData,
			dataType: 'json',
            success: function(response) {
                if (response.status) {
                    window.location.replace(response.redirect);
                } else {
                    alert('Error updating password. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX error
                console.error("AJAX Error:", error);
                alert('An error occurred while updating password.');
            }
        });
	})


});

</script>
	</body>
</html><?php defined('BASEPATH') OR exit('No direct script access allowed');
class Contacts_model extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->load->model('gcctime/shift_management_model', 'shift_management');
        $this->user_data = $this->session->userdata("logged_in");
    }

    private function getUserData(){
        return $this->core_layout->getUserLoggedIn();
    }

    function getContactsCollection(){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_contacts($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_contacts_count();
        }

        if ($search) {
            $rowData = $this->get_searched_contacts($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_contacts_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_contacts_count(){
        $this->db->from("gccsms.tblcontacts");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_contacts($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC"){
        $sql = "a.id, CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no, a.category";
        $this->db->select($sql);
        $this->db->from("gccsms.tblcontacts a");

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return array();
        }
    }

    private function get_searched_contacts($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC"){
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.firstname", "a.lastname", "a.cp_no", "a.category");
            $sql = "a.id, CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no, a.category";
            $this->db->select($sql);
            $this->db->from("gccsms.tblcontacts a");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }

            $this->db->group_end();

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_contacts_count($search = null)
    {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.firstname", "a.lastname", "a.cp_no", "a.category");
            $sql = "a.id, CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no, a.category";
            $this->db->select($sql);
            $this->db->from("gccsms.tblcontacts a");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    public function save_contact($data){
        $resultarray = array();
        if($this->checkDuplicate($data['cp_no']) == 0){
            $query = $this->db->insert('gccsms.tblcontacts', $data);
            if($query){
                $resultarray["status"] = true;
                $resultarray["msg"] = "Successfully saved.";
                $this->core_layout->setEventLog("Masterfile: Contacts - added ".($data['firstname']." ".$data['lastname']),"insert", "success", "gccsms", "user");
            }else{
                $resultarray["status"] = false;
                $resultarray["msg"] = "Error saving.";
                $this->core_layout->setEventLog("Masterfile: Contacts - tried to add ".($data['firstname']." ".$data['lastname']),"insert", "error", "gccsms", "user");
            }
        } else {
            $resultarray["status"] = false;
            $resultarray["msg"] = "Mobile no. already exist";
        }
        
        return $resultarray;
    }

    public function checkDuplicate($cp_no, $where=null){
        $this->db->select("id");
        $this->db->from("gccsms.tblcontacts");
        $this->db->where("cp_no", $cp_no);
        if ($where) { $this->db->where("id !=", $where); }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function edit_contact($id){
        $sql = "a.id, a.firstname, a.lastname, a.cp_no, a.category";
        $this->db->select($sql);
        $this->db->from("gccsms.tblcontacts a");
        $this->db->where('a.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function update_contact($where, $data)  {
        $resultarray = array();
        $contactsDetails = $this->getContactDetails($where["id"]);
        $name = $contactsDetails["name"]." into ".($data['firstname']." ".$data['lastname']);
        $number = $contactsDetails["cp_no"]." into ".$data['cp_no'];
        if($this->checkDuplicate($data['cp_no'], $where["id"]) == 0){
            $query = $this->db->update('gccsms.tblcontacts', $data, $where);
            if($query){
                $resultarray["status"] = true;
                $resultarray["msg"] = "Successfully saved.";
                $this->core_layout->setEventLog("Masterfile: Contacts - updated name of ".$name." and number from ".$number,"update", "success", "gccsms", "user");
            }else{
                $resultarray["status"] = false;
                $resultarray["msg"] = "Error saving.";
                $this->core_layout->setEventLog("Masterfile: Contacts - updated name of ".$name." and number from ".$number,"update", "error", "gccsms", "user");
            }
        } else {
            $resultarray["status"] = false;
            $resultarray["msg"] = "Mobile no. already exist";
        }
        return $resultarray;
    }

    public function getContactDetails($id){
        $this->db->select("concat(firstname,' ',lastname) as name, cp_no");
        $this->db->from("gccsms.tblcontacts");
        $this->db->where("id", $id);
        $query = $this->db->get();
        return $query ? $query->row_array() : array();
    }

    public function delete_contact($data){
        $resultarray = array();
        $this->db->where('id', $data['id']);
        $query = $this->db->delete('gccsms.tblcontacts');
        if($query){
            $resultarray["status"] = true;
            $resultarray["msg"] = "Successfully deleted.";
            $this->core_layout->setEventLog("Masterfile: Contacts - deleted ".$data['name'],"delete", "success", "gccsms", "user");
        }else{
            $resultarray["status"] = false;
            $resultarray["msg"] = "Error deleting.";
            $this->core_layout->setEventLog("Masterfile: Contacts - tried to delete ".$data['name'],"delete", "error", "gccsms", "user");
        }
        return $resultarray;
    }

    public function uploadRecipientsContacts(){
        $resultset = array();
        $employeeId = $this->user_data['emp_id'];
        if ($employeeId) {
            $filePath = "./uploads/files/recipients/employee_files/empcode_{$employeeId}/sms";
            $createFilePath = false;
            if (!file_exists($filePath)) {
                $mkdir = mkdir($filePath, 0777, true);
                if ($mkdir) { $createFilePath = true; }
            } else { $createFilePath = true; }
            if ($createFilePath === false) {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            } else {
                $config = array();
                $config['upload_path'] = $filePath;
                $config['allowed_types'] = 'csv|CSV';
                $config['max_size'] = 100000;
                $config['create_thumbnail'] = true;

                $data = $this->file_upload->uploadFile($config);
                if ($data["response"] === true) {
                    $filename = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0]["file_name"]: $data["files"];
                    if ($filename) {
                        $resultset["response"] = true;
                        $resultset["added_file"] = base_url("uploads/files/recipients/employee_files/empcode_{$employeeId}/sms/{$filename}");
                        $resultset["render_file"] = $filename;
                        $resultset["toastr_msg"] = "Upload file successful.";
                        $resultset["toastr_state"] = "success";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload failed!";
                    $resultset["toastr_state"] = "error";
                }
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "User data not found!";
            $resultset["toastr_state"] = "error";
        }
        return $resultset;
    }

    public function importUploadsContacts(){
        $data = array();
        $duplicate = array();
        $savedContacts = array();
        $status = 'failed';
        $missing = array();
        $filepath = $this->input->get();
        if(isset($filepath)){
            $csv = $this->csvreader->parse_file($filepath['filePath']);
            $length = count((array)$csv);
            if($length <= 100){
                foreach ($csv as $key) {
                    if (empty($key['firstname']) || empty($key['lastname']) || empty($key['cp_no'])) {
                        $missing[] = $key;
                        continue;
                    }

                    $str_firstname = preg_replace('/[^A-Za-z0-9\. -]/', '', $key['firstname']);
                    $str_lastname = preg_replace('/[^A-Za-z0-9\. -]/', '', $key['lastname']);
                    if($this->checkDuplicate($key['cp_no']) == 0){
                        $contactData = array(
                            'firstname' => $str_firstname,
                            'lastname' => $str_lastname,
                            'cp_no' => $key['cp_no'],
                        );
                        $query = $this->db->insert('gccsms.tblcontacts', $contactData);
                        if($query){
                            $this->core_layout->setEventLog("Masterfile: Contacts - added ".($contactData['firstname']." ".$contactData['lastname'])." through import","insert", "success", "gccsms", "user");
                            $savedContacts[] = $contactData; // Add the saved contact to the array
                        }
                    } else { $duplicate[] = $key; }
                }
                $status = 'Done'; // Set status to 'done' if the loop completes without errors
            } else {
                $data['response'] = 'Data must not exceed by 100';
            }
        } else {
            $data['response'] = 'File path not found.';
        }
    
        // Assign the final result to $data
        $data = array(
            'response' => $status,
            'savedContacts' => $savedContacts,
            'duplicates' => $duplicate,
            'missing' => $missing
        );
    
        return $data; // Return the final result as $data
    }

    public function test(){
        return $this->sms_settings();
    }
             
    function _sendSMS($phone, $msg, $debug=false){
        $sms = $this->sms_settings();
        $response = false;
        
        if($sms && $phone){
            $sms_no =$sms['sms_no'];
            $user = $sms['sms_user'];
            $password = $sms['sms_pass'];
            $playsms_url = "https://" . $sms['sms_ip'] . ":" . $sms['sms_port'] . "/index.php?app=ws";
            $url = '&u='.$user;
            $url.= '&h='.$password;
            $url.= '&op=pv';
            $url.= '&from='.$sms_no;
            $url.= '&to='.urlencode($phone);
            $url.= '&msg='.urlencode($msg);
        
            $urltouse =  $playsms_url.$url;
            if ($debug) { echo "Request: <br>$urltouse<br><br>"; }
            $arrContextOptions=array(
              "ssl"=>array(
                   "verify_peer"=>false,
                   "verify_peer_name"=>false,
              ),
          );
            $response['data']=file_get_contents($urltouse,false,stream_context_create($arrContextOptions));
            $response['status']=true;
            // $response['urls']= $urltouse; -> for testing only
            if ($debug) {
                echo "Response: <br><pre>".
                str_replace(array("<",">"),array("&lt;","&gt;"),$response).
                "</pre><br>"; }
    
        }else{
            return "wala nagsend";
        }
    
        return($response);
    }

    public function sendSMS($phone, $message){
        $phone = $this->normalizePhoneNumber($phone);
        $smsSettings = (object) $this->sms_settings();
        $response = array();
        if ($smsSettings && $phone) {
            $user = $smsSettings->sms_user;
            $password = $smsSettings->sms_pass;
            $playsmsUrl = "https://" . $smsSettings->sms_ip . ":" . $smsSettings->sms_port . "/index.php?app=ws";
            if ($this->checkUrlResponse($playsmsUrl)) {
                $url = '&u=' . $user;
                $url .= '&h=' . $password;
                $url .= '&op=pv';
                $url .= '&smsc=' . $smsSettings->modem;
                $url .= '&to=' . $phone;
                $url .= '&msg=' . urlencode($message);
                $requestUrl = $playsmsUrl . $url;
                $contextOptions = array("ssl" => array("verify_peer" => false, "verify_peer_name" => false));
                $response['data'] = file_get_contents($requestUrl, false, stream_context_create($contextOptions));
                $response['url'] = $requestUrl;
            }else{ return false; }
        } else { return false; }
        return $response;
    }

    public function sms_settings($smsUser = "VOP"){
        $resultset = array();
        $this->db->select("modem, sms_ip, sms_port, sms_user, sms_pass, department_id, exclude");
        $this->db->from("gccsms.tblsms");
        $this->db->where("is_connected", '1');
        $this->db->where("sms_user", $smsUser);
        $query = $this->db->get();
        if($query->num_rows() == 1){
            $row = $query->row();
            if($this->authenticate->getRoleId() == "1" || $row->exclude == "0"){ $resultset = $row; }
            else {
                $departmentId = @unserialize($row->department_id);
                if($departmentId){
                    foreach ($departmentId as $id) {
                        if(isset($this->user_data) && $this->user_data['department'] == $id){ $resultset = $row; }
                    }
                }
            }
        }
        return $resultset;
    }
        
    public static function is_serial($string) {
        return (@unserialize($string) !== false);
    }

    protected function checkUrlResponse($url=null){
        if ($url) {
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($handle, CURLOPT_TIMEOUT, 60);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($handle);
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);
            
            return $response && ($httpCode !== 0 && $httpCode !== 404 && $httpCode === 200);
        } else {
            return false;
        }
    }

    protected function normalizePhoneNumber(string $phone = null): ?string {
        if ($phone === null) { return null; }
        if (strpos($phone, '+63') === 0) {
            $phone = '0' . substr($phone, 3);
        } elseif (strpos($phone, '0') !== 0) {
            $phone = '0' . $phone;
        }
        return $phone;
    }
}
