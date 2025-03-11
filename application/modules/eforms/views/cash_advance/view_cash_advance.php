<style>
label.col-6.px-0.colon--after:after {
    content: ":";
    margin-left: 5px;
}

@media (max-width: 767px) {
   .modal-footer {
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
    }
}

@media screen and (max-width: 690px){
    #buttons{
        flex-wrap
        flex-wrap: wrap;
    }

    #buttons button, #buttons a{
        flex: 1 1 22%;
        max-width: 100%;
    }

    #buttons button:not(:last-child), #buttons a:not(:last-child){
        margin: 0 0 10px 0 !important;
    }
}

@media screen and (max-width: 480px){
    #buttons button, #buttons a{
        flex: 0 0 100%;
        max-width: 100%;
    }
}
@media screen and (max-width: 420px){
    #status_detail{
        font-size: 12px !important;
    }
}
</style>
<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <?php 
                                    if(isset($_GET['type'])){
                                        if($_GET['type'] == 'pyrll'){
                                            $ca_type = "?type=pyrll";
                                        }else if($_GET['type'] == 'acctg'){
                                            $ca_type = "?type=acctg";
                                        }else{
                                            $ca_type = "?type=approval";
                                        }
                                    }
                                ?>
                                <?php $temp_url = (isset($_GET['notif']))?site_url("eforms/cash_advance/pending_balance") . $ca_type:"masterfile" ?>
                                <a type="button" href="<?=$temp_url ?>" title="Back to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                                <h3 class="m-portlet__head-text">
                                    Cash Advance FORM
                                </h3>
						</div>
					</div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <form action="#" id="form_cash_advance" class="m-form m-form--fit form-horizontal">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="m-portlet__body">
                        <div id="cash_advance_renderer">
                            <div class="form-group m-form__group row" :class="loading_content === true ? '':'m--hide'">
                                <div class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                    <h3 class="m--font-brand">Loading Content Please Wait . . . </h3>
                                </div>
                                <div class="col-1 col-md-1 col-lg-1 col-sm-1 col-xs-12">
                                    <div class="m-loader m-loader--lg" style="width: 30px; display: inline-block;"></div>
                                </div>
                            </div>
                            <div class="row" :class="loading_content === true ? 'm--hide':''">
                                <div class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Reference #: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="cash_advance_no">
                                            <h5 v-text="vm_tab1.reference_no">&nbsp;</h5>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Employee Name:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="employee">
                                            <b v-text="vm_tab1.display_name">&nbsp;</b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Employment Status:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="emp_status">
                                            <b v-text="vm_tab1.emp_status">&nbsp;</b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Employment Date:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="emp_date">
                                            <b v-text="moment(vm_tab1.date_employed).format('LL')">&nbsp;</b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-3">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Company:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="company">
                                            <p class="mb-0"><b v-text="vm_tab1.company"></b></p>
                                            <p class="mb-0"><span v-text="vm_tab1.department"></span></p>
                                            <p class="mb-0"><span v-text="vm_tab1.position"></span></p>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-3">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Status:</label>
                                        <div class=" col-md-8 col-sm-8 col-xs-12" id="status">
                                            <span id="status_detail"><b v-text="vm_tab1.status">&nbsp;</b></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Last Vale:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="last_vale">
                                            <h5 v-text="vm_tab1.last_vale">&nbsp;</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Amount Applied:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="amt_applied">
                                            <b v-text="vm_tab1.amt_applied">&nbsp;</b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Deduction Amount:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="amt_deduct">
                                            <b v-text="vm_tab1.amt_to_b_deducted"></b><strong id="percent_sign" class="ml-2"></strong>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Purpose:</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="purpose">
                                            <p class="mb-0"><b v-text="vm_tab1.purpose">&nbsp;</b></p>
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <div class="form-group m-form__group row pb-0" v-if="vm_tab1.recommend_remarks">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Allowable:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="allowable">
                                                <b v-text="vm_tab1.recommend_remarks">&nbsp;</b>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row pb-0" v-if="vm_tab1.amt_approved">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Amount Approved:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="amount_approved">
                                                <h5 v-text="vm_tab1.amt_approved">&nbsp;</h5>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row pb-0">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Interest Percentage:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <template v-if="vm_tab1.acctg_ca_interest_percentage">
                                                <h5><span v-text="vm_tab1.acctg_ca_interest_percentage">&nbsp;</span><span class="ml-2">%</span></h5>
                                                </template>
                                                <template v-else>
                                                <h5><span>0.00</span><span class="ml-2">%</span></h5>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                    <div class="form-group m-form__group row pb-0">
                                        <h2 class="col-12">ACCOUNTING DETAILS</h2>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class="col-md-8 col-sm-8 col-xs-12">
                                        CASH ADVANCE BALANCE PENDING:
                                        </label>
                                        <div class=" text-right">
                                            <b class="cabp"> {{vm_tab1.acctg_ca_pending_formatted}} </b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class="col-md-8 col-sm-8 col-xs-12">
                                        CASH ADVANCE INTEREST:
                                        </label>
                                        <div class=" text-right">
                                            <b v-text="vm_tab1.acctg_ca_interest_formatted"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class="col-md-8 col-sm-8 col-xs-12">
                                        SSS LOAN:
                                        </label>
                                        <div class="  text-right" id="amt_ocharge">   
                                            <b v-text="vm_tab1.acctg_sss_loan_formatted"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class="col-md-8 col-sm-8 col-xs-12">
                                        HDMF LOAN:
                                        </label>
                                        <div class=" text-right">   
                                            <b v-text="vm_tab1.acctg_hdmf_loan_formatted"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class="col-md-8 col-sm-8 col-xs-12">
                                                MEDICAL LOAN:
                                        </label>
                                        <div class=" text-right">   
                                            <b v-text="vm_tab1.acctg_outside_loan_formatted"></b>
                                        </div>
                                    </div>
                                    <template v-if="count > 0">
                                        <div class="form-group m-form__group row pb-0">
                                            <label class="col-12"><span class="m--font-boldest">OTHER CHARGES</span></label>
                                        </div>
                                        <div class="form-group m-form__group row pt-0 pb-0">
                                            <div v-for="(item, index) in vm_charge" class="col-12 row m-0">
                                                <label class="col-6 px-0 colon--after">{{ item.description }}</label>   
                                                <b class="iacharge col-6 px-0 text-right">{{'₱' + ' ' + item.amount }}  </b>  
                                            </div>
                                        </div>
                                    </template>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class="">REMARKS:</label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <p class="mb-0">
                                                <template v-if="vm_tab1.acctg_bal_remarks2">
                                                    <b v-text="vm_tab1.acctg_bal_remarks2">&nbsp;</b>
                                                </template>
                                                <template v-else>
                                                    <span class="pull-right">N/A</span>
                                                </template>
                                            </p>  
                                        </div> 
                                    </div>
                                    <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Accounting Details Last Edited By: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">   
                                            <p>
                                            <span id="acctg_bal"></span>
                                            <span id="acctg_bal_dt"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x" :class="loading_content === true ? 'm--hide':''"></div>
                            <div class="row">
                                <div class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                    <div class="form-group m-form__group row pb-0" v-if="vm_tab1.created_by && vm_tab1.created_by !== 'N/A'">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Created By: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <p class="mb-0"><b v-text="vm_tab1.created_by"></b> ON <b v-text="moment(vm_tab1.created_dt).format('LLL')"></b></p>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pb-0" v-if="vm_tab1.last_edited_by && vm_tab1.last_edited_by !== 'N/A'">
                                        <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Last Edited By: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="last_edited_by">
                                        <p class="mb-0"><b v-text="vm_tab1.last_edited_by"></b> ON <b v-text="vm_tab1.last_edited_dt !== '0000-00-00 00:00:00' ? moment(vm_tab1.last_edited_dt).format('LLL') : 'N/A'"></b></p>
                                        </div>
                                    </div>
                                    <template v-if="vm_tab1.status == 'Awaiting Approval'">
                                        <div class="form-group m-form__group row pb-0" v-if="vm_tab1.final_approved_dt && vm_tab1.final_approved_by !== 'NULL'">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Undo for final approval by: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="final_approved_by">
                                            <p class="mb-0"><b v-text="vm_tab1.final_approved_by"></b> ON <b v-text="vm_tab1.final_approved_dt !== '0000-00-00 00:00:00' ? moment(vm_tab1.final_approved_dt).format('LLL') : 'N/A'"></b></p>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row" v-if="vm_tab1.final_approved_remarks && vm_tab1.final_approved_remarks !== 'N/A'">
                                                <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Remarks: </label>
                                                <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                    <p class="mb-0"><b v-text="vm_tab1.final_approved_remarks"></b></p>
                                                </div>
                                            </div>
                                    </template>
                                    <template v-if="vm_tab1.for_posting_by">
                                        <div v-if="vm_tab1.status == 'Awaiting Approval'" class="form-group m-form__group row pb-0" id="undo_posting">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Undo Posting by:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">   
                                                <b v-text="vm_tab1.for_posting_by"></b> ON <b v-text="moment(vm_tab1.for_posting_dt).format('LLL')"></b>
                                            </div>
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Remarks:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">   
                                                <p class="mb-0"><b v-text="vm_tab1.for_posting_remarks">&nbsp;</b></p>
                                            </div>
                                        </div>
                                        <div v-show="vm_tab1.status == 'For Posting'" class="form-group m-form__group row pb-0" id="for_posting">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">For Posting by:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="amt_deduct">   
                                                <b v-text="vm_tab1.for_posting_by"></b> ON <b v-text="moment(vm_tab1.for_posting_dt).format('LLL')"></b>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                    <div id="recommend_by" v-if="vm_tab1.recommend_by">
                                        <div class="form-group m-form__group row pb-0">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Recommended By: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.recommend_by"></b> ON <b v-text="moment(vm_tab1.recommend_dt).format('LLL')"></b></p>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row" v-if="vm_tab1.recommend_remark2 && vm_tab1.recommend_remark2 !== 'N/A'">
                                            <label class="col-12 col-md-12 col-lg-12 col-sm-12">Remarks: </label>
                                            <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                                <p class="mb-0"><b v-text="vm_tab1.recommend_remark2"></b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                    <div id="approved_by" v-if="vm_tab1.approved_by">
                                        <div class="form-group m-form__group row pb-0">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Marked As Approved By: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.approved_by"></b> ON <b v-text="moment(vm_tab1.approved_dt).format('LLL')"></b></p>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row" v-if="vm_tab1.approved_remarks && vm_tab1.approved_remarks !== 'N/A'">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Remarks: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.approved_remarks"></b></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cancelled_by" v-if="vm_tab1.cancelled_by">
                                        <div class="form-group m-form__group row pb-0">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Cancelled By: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.cancelled_by"></b> ON <b v-text="moment(vm_tab1.cancelled_dt).format('LLL')"></b></p>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Remarks: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.cancelled_remarks"></b></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="disapproved_by" v-if="vm_tab1.disapproved_by">
                                        <div class="form-group m-form__group row pb-0">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Disapproved By: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.disapproved_by"></b> ON <b v-text="moment(vm_tab1.disapproved_dt).format('LLL')"></b></p>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row pb-0">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Remarks: </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.disapproved_remarks"></b></p>
                                            </div>
                                        </div>
                                    </div>

                                    <template v-if="vm_tab1.status == 'For Posting'">
                                        <div class="form-group m-form__group row pb-0" id="undo_posted">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Undo Posted by:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">   
                                                <b v-text="vm_tab1.posted_by"></b> ON <b v-text="moment(vm_tab1.posted_dt).format('LLL')"></b>
                                            </div>
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Remarks:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">   
                                                <p class="mb-0"><b v-text="vm_tab1.posted_remarks"></b></p>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row pb-0" id="posted">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Posted by:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="amt_deduct">   
                                                <b v-text="vm_tab1.posted_by"></b> ON <b v-text="moment(vm_tab1.posted_dt).format('LLL')"></b>
                                            </div>
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12">Remarks:</label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">   
                                                <p class="mb-0"><b v-text="vm_tab1.posted_remarks"></b></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <template v-if="vm_tab1.status == 'Released'">
                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x" :class="loading_content === true ? 'm--hide':''"></div>
                                <div class="form-group m-form__group row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <h4>RELEASED DETAILS</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row pb-0" v-if="vm_tab1.created_by && vm_tab1.created_by !== 'N/A'">
                                            <label class=" col-md-4 col-lg-4 col-sm-4 col-xs-12"><strong>Released By: </strong></label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <p class="mb-0"><b v-text="vm_tab1.released_by"></b> ON <b v-text="moment(vm_tab1.released_dt).format('LLL')"></b></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row pb-0" v-if="vm_tab1.released_remarks">
                                            <label class=" col-md-12 col-lg-12 col-sm-12 col-xs-12"><strong>Remarks: </strong></label>
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                <p class="mb-0">
                                                    <b v-text="vm_tab1.released_remarks"></b>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group row pb-0">
                                            <label class=" col-md-5 col-lg-5 col-sm-12 col-xs-12"><strong>Debit Note #: </strong></label>
                                            <div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
                                                <h5 class="mb-0" v-text="vm_tab1.dn_no"> </h5>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row pb-0">
                                        <label class=" col-md-5 col-lg-5 col-sm-12 col-xs-12"> <strong>Voucher Reference #: </strong></label>
                                            <div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
                                                <h5 class="mb-0" v-text="vm_tab1.voucher_reference_no"></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div class="row mt-5 m--hide" id="list-content">
                            <div class="col-md-8">
                                <div class="form-group m-form__group row">
                                    <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12 table-responsive-m" style="overflow-x: scroll;">
                                        <table class="table table-striped table-bordered" id="table-cash-advance-content" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>CA #</th>
                                                    <th>Purpose</th>
                                                    <th>Date Approved</th>
                                                    <th>Active Balance</th>
                                                    <th>Payroll Status</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group m-form__group row">
                                    <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12 table-responsive">
                                        <table class="table table-striped table-bordered" id="table-file-content" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>File Name</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody> 

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="buttons"></div>
                </form>
            </div>
	    </div>
    </div>    
