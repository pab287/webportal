		<?php if(isset($no_sidenav) && $no_sidenav): ?></div><?php endif; ?>
		</div>
	</div>
</div>
<!-- end:: Page -->	    
<!-- begin::Scroll Top -->
<div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
	<em class="la la-arrow-up"></em>
</div>
<!-- end::Scroll Top -->

<!--start::HRIS modals-->
<?php 
	if($this->uri->segment(1) == "hris"):
		$this->load->view("hris/masterfile/employee/notification/modals/settings");
	endif;
?>
<!--end ::HRIS modals-->
<?php
	$privilegeName = $this->core_layout->getPrivilegeName();
	$_actions = array();
	if($privilegeName){				
		$actions = $this->core_layout->generatePrivilegeAction();
		if(isset($actions[$privilegeName]) && $actions[$privilegeName]){
			$_actions = $actions[$privilegeName];
		}
		if(isset($actions["global_privileges"]) && $actions["global_privileges"]){
			foreach($actions["global_privileges"] as $privilege){
				if(!in_array($privilege, $_actions)){
					$_actions[] = $privilege;
				}
			}
		}
		
		$defaultPrivileges = $this->core_layout->listXml();
		if($defaultPrivileges){
			foreach($defaultPrivileges as $privilege){
				$nPrivilege = "btn".ucwords(strtolower($privilege));
				if(!in_array($nPrivilege, $_actions)){
					$_actions[] = $nPrivilege;
				}
			}
		}

		//$tempActions = array("btnBack", "btnCancel", "btnClose");
		$tempActions = array("btnBack", "btnClose");
        foreach ($tempActions as $key => $value) { if(!in_array($value, $_actions)){ $_actions[] = $value; }}
	}
	
?>
<?php $actions = $this->core_layout->getCurrentActions(); ?>

<!-- for cash advance notification role -->
<?php /*** $for_ca_notif = $this->core_layout->personal_roles_for_notif(); ***/ ?>

<!-- for cash advance notification role -->

<!-- script src="<?=base_url('node_modules/socket.io/client-dist/socket.io.js') ?>"></script -->
<script>
var module_id = <?php echo $this->authenticate->getCurrentModuleId() ?>;
var _currentActions = <?php echo json_encode($actions); ?>;
var _actions = <?php echo json_encode($_actions); ?>;

/*** for cash advance notification role ***/
/*** var _for_ca_actions = <?=json_encode($for_ca_notif) ?>; ***/
let company_id = <?=$this->session->userdata("logged_in")['company'] ? $this->session->userdata("logged_in")['company'] : 0;  ?>;
/*** for cash advance notification role ***/

// console.log(_currentActions);
// console.log(_currentActions);
var module_id = <?php echo $this->authenticate->getCurrentModuleId() ?>;
var triggerActionPrivileges = function(){
	var _currentPage = $(".m-content");
	/*** var _buttons = _currentPage.find("button, a.btn"); ***/
	/*** var _buttons = _currentPage.find("button[type=button].btn, button[type=submit].btn, a.btn, a.m-nav__link"); ***/
	var _buttons = _currentPage.find("button[type=button].btn, a.btn, a.m-nav__link");
	
	var _modalPage = $(".modal");
	var _modalButtons = _modalPage.find("button[type=submit], button[type=button].btn, a.btn");
	
	var _currentTable = _currentPage.find("table.dataTable");
	if(typeof _buttons !== "undefined"){
		_buttons.each(function(ii, vv){
			var found = false;
			var currentAction = $(vv);
			var currentAttr = currentAction.attr("data-dismiss");
			if(typeof currentAttr == "undefined"){
				_actions.forEach(function(value, key){
					currentClass = currentAction.hasClass(value);
					if(currentClass){ found = true; }
				});
			}else{
				found = true;
			}
			
			if(found == false){
				if(currentAction.hasClass("m-nav__link")){
					currentAction = currentAction.parent("li");
				}
				currentAction.remove();
			}
		});
	}
	
	if(typeof _modalButtons !== "undefined"){
		_modalButtons.each(function(ii, vv){
			var found = false;
			var currentAction = $(vv);
			var currentAttr = currentAction.attr("data-dismiss");
			if(typeof currentAttr == "undefined"){
				_actions.forEach(function(value, key){
					currentClass = currentAction.hasClass(value);
					if(currentClass){ found = true; }
				});
			}else{
				found = true;
			}
			
			if(found == false){ currentAction.remove(); }
		});
	}
}
// notification global
var vmTopNotification = new Vue({
        el: "#notification",
        data: { rows: {}, count: 0 },
        methods: {
            getToDate: function(date){
                var newDate = new Date(date);
                return newDate.toDateString();
            },
        }
    });

