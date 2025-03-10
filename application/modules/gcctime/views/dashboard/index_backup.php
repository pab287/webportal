<style type="text/css">
    #latelist:before, #real-time-attendances:before {
        display: none !important;
    }
</style>
<div class="m-content">
    <?php $this->load->view("dashboard/clock", array("logs" => $logs, "admin_privilege"=>$admin_privilege)); ?>
    <?php $roleId = $this->authenticate->getRoleId(); ?>
    <?php if (($roleId == 1 || $roleId == 2) || $admin_privilege == true): ?>
        <div class="row">
            <?php $this->load->view("dashboard/real_time_attendance", array("logs" => $real_time_attendances)); ?>
            <?php $this->load->view("dashboard/latemonitor"); ?>
            <?php $this->load->view("dashboard/absent"); ?>
        </div>
        <div class="row">
            <div class="col-xl-4">
                <?php $this->load->view("dashboard/undertimemonitor"); ?>
            </div>
            <?php $this->load->view("dashboard/attendancevariance"); ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php echo $this->load->view("dashboard/personal_attendance", array("biometric_id" => $biometric_id), true); ?>
        </div>
        <div class="row">
            <?php echo $this->load->view("dashboard/personal_attendance_yesterday", array("biometric_id" => $biometric_id), true); ?>
        </div>
    <?php endif; ?>
</div>