</div>

<!--modal recommend-->
<div class="modal fade" id="recommend_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Do you recommend?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "recommend_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class=" form-control-label">
                            Employee:
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12" id="employee_name">   
                            <b v-text="vm_tab2.display_name"></b>
                        </div>
                        <input type="hidden" name="employee" v-model="vm_tab2.employee"/>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class=" form-control-label">
                            Amount Applied:
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12" id="amt_applied" v-text="vm_tab2.amt_applied">   
                        
                        </div>
                        <input type="hidden" name="amt_applied" v-model="vm_tab2.amt_applied"/>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class=" col-form-label form-control-label">
                            Remarks:
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <textarea class="form-control" name="recommend_remark2" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <div class="col-12">
                            <small><i><b class="text-danger">Note:</b> I confirm that all the details and attachments have been thoroughly verified and deemed accurate.</i></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <template v-if="isLoading === false">
                        <button type="submit" class="btn btn-submit btn-primary btnSave">
                            <!-- Save -->
                            Verify and Recommend
                        </button>
                    </template>
                    <template v-else>
                        <span class="m-loader m-loader--brand" style="margin-right: 25px;"></span>
                    </template>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end recommend modal-->
<!--modal undo recommend note-->
<div class="modal fade" id="undo_recommend_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                   Undo Recommendation Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_recommend_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to undo the note of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal Set HR-->
<div class="modal fade" id="set_hr_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Set Payroll Balance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="set_hr_modal_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-12 form-control-label">
                            Allowable:
                        </label>
                        <div class="col-12">
                            <input type="text" name="recommend_remarks" class="form-control"  data-validation="required"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-12 form-control-label">
                            Please enter employee balance as of today: <!--and click "Save" button below:-->
                        </label>
                        <div class="col-12">
                            <input type="text" name="hr_bal_remarks" class="form-control"  data-validation="required"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 col-form-label form-control-label">
                            Payroll Remarks:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="hr_remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="save" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end Set HR modal-->
