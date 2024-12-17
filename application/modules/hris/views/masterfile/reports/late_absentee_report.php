<style>
    li.select2-selection__choice {
        white-space: pre-line;
        max-width: 90%;
        line-height: 20px;
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
            <ul class="nav nav-tabs nav-fill">
                <li class="nav-item">
                    <a class="nav-link active show" data-toggle="tab" href="#m_tabs_late_report">EMPLOYEES LATE REPORT</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#m_tabs_absentee_report">EMPLOYEES ABSENTEE REPORT</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="m_tabs_late_report" role="tabpanel">
                    <?php $this->load->view("hris/masterfile/reports/contents/employee_late_report"); ?>
                </div>
                <div class="tab-pane" id="m_tabs_absentee_report" role="tabpanel">
                    <?php $this->load->view("hris/masterfile/reports/contents/employee_absentee_report"); ?>
                </div>
            </div>
        </div>
    </div>
</div>
