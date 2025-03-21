<style>
    #dropdownMenuButton:after {
        display: none;
    }
    table.dataTable {
        table-layout: fixed;
        word-break: break-all;
    }
    #resume_page a:hover{
        text-decoration: none;
    }
</style>
<div class="m-content">
<div class="m-portlet m-portlet--mobile">
<div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        CRS REPORTS
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools"></div>
        </div>
        <div class="m-portlet__body">
        <div class="form-group row">
        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">

                                <label class="control-label">Status</label>
                                <select id="crs_report_status" name="status" class="form-control">
                                    <option value=""></option>
                                    <option value="pending">PENDING</option>
                                    <option value="forinterview">FOR INTERVIEW</option>
                                    <option value="doneinterview">DONE INTERVIEW</option>
                                    <option value="pooling">POOLING</option>
                                    <option value="blacklisted">BLACKLISTED</option>
                                    <option value="hired">HIRED</option>
                                    <option value="shortlisted">SHORTLISTED</option>
                                    <option value="eligible">ELIGIBLE</option>
                                    <option value="disqualified">DISQUALIFIED</option>
                                    <option value="reserve">RESERVE</option>
                                    <option value="overqualified">OVERQUALIFIED</option>
                                    <option value="disregard">DISREGARD</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Recruitment Source</label>
                                <select id="crs_report_recruitment" name="recruitment"
                                        class="form-control" data-validation="required">
                                    <option value=""></option>
                                    <option value="Mynimo">MYNIMO</option>
                                    <option value="Jobstreet">JOBSTREET</option>
                                    <option value="Facebook">FACEBOOK</option>
                                    <option value="Linkedin">LINKEDIN</option>
                                    <option value="Walk In">WALK IN</option>
                                    <option value="REFERRAL">REFERRAL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12 m--hide interview_dt">
                            <div class="form-group">
                                <label class="control-label">Interview Date</label>
                                <input type="text" name="interview_dt" id="interview_dt" placeholder="SELECT DATE" class="form-control date" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12 m--hide hired_dt">
                            <div class="form-group">
                                <label class="control-label">Hired Date</label>
                                <input type="text" name="hired_dt" id="hired_dt" placeholder="SELECT DATE" class="form-control date" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Application Method</label>
                                <select id="application_method" name="application_method"
                                        class="form-control" >
                                    <option value=""></option>
                                    <option value="Internal">Internal Encoding</option>
                                    <option value="Online">Online Application</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12 application_dt">
                            <div class="form-group">
                                <label class="control-label">Application Date</label>
                                <input type="text" name="application_dt" id="application_dt" placeholder="SELECT DATE" class="form-control date" autocomplete="off" readonly>
                            </div>
                        </div>

          </div>

          

        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">&nbsp;</div>
                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    
                        
                    
                    
                      </div>



                </div>
            </div>
            <div class="table-responsive-sm">
                <table class="table table-hover table-bordered" id="table-crs-report" width="100%">
                    <thead>
                    <tr>
                        <th></th>
                        <td></td>
                        <th></th>
                        <th>Name</th>
                        <th>School</th>
                        <th>Course</th>
                        <th>Position</th>
                        <th>Eligible Position/Tag</th>
                        <th>Recruitment</th>
                        <th>Application Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
</div>
</div>
<?php $this->load->view("modals/query_search") ?>
</div>