<!--modal Set HR-->
<div class="modal fade" id="edit_set_hr_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Set Payroll Balance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="edit_set_hr_modal_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-12 form-control-label">
                            Allowable:
                        </label>
                        <div class="col-12">
                            <input type="text" name="recommend_remarks" v-model="vm_tab3.recommend_remarks" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-12 form-control-label">
                            Please enter employee balance as of today: <!--and click "Save" button below:-->
                        </label>
                        <div class="col-12">
                            <input type="text" name="hr_bal_remarks" v-model="vm_tab3.hr_bal_remarks" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 col-form-label form-control-label">
                            HR Remarks:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="hr_remarks" v-text="vm_tab3.hr_remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="save" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end Set HR modal-->
<!--modal Set Acctg Bal-->
<div class="modal fade set_acctg_modal_details" id="set_acctg_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" >
                <h5 class="modal-title" id="exampleModalLabel">
                    Set Accounting Balances
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="set_acctg_modal_form">
              <!-- <input type="hidden" name="csrf_token"> -->
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            CASH ADVANCE BALANCE PENDING: <!--and click "Save" button below:-->
                        </label>
                        <div class="col-12">
                            <input type="text" required name="cash_advance_pending" class="form-control" id="cash_advance_pending" data-validation="required"  v-model="vm_tab1.acctg_ca_pending_formatted"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            CASH ADVANCE INTEREST: 
                        </label>
                        <div class="col-12">
                            <input type="text" required  name="cash_advance_interest" class="form-control" id="cash_advance_interest" v-model="vm_tab1.acctg_ca_interest_formatted"  data-validation="required" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            SSS LOAN: <!--and click "Save" button below:-->
                        </label>
                        <div class="col-12">
                            <input type="text" required name="sss_loan" class="form-control" id="sss_loan"  data-validation="required" v-model="vm_tab1.acctg_sss_loan_formatted" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            HDMF LOAN: <!--and click "Save" button below:-->
                        </label>
                        <div class="col-12">
                            <input type="text" required name="hdmf_loan" class="form-control" id="hdmf_loan"   data-validation="required" v-model="vm_tab1.acctg_hdmf_loan_formatted" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            MEDICAL LOAN: 
                        </label>
                        <div class="col-12">
                            <input type="text" required  name="outside_loan" class="form-control" id="outside_loan" data-validation="required" v-model="vm_tab1.acctg_outside_loan_formatted" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            OTHER CHARGES: <!--and click "Save" button below:-->
                        </label>
                        <div class="col-12">                            
                            <div class="px-3 mb-8">
                                <div id="meu-wrapper" class="mb-5"  v-if="count >= 1">
                                    <div v-for="(item, index) in vm_charge" class="meu-rows row align-item-center py-3" :id="'row_'+index" >
                                        <div class="col-7 ps-0">
                                            <span>Label</span>
                                            <input type="text" class="form-control resetafter form-control-solid meu-items" placeholder="Charge Label" id="oc_label" name="oc_label[]" required :value="item.description "/>
                                        </div>
                                        
                                        <div class=" ps-0">
                                            <span>Amount</span>
                                            <input type="text"  class="form-control resetafter form-control-solid meu-items charges_amount" id="oc_amount" name="oc_amount[]" required :value="item.amount"/>
                                        </div>

                                        <div class="col-1 d-flex align-items-end justify-content-center p-1">
                                            <span class="remove-meu svg-icon svg-icon-danger svg-icon-2hx" :id="index" data-row="row">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"/>
                                                    <rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="currentColor"/>
                                                    <rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div id="meu-wrapper" class=""  v-else>
                                    <div class="meu-rows row align-item-center" id="row1" >
                                        <!-- <div class="col-7 ps-0">
                                            <span>Label</span>
                                            <input type="text" class="form-control resetafter form-control-solid meu-items" placeholder="Charge Label" required id="otherChargersLabel"  name="oc_label[]" />
                                        </div>

                                        <div class=" ps-0">
                                            <span>Amount</span>
                                            <input type="text" placeholder="₱ 0.00"  class="form-control resetafter form-control-solid meu-items charges_amount" id="oc_amount"  name="oc_amount[]" required />
                                        </div>

                                        <div class="col-1 d-flex align-items-end justify-content-center p-1">
                                            <span class="remove-meu svg-icon svg-icon-danger svg-icon-2hx" id="remove" data-row="row">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"/>
                                                    <rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="currentColor"/>
                                                    <rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </div> -->
                                    </div>
                                </div>

                                <div class="px-6 form-group" align="right">
                                    <span class="add-new btn btn-sm btn-light-success" id="addScnt"><i class="fa fa-plus-circle"></i> Add New Charges</span>
                                </div>
                            </div>
                                               
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-12 col-form-label form-control-label">
                            Remarks:
                        </label>
                        <div class="col-12 text-left">

                            <textarea v-if="vm_tab1.acctg_bal_remarks2 ==''" class="form-control" name="acctg_bal_remarks2" id="acctg_bal_remarks2" rows="3" required>N/A</textarea>

                            <textarea v-else class="form-control" name="acctg_bal_remarks2" id="acctg_bal_remarks2" rows="3" data-validation="required" v-text="vm_tab1.acctg_bal_remarks2" ></textarea>
                        </div>
                    </div>

                    <input type="text" name="set_acctg_status" class="form-control" id="set_acctg_status" hidden/>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="for_approval" class="btn btn-submit btn-success btnSave">
                        For Approval
                    </button>
                    <button type="submit" id="save" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end Set acctg modal-->    
<!--modal Set edit Acctg Bal-->
<div class="modal fade" id="edit_set_acctg_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Set Accounting Balance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="edit_set_acctg_modal_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            Employee balance as of today:
                        </label>
                        <div class="col-12">
                            <input type="text" name="acctg_bal_remarks" v-model="vm_tab4.acctg_bal_remarks" class="form-control" readonly/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 col-form-label form-control-label">
                            Remarks:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="acctg_bal_remarks2" v-model="vm_tab4.acctg_bal_remarks2" rows="3" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end Set acctg modal-->  
<!--modal Approve-->
<div class="modal fade" id="approved_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Enter Approved Amount
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="approved_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group row">
                        <label class="col-12 form-control-label">
                            Amount Approved:
                        </label>
                        <div class="col-12">
                            <input type="text" name="amt_approved" id="amt_approved" class="form-control" data-validation="required"/>
                        </div>
                        <label class="col-12 form-control-label">
                         
                        </label>
                        <div class="col-12">
                           <i class="text-right m--font-danger" id="approve_message"></i>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12 form-control-label">
                            Notes:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="approved_remarks" rows="3" id="approve_remarks"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!--end Approve-->
<!--modal undo approval -->
<div class="modal fade" id="undo_approval_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Approval Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_approval_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to undo approval of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal disapprove-->
<div class="modal fade" id="disapprove_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Disapprove Form
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="disapprove_modal_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label form-control-label">
                            Reason:
                        </label>
                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                            <textarea class="form-control" rows="5" name="disapproved_remarks" id="reason_disapprove"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end disapprove modal-->
<!--modal undo disapproval -->
<div class="modal fade" id="undo_disapproval_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Disapproval Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_disapproval_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to undo disapproval of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal cancel-->
<div class="modal fade" id="cancel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Cancel Cash Advance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "cancel_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label form-control-label">
                            Remarks:
                        </label>
                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                            <textarea class="form-control" name="cancelled_remarks" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Save
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end cancel modal-->

<!--modal deduction status-->
<div class="modal fade" id="deduction_status_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Cash Advance Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-portlet m-portlet--bordered m-portlet--unair">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title align-items-center">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-file-1"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    <label for="" style="font-weight: 800">Remarks</label>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <span id="for_remarks"></span>
                    </div>
                    <div id="for_history" class="m-portlet__foot hideTable">
                        <table class="table" id="loan_history" width="100%">
                            <thead>
                                <th>Payroll Cut Off</th>
                                <th>Posted By</th>
                                <th>Amount</th>
                            </thead>      
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">
                                        <span class="m--font-boldest">TOTAL</span>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot> 
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal deduction status-->

<!--modal for posting -->
<div class="modal fade" id="posting_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Update to For Posting
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="posting_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <!-- <div class="form-group m-form__group">
                        <label class="form-control-label">
                            attachment: *
                        </label>
                        <div class="custom-file" >
                            <input type="file" name="file" id="fileupload" class="custom-file-input form-control" data-validation="required">
                            <span class="custom-file-control" id="file_append"></span>
                        </div>							    
					</div>
                    <div class="form-group m-form__group text-center">
                        <div class="gallery row justify-content-center"></div>
                    </div> -->
                    <div class="form-group row">
                        <label class="col-12 form-control-label">
                            Remarks: *
                        </label>
                        <!-- <div class="col-12">
                            <textarea class="form-control" name="for_post_remarks" rows="3" id="for_post_remarks" data-validation="required"></textarea>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        For Posting
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal for posting -->
<!--modal Undo For Posting-->
<div class="modal fade" id="undo_posting_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Undo For Posting
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="undo_posting_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 form-control-label">
                            Reason:
                        </label>
                        <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                            <textarea class="form-control" name="undo_posting_remark" rows="3" id="undo_post_remark" data-validation="required"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Undo Posted
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal Undo For Posting-->

<!--modal posted-->
<div class="modal fade" id="posted_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Update to Posted
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="posted_modal_form">
                <!-- <input type="hidden" name="csrf_token" value="<?php //echo $this->security->get_csrf_hash(); ?>"> -->
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group">
                        <label class="form-control-label">
                            attachment: *
                        </label>
                        <div class="custom-file" >
                            <input type="file" name="file" id="posted-fileupload" class="custom-file-input form-control" data-validation="required">
                            <!-- <input type="hidden" id="path" name="path"/> -->
                            <!-- <input type="hidden" id="filename" name="filename[]"/> -->
                            <span class="custom-file-control" id="file_append"></span>
                        </div>							    
					</div>
                    <div class="form-group m-form__group text-center">
                        <!-- <img id="picture" name="picture" style="max-width: 250px; margin: 0 auto;"><br> -->
                        <div class="gallery row justify-content-center"></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12 form-control-label">
                            Remarks: *
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="posted_remarks" rows="3" id="post_remarks" data-validation="required"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Post
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal posted-->
<!--modal Undo Posted-->
<div class="modal fade" id="undo_posted_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Undo Posted
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="undo_posted_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 form-control-label">
                            Reason:
                        </label>
                        <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                            <textarea class="form-control" name="undo_posted_remark" rows="3" id="undo_post_remark" data-validation="required"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Undo Posted
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal Undo Posted-->

<!-- for final approval -->
<div class="modal fade" id="final_approval_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Update to For Final Approval
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="final_approval_form">
                <!-- <input type="hidden" name="csrf_token" value="<?php //echo $this->security->get_csrf_hash(); ?>"> -->
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group">
                        <label class="form-control-label">
                            attachment: *
                        </label>
                        <div class="custom-file" >
                            <input type="file" name="file" id="final-fileupload" class="custom-file-input form-control" data-validation="required">
                            <!-- <input type="hidden" id="path" name="path"/> -->
                            <!-- <input type="hidden" id="filename" name="filename[]"/> -->
                            <span class="custom-file-control" id="file_append"></span>
                        </div>							    
					</div>
                    <div class="form-group m-form__group text-center">
                        <!-- <img id="picture" name="picture" style="max-width: 250px; margin: 0 auto;"><br> -->
                        <div class="gallery row justify-content-center"></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12 form-control-label">
                            Remarks: *
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="final_remarks" rows="3" id="final_remarks" data-validation="required"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Submit
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!-- for final approval -->
<!-- undo awaiting approval -->
<div class="modal fade" id="undo_awaiting_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Undo Awaiting Approval
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="undo_awaiting_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 form-control-label">
                            Reason:
                        </label>
                        <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                            <textarea class="form-control" name="undo_awaiting_remark" rows="3" id="undo_awaiting_remark" data-validation="required"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Undo Awaiting Approval
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!-- undo awaiting approval -->
<!-- undo for final approval -->
<div class="modal fade" id="undo_for_final_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Undo For Final Approval
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="undo_for_final_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 form-control-label">
                            Reason:
                        </label>
                        <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                            <textarea class="form-control" name="approved_remarks" rows="3" id="undo_approved_remarks" data-validation="required"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn text-white btn-submit btn-primary  btnSave">
                        Undo For Final Approval
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!-- undo for final approval -->

<!-- released modal -->
<div class="modal fade" id="released" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Released
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="released-form" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group row align-items-center">
                        <label class="col-md-12 col-lg-12 col-sm-12 col-xs-12 form-control-label m-0 required">
                            Debit Note #
                        </label>
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" name="dn_number" id="dn_number" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-md-6 col-lg-6 col-sm-12 col-xs-12 form-control-label m-0 required">
                            Voucher Reference #
                        </label>
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" name="voucher_ref" id="voucher_ref" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-12 col-lg-12 col-sm-12 col-xs-12 form-control-label">
                            Remarks
                        </label>
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control" name="released_remarks" rows="3" id="released_remarks"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn text-white btn-submit btn-primary  btnSave">
                        Released
                    </button>
                    <button type="button" class="btn text-white btn-metal btnClose" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!-- released modal -->

<style>
.hideTable{
    display: none
}
#table-cash-advance-content tbody tr:hover{
    background: #c9cacc;
    cursor: pointer
}
#table-cash-advance-content tbody tr:hover td:not(:last-child){
    border-right: none !important;
}

