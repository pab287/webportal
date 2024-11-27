<div class="m-content">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active show" data-toggle="tab" href="#m_tabs_hire_separated">HIRED / SEPARATED EMPLOYEES REPORT</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#m_tabs_licenses_certifications">LICENSES AND CERTIFICATIONS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#m_tabs_training_seminar">TRAININGS AND SEMINARS REPORT</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#m_tabs_drivers_license">DRIVERS LICENSE REPORT</a>
                </li>
            </ul>                    

            <div class="tab-content">
                <div class="tab-pane active" id="m_tabs_hire_separated" role="tabpanel">
                    <?php $this->load->view("hris/masterfile/reports/contents/hired_separated_employees"); ?>
                </div>
                <div class="tab-pane" id="m_tabs_licenses_certifications" role="tabpanel">
                    <?php $this->load->view("hris/masterfile/reports/contents/employee_licenses_certifications"); ?>
                </div>
                <div class="tab-pane" id="m_tabs_training_seminar" role="tabpanel">
                    <?php $this->load->view("hris/masterfile/reports/contents/employee_trainings_seminars"); ?>
                </div>
                <div class="tab-pane" id="m_tabs_drivers_license" role="tabpanel">
                    <?php $this->load->view("hris/masterfile/reports/contents/employee_drivers_license"); ?>
                </div>
            </div>  
        </div>
    </div>
</div>
<style>
    .nav-tabs a.nav-link.active {
        font-size: 1.25rem;
        font-weight: 500 !important;
    }
    tr.dtrg-group.dtrg-start.dtrg-level-0 {
        background-color: #716ACA;
        color: #FFFFFF;
    }
    tr.dtrg-group.dtrg-start.dtrg-level-0 span:nth-child(1) {
        font-weight: bold;
    }
    tr.dtrg-group.dtrg-start.dtrg-level-0 span:nth-child(2n+0) {
        margin: 0 25px;
    }
    td.m--highlight--danger {
        color: #FF0000;
        font-weight: 700;
    }
</style>