let vmHRISNearingOneMonthNotification = new Vue({
        el: "#nearingonemonth",
        data: { rows: {}, count: 0 },
        methods: {

        }
    });

let vmHRISNearingOneMonthPageNotification = new Vue({
	el: "#nearingonemonthpage",
	data: { rows: {}, count: 0 },
	methods: {
		redirectViewUrl : function(id){
			return siteUrl("hris/masterfile/view_employee_masterfile/"+id);
		}
	}
});
jQuery(document).ready(function(){
	triggerActionPrivileges();
	// ajax for notification
	/*** temporary comment to speedup loading time ***/
	/*** var notifBell = [];
	$.ajax({
        url: baseUrl("users/get_all_notif"),
        method: 'get',
        contentType: 'application/json',
        success: function(data){
			console.log(data);
            if(data.length != 0){
				notifBell.push("overdue");
                vmTopNotification.rows = Object.assign({}, data);
                vmTopNotification.count = data.length;
                $("#notifBadge").html(data.length);
				$("#notification .m-dropdown__header-title").html(data.length+" New");
				$("#m_topbar_notification_icon #notifBar").text(notifBell.length);
            }else{
				$("#notification .m-dropdown__header-title").html("No New");
				$("#m_topbar_notification_icon #notifBar").text("0");
            }
        }
    });
    $(document).on("click","#topbar_notifications_overdue #notif_area .notif", function(){
        var div_id = $(this).attr("value");
        window.location.href = baseUrl("eforms/borrowing/overdue_borrowing/?data="+div_id);
    });

	if(module_id == 1){
		//ajax call for nearing one month
		let nearingbell = [];
		// $.ajax({
		// 	url: baseUrl("hris/masterfile/get_nearing_one_month_employees"),
		// 	method: 'get',
		// 	contentType: 'application/json',
		// 	success: function(data){
		// 		if(data.length != 0){
		// 			nearingbell.push("nearingonemonth");
		// 			vmHRISNearingOneMonthNotification.rows = Object.assign({}, data);
		// 			vmHRISNearingOneMonthNotification.count = data.length;

					vmHRISNearingOneMonthPageNotification.rows = Object.assign({}, data);
					vmHRISNearingOneMonthPageNotification.count = data.length;
					$("#nearingBadge").html(data.length);
					$("#nearingonemonth .m-dropdown__header-title").html(data.length+" New");
					$("#m_topbar_hris_notification_icon #notifHRISBar").text(nearingbell.length);
				}else{
					$("#nearingonemonth .m-dropdown__header-title").html("No New");
					$("#m_topbar_hris_notification_icon #notifHRISBar").text("0");
				}
			}
		});
	} ***/
	/*** temporary comment to speedup loading time ***/
});

if(typeof idleTimerTrigger !== "undefined" && typeof idleTimerTrigger == "function"){
	idleTimerTrigger();
}

// var socket = io('http://'+window.location.hostname+':3000');
</script>

<!-- global cash advance notification -->
<!-- <script src="<?php //echo base_url('assets/js/global_notification.js'); ?>"></script> -->
<!-- global cash advance notification -->

<?php  if($this->authenticate->getCurrentModuleId() == 13): ?>
	<?php 
		$modules = "eforms";
		$moduleResource = array("eforms-borrowing");
	?>
	<?php if($this->authenticate->getPrivilegeChecker($moduleResource, $modules)): ?>
		<!-- <script src="<?//=base_url('assets/js/eforms/borrowing/notif.client.js') ?>"></script> -->
	<?php endif; ?>
<?php elseif($this->uri->segment(1) == "hris"): ?>
	<!-- <script src="<?//=base_url('assets/js/hris/hris.client.js') ?>"></script> -->
<?php endif; ?>

<?php echo $this->core_layout->getStoredFooterJs(); ?>
<?php $this->load->view("gcctime/attendance/modals/attendance_log") ?>
</body>
<!-- end::Body -->
</html>