@media print{
    #print_cash_advance {
        size: portrait;
    }
}
</style>
<div class="m-content" hidden>
	<div class="m-portlet__body" id="printableArea" style="text-transform: uppercase !important;">
    <style>
        @media print{ .printable-row_content{ page-break-inside: avoid; } }
    </style>
        <form id="print_cash_advance">
                <table width="100%" border="0" style="text-transform: uppercase;">
                    <td width="100%">
                        <table style="font-size:11pt;" width="100%" border="0">
                            <tr>
                                <td style="text-align: center;" class="text-center"><b><u>APPLICATION FOR CASH ADVANCE </u></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11pt;" width="100%" border="0">
                            <tr>
                                <td width="20%" style="text-align: left;" class="text-left"><img width="75" src="http://152.69.208.158/web/assets/logo.png"></td>
                                <td style="80%" class="text-center"></td>
                            </tr>
                        </table>
                        </br>
                        <table style="font-size:7pt;" width="100%"  border="0">
                            <tr>
                                <td width="20%">Date</td>
                                <td width="40%">:&nbsp;<b v-text="moment(vm_tab1.created_dt).format('LL')" id="created_dt"></b></td>
                                <td width="20%">CA #</td>
                                <td width="30%">:&nbsp;<b v-text="vm_tab1.reference_no"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Name</td>
                                <td width="40%">:&nbsp;<b v-text="vm_tab1.display_name" id="employee"></b></td>
                                <td width="20%">Status</td>
                                <td width="30%">:&nbsp;<b  v-text="vm_tab1.status" id="emp_status"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Position</td>
                                <td width="80%">:&nbsp;<b  v-text="vm_tab1.position" id="position"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Date Hired</td>
                                <td width="80%">:&nbsp;<b v-text="moment(vm_tab1.date_employed).format('LL')" id="date_employed"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">ID #</td>
                                <td width="80%">:&nbsp;<b v-text="vm_tab1.emp_idno" id="emp_idno"></b></td>
                            </tr>
                        </table>
                        <br>    
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Payroll O/S Balance</td>
                                <td width="40%">:&nbsp;<b  v-text="vm_tab1.hr_bal_remarks" id="hr_bal_remarks"></b></td>
                                <td width="20%">Last Vale</td>
                                <td width="30%">:&nbsp;<b v-text="vm_tab1.last_vale" id="last_vale"></b></td>
                            </tr>
                        </table>
                        <!-- <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Acctg O/S Balance</td>
                                <td width="40%">:&nbsp;<b v-text="vm_tab1.acctg_bal_remarks" id="acctg_bal_remarks"></b></td>
                                <td width="20%">Amount Applied</td>
                                <td width="30%">:&nbsp;<b v-text="vm_tab1.amt_applied" id="amt_applied"></b></td>
                            </tr>
                        </table> -->
                        <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Deduction Amount</td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="80%" id="amt_deduct">: &nbsp;<b id="percent_sign" v-text="vm_tab1.amt_to_b_deducted"></b><span v-if="vm_tab1.deduct_type == 'percentage'">%</span>
                                    </td>
                                </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Purpose</td>
                                    <td width="80%">:&nbsp;<b v-text="vm_tab1.purpose" id="purpose"></b></td>
                                </tr>
                        </table>
                        </br>
                        <table style="font: size 9pt;" width="100%" border="0">
                                    <tr>
                                        <td style="text-align: center;" class="text-center"><b><u>ACCOUNTING DETAILS</u></b></td>
                                    </tr>
                        </table>
                            </br>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr width="">
                                    <td width="20%">CASH ADVANCE BALANCE PENDING </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="40%" id="amt_deduct">:&nbsp;<b class="cabp">{{vm_tab1.acctg_ca_pending_formatted}} </b></td>
                                    <td width="20%">SSS LOAN </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="30%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{vm_tab1.acctg_sss_loan_formatted}}</b></td>
                                </tr>
                            </table>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">CASH ADVANCE INTEREST </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="40%" id="amt_deduct">:&nbsp;<b>{{vm_tab1.acctg_ca_interest_formatted}}</b></td>
                                    <td width="20%">HDMF LOAN </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="30%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{vm_tab1.acctg_hdmf_loan_formatted}}</b></td>
                                </tr>
                            </table>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">OTHER CHARGES:</td>
                                    <td width="40%">&nbsp;</td>
                                    <td width="20%">MEDICAL LOAN </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="30%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{vm_tab1.acctg_outside_loan_formatted}}</b></td>
                                </tr>
                            </table>
                            <table v-if="count >= 1" style="font-size:7pt;" width="100%" border="0"  >
                                <tr>
                                    <tr v-for="(item, index) in vm_charge">
                                        <td width="20%" style="padding-left:10px;">{{ item.description }}</td>
                                        <td width="80%">: <b class="iacharge">{{'₱' + ' ' + item.amount }}</b></td>
                                    </tr>
                                </tr>
                            </table>
                            <table style="font-size:7pt; padding-top:10px;" width="100%" border="0">
                                <tr>
                                    <td width="20%">CASH ADVANCE REMARKS </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="80%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{ vm_tab1.acctg_bal_remarks2 }}</b></td>
                                </tr>
                            </table>
                        <br>
                        <table style="font-size:9pt;" width="100%" border="0">
                            <tr>
                                <td width="33%">Marked Approved By</td>
                                <td width="33%">Amount Aproved</td>
                                <td width="33%">Remarks</td>
                            </tr>
                        </table>
                        </br>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="33%"><b v-text="vm_tab1.approved_by" id="marked_approved_by1"></b>
                                    <hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"><b v-text="moment(vm_tab1.approved_dt).format('L')"></b>
                                </td>
                                <td width="33%"><b v-text="vm_tab1.amt_approved" id="amount_approved1"></b>
                                 <hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;">
                                 <b></br></b>
                                </td>
                                </td>
                                <td width="33%" id="app_rem1"><b v-text="vm_tab1.approved_remarks"></b>
                                <hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;">
                                </br>
                                </td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="50%" id="app_date1"></td>
                                <td width="50%"></td>
                            </tr>
                        </table>
                        </br>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="50%"><b v-text="vm_tab1.acctg_bal_by" id="acctg_bal_by"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                <td width="50%">&nbsp;<hr style="margin:0px 0px 0px 0px;border-top: 1px solid black;"></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="50%">Accounting</td>
                                <td width="50%">Received By</td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="50%"></td>
                                <td width="50%" class="text-right" style="text-align: right;">COMPANY COPY</td>
                            </tr>
                        </table>
                    </td>
                    </table>
                    <br>
                    <table class="printable-row_content" style="font-size:7pt; border-top: 1px dashed black; text-transform: uppercase;" width="100%">
                        <td width="50%">
                        <br>
                        <table style="font-size:11pt;" width="100%" border="0">
                            <tr>
                                <td style="text-align: center;" class="text-center"><b><u>APPLICATION FOR CASH ADVANCE </u></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11pt;" width="100%" border="0">
                            <tr>
                                <td width="20%" style="text-align: left;" class="text-left"><img width="75" src="http://152.69.208.158/web/assets/logo.png"></td>
                                <td style="80%" class="text-center"></td>
                            </tr>
                        </table>
                        </br>
                        <table style="font-size:7pt;" width="100%"  border="0">
                            <tr>
                                <td width="20%">Date</td>
                                <td width="40%">:&nbsp;<b v-text="moment(vm_tab1.created_dt).format('LL')" id="created_dt"></b></td>
                                <td width="20%">CA #</td>
                                <td width="30%">:&nbsp;<b v-text="vm_tab1.reference_no"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Name</td>
                                <td width="40%">:&nbsp;<b v-text="vm_tab1.display_name" id="employee"></b></td>
                                <td width="20%">Status</td>
                                <td width="30%">:&nbsp;<b  v-text="vm_tab1.status" id="emp_status"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Position</td>
                                <td width="80%">:&nbsp;<b  v-text="vm_tab1.position" id="position"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Date Hired</td>
                                <td width="80%">:&nbsp;<b v-text="moment(vm_tab1.date_employed).format('LL')" id="date_employed"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">ID #</td>
                                <td width="80%">:&nbsp;<b v-text="vm_tab1.emp_idno" id="emp_idno"></b></td>
                            </tr>
                        </table>
                        <br>    
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Payroll O/S Balance</td>
                                <td width="40%">:&nbsp;<b  v-text="vm_tab1.hr_bal_remarks" id="hr_bal_remarks"></b></td>
                                <td width="20%">Last Vale</td>
                                <td width="30%">:&nbsp;<b v-text="vm_tab1.last_vale" id="last_vale"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="20%">Acctg O/S Balance</td>
                                <td width="40%">:&nbsp;<b v-text="vm_tab1.acctg_bal_remarks" id="acctg_bal_remarks"></b></td>
                                <td width="20%">Amount Applied</td>
                                <td width="30%">:&nbsp;<b v-text="vm_tab1.amt_applied" id="amt_applied"></b></td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Deduction Amount</td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="80%" id="amt_deduct">: &nbsp;<b id="percent_sign" v-text="vm_tab1.amt_to_b_deducted"></b><span v-if="vm_tab1.deduct_type == 'percentage'">%</span>
                                    </td>
                                </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Purpose</td>
                                    <td width="80%">:&nbsp;<b v-text="vm_tab1.purpose" id="purpose"></b></td>
                                </tr>
                        </table>
                        </br>
                        <table style="font: size 9pt;" width="100%" border="0">
                                    <tr>
                                        <td style="text-align: center;" class="text-center"><b><u>ACCOUNTING DETAILS</u></b></td>
                                    </tr>
                        </table>
                            </br>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr width="">
                                    <td width="20%">CASH ADVANCE BALANCE PENDING </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="40%" id="amt_deduct">:&nbsp;<b class="cabp">{{vm_tab1.acctg_ca_pending_formatted}} </b></td>
                                    <td width="20%">SSS LOAN </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="30%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{vm_tab1.acctg_sss_loan_formatted}}</b></td>
                                </tr>
                            </table>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">CASH ADVANCE INTEREST </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="40%" id="amt_deduct">:&nbsp;<b>{{vm_tab1.acctg_ca_interest_formatted}}</b></td>
                                    <td width="20%">HDMF LOAN </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="30%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{vm_tab1.acctg_hdmf_loan_formatted}}</b></td>
                                </tr>
                            </table>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="20%">OTHER CHARGES:</td>
                                    <td width="40%">&nbsp;</td>
                                    <td width="20%">MEDICAL LOAN </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="30%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{vm_tab1.acctg_outside_loan_formatted}}</b></td>
                                </tr>
                            </table>
                            <table v-if="count >= 1" style="font-size:7pt;" width="100%" border="0"  >
                                <tr>
                                    <tr v-for="(item, index) in vm_charge">
                                        <td width="20%" style="padding-left:10px;">{{ item.description }}</td>
                                        <td width="80%">: <b class="iacharge">{{'₱' + ' ' + item.amount }}</b></td>
                                    </tr>
                                </tr>
                            </table>
                            <table style="font-size:7pt; padding-top:10px;" width="100%" border="0">
                                <tr>
                                    <td width="20%">CASH ADVANCE REMARKS </td>
                                    <!-- <td width="80%">:&nbsp;<b id="amount_deduct2"></b></td> -->
                                    <td width="80%" id="amt_deduct">:&nbsp;<b id="percent_sign">{{ vm_tab1.acctg_bal_remarks2 }}</b></td>
                                </tr>
                            </table>
                        <br>
                        <table style="font-size:9pt;" width="100%" border="0">
                            <tr>
                                <td width="33%">Marked Approved By</td>
                                <td width="33%">Amount Aproved</td>
                                <td width="33%">Remarks</td>
                            </tr>
                        </table>
                        </br></br>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="33%"><b v-text="vm_tab1.approved_by" id="marked_approved_by1"></b>
                                    <hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"><b v-text="moment(vm_tab1.approved_dt).format('L')"></b>
                                </td>
                                <td width="33%"><b v-text="vm_tab1.amt_approved" id="amount_approved1"></b>
                                 <hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;">
                                 <b></br></b>
                                </td>
                                </td>
                                <td width="33%" id="app_rem1"><b v-text="vm_tab1.approved_remarks"></b>
                                <hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;">
                                </br>
                                </td>
                            </tr>
                        </table>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="50%" id="app_date1"></td>
                                <td width="50%"></td>
                            </tr>
                        </table>
                        </br>
                        <table style="font-size:7pt;" width="100%" border="0">
                            <tr>
                                <td width="50%"><b v-text="vm_tab1.acctg_bal_by" id="acctg_bal_by"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                <td width="50%">&nbsp;<hr style="margin:0px 0px 0px 0px;border-top: 1px solid black;"></td>
                            </tr>
                        </table>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="50%">Accounting</td>
                                    <td width="50%">Received By</td>
                                </tr>
                            </table>
                            <table style="font-size:7pt;" width="100%" border="0">
                                <tr>
                                    <td width="50%"></td>
                                    <td width="50%" class="text-right" style="text-align: right;">EMPLOYEE COPY</td>
                                </tr>
                            </table>
                        </td>
                    </table>
                </tr>
            </table>
        </form>
    </div>
</div>
<script>
$(document).ready(function(){
    $("#for_approval").click(function(){
        $("#set_acctg_status").val("Awaiting Approval");
    });

    $("#save").click(function(){
        $("#set_acctg_status").val("Accounting Balance Pending");
    });
});

</script>