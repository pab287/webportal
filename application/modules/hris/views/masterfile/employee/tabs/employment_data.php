<form id="frmEditEmploymentData" class="m-form m-form--fit m-form--label-align-right" method="post"
      action="<?php echo site_url("hris/masterfile/update_employee_employment_data"); ?>">
    <input type="hidden" name="id" v-model="vm_tab3.id"/>
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="current_status" v-model="vm_tab3._status">
    <input type="hidden" name="current_company_id" v-model="vm_tab3.current_company_id">
    <input type="hidden" name="current_department_id" v-model="vm_tab3.current_department_id">
    <input type="hidden" name="current_supervisor" v-model="vm_tab3.current_supervisor">
    <input type="hidden" name="current_position_id" v-model="vm_tab3.current_position_id" class="current_position">
    <div class="m-portlet__body">
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="company_id" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label" :class="!vm_tab3.current_company_id ? 'required' : ''">Company:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <div class="form-group m-form__group p-0">
                            <div class="input-group" v-if="vm_tab3.current_company_id">
                                <input type="text" class="form-control" disabled
                                       v-model="vm_tab3.company">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btnEdit btn m-btn m-btn--hover-success m-btn--icon"
                                            type="button"
                                            data-placement='right' data-toggle='m-tooltip' title='' data-original-title='Transfer Company'
                                            data-skin="dark"
                                            style="padding: 0.53rem 1rem;" onclick="openChangeCompanyDialog()">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </span>
                            </div>

                            <div v-if="!vm_tab3.current_company_id">
                                <select id="m--input-company_id" class="form-control m-input select2" name="company_id" placeholder="Select an option"
                                        data-validation="required" v-model="vm_tab3.company_id"></select>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="work_status" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">CLASSIFICATION:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <select class="form-control m-input" name="employee_status"
                                placeholder="Select an option" id="classification" data-validation="required"
                                v-model="vm_tab3.employee_status">
                            <option value="" disabled>&nbsp;</option>
                            <option value="Active">ACTIVE</option>
                            <option value="Inactive">INACTIVE</option>
                            <!--<option value="Contractor">CONTRACTOR</option>-->
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="department_id" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">Department:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <select id="m--input-department_id" class="form-control m-input select2" name="department_id" placeholder="Select an option"  data-validation="required"
                                v-model="vm_tab3.department_id"></select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="work_status" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">STATUS:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <select class="form-control m-input"
                                name="work_status" id="status"
                                placeholder="Select an option" data-validation="required">
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="position" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">Position:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <select id="m--input-position_id" class="form-control m-input select2" name="position" placeholder="Select an option"  data-validation="required"
                        v-model="vm_tab3.position"></select>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5 m--hide" id="rehire-button-container-employment-data">
                <a href="javascript:void(0)" class="m-alert m-alert--icon m-alert--outline alert alert-warning alert-dismissible fade show" data-toggle="modal" data-target="#modal-rehire"
                style="cursor:pointer; text-decoration: none">
					<div class="m-alert__icon">
						<i class="la la-warning"></i>
					</div>
					<div class="m-alert__text">
					  	<strong>REHIRE OPTION IS AVAILABLE!</strong>
					</div>
                </a>
            </div>
        </div>

        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x" v-show=" vm_tab3.level !== 'EXECUTIVE' "></div>
        <div class="row" v-show="vm_tab3.level !== 'MANAGERIAL'  && vm_tab3.level !== 'EXECUTIVE' ">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 col-form-label text-center">
                <label class="m-checkbox m-checkbox--check-bold m-checkbox--state-brand col-form-label" style="padding-top: 1px !important">
                    <input type="checkbox" id="is_two_level" :checked="vm_tab3.current_tl_supervisory == 1 ? true : false" name="tl_supervisory" value="1"> 
                    <label for="is_two_level"> TWO LEVEL SUPERIOR </label>
                    <span></span>
                </label>
                <span class="flaticon-questions-circular-button" data-placement='right' data-toggle='m-tooltip' title='' data-original-title='Click box to enable Two Level Superior' data-skin="dark" style="font-size: 15px; margin-left: 3px;"></span>
            </div>
        </div>
        <div class="row m--margin-bottom-10" v-show="vm_tab3.level !== 'EXECUTIVE' ">
            <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                <div class="form-group m-form__group row">
                    <label for="position" class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 col-form-label required">Immediate Superior:</label>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <select id="m--input-supervisor_id" class="form-control m-input select2" name="supervisor" placeholder="Select an option" v-model="vm_tab3.supervisor" data-validation="required">
                            <option value="0">None</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10" v-show="vm_tab3.level !== 'MANAGERIAL'  && vm_tab3.level !== 'EXECUTIVE'">
            <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8" v-show="vm_tab3.current_tl_supervisory == 1">
                <div id="remove-initial-class" class="form-group m-form__group row">
                    <label for="position" class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 col-form-label required">2ND Level Superior:</label>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <select id="m--input-manager_id" class="form-control m-input select2" name="manager" placeholder="Select an option" v-model="vm_tab3.manager" data-validation="required">
                            <option value="0">None</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="idno" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">Identification #:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <input type="number" name="idno" class="form-control m-input" maxlength="25" size="25" autocomplete="off"
                            v-model="vm_tab3.idno" data-validation="required" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="biometricno" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">Biometric #:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <input type="number" name="biometricno" class="form-control m-input" maxlength="7" size="7" autocomplete="off"
                            v-model="vm_tab3.biometricno" data-validation="required" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="level" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">Level / Ranking:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <select class="form-control m-input" name="level" placeholder="Select an option" data-validation="required"
                                v-model="vm_tab3.level" id="level">
                            <option value="SKILLED RANK AND FILE">SKILLED RANK AND FILE</option>
                            <option value="RANK AND FILE">RANK AND FILE</option>
                            <option value="SUPERVISORY">SUPERVISORY</option>
                            <option value="MANAGERIAL">MANAGERIAL</option>
                            <option value="EXECUTIVE">EXECUTIVE</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="work_mode" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">Work Mode:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <select class="form-control m-input select2" id="m--input-work_mode" name="work_mode" placeholder="Select an option" data-validation="required"
                                v-model="vm_tab3.work_mode">
                            <option value="Time Based">Time Based</option>
                            <option value="Flexi Time">Flexi Time</option>
                            <option value="Activity Based">Activity Based</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="payroll_type" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">Payroll Type:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <select class="form-control m-input select2" name="payroll_type" id="m--input-payroll_type_id"
                            placeholder="Select an option" data-validation="required" v-model="vm_tab3.payroll_type">
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                <div class="form-group m-form__group row">
                    <label for="payroll_type" class="col-sm-12 col-md-6 col-lg-6 col-xl-6 col-form-label required">Work Schedule:</label>
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <select class="form-control m-input select2" name="work_schedule"
                            placeholder="Select an option" data-validation="required" v-model="vm_tab3.work_schedule">
                            <option value="4">Default - NO TIME IN OR OUT</option>
                            <option value="3">Super Flexi - 1 IN OR 1 OUT</option>
                            <option value="2">Drivers - 1 IN AND 1 OUT</option>
                            <option value="1">Flexi - 1 IN AND 1 OUT</option>
                            <option value="0">Regular - 2 IN AND 2 OUT</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="date_start" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label required">Date Hired:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input type="text" id="m_datepicker-date_hired" name="date_start" class="form-control m-input" maxlength="12" size="12"
                                   autocomplete="off" data-validation="required" v-model="vm_tab3.date_start" readOnly={true}/>
                        </div>
                        <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="date_end_prob" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">Probee End Date:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input type="text" id="m_datepicker-date_end_prob" name="date_end_prob" class="form-control m-input" maxlength="12"
                                   size="12" autocomplete="off" v-model="vm_tab3.date_end_prob"/>
                                   
                        </div>
                        <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bootom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="date_regular" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">Regularized:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input type="text" id="m_datepicker-date_regular" name="date_regular" class="form-control m-input" maxlength="12"
                                   size="12" autocomplete="off" v-model="vm_tab3.date_regular" disabled/>
                        </div>
                        <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="date_end" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">Separated:</label>
                    <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input type="text" id="m_datepicker-date_end" name="date_end" class="form-control m-input" maxlength="12" size="12"
                                   autocomplete="off" v-model.lazy="vm_tab3.date_end" disabled />
                        </div>
                        <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="form-group m-form__group row">
                    <label for="date_resign" class="col-sm-6 col-md-3 col-lg-3 col-xl-3 col-form-label">Resignation Effective Date:</label>
                    <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input type="text" id="m_datepicker-date_resign" name="resignation_effective_date" class="form-control m-input" maxlength="12"
                                   size="12" autocomplete="off" v-model="vm_tab3.resignation_effective_date"/>
                        </div>
                        <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <span class="m-form__help col-form-label text-justify"><span class="m--font-bolder">Note! </span> Leave Blank if no resignation received.</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12" id="resignation_remarks" :style="vm_tab3.resignation_effective_date != '0000-00-00' && vm_tab3.resignation_effective_date ? 'display: block' : 'display: none'">
                <div class="form-group m-form__group row">
                    <label for="" class="col-sm-6 col-md-3 col-lg-3 col-xl-3 col-form-label">Performance Rating Remarks:</label>
                    <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3">
                        <p id="resign_remarks" class="col-form-label"></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="reason_row">
            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
            <div class="row m--margin-bottom-10">
                <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-10">
                    <div class="form-group m-form__group row">
                        <label for="reason_of_separation" class="col-xs-6 col-sm-6 col-md-4 col-lg-4 col-xl-4 col-form-label required">Reason for Separation:</label>
                        <div class="col-xs-6 col-sm-6 col-md-8 col-lg-8 col-xl-8">
                            <div class="input-group">
                                <textarea class="form-control m-input" 
                                    id="resign_reason" name="resign_reason" rows="5" style="min-height: 110px;"
                                    data-validation="required">{{vm_tab3.resign_reason ? vm_tab3.resign_reason : ''}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-10">
                <div class="form-group m-form__group row">
                    <label for="default_station" class="col-xs-6 col-sm-6 col-md-4 col-lg-4 col-xl-4 col-form-label">CURRENT STATION / LOCATION:</label>
                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-8 col-xl-8">
                        <select class="form-control m-input"
                                name="default_station" id="default_station" 
                                placeholder="Select an option">
                                <option></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-10">
                <div class="form-group m-form__group row">
                    <label for="work_station" class="col-xs-6 col-sm-6 col-md-4 col-lg-4 col-xl-4 col-form-label">SITE POINT LOCATIONS / PROJECTS: <span class="ml-3"><i class="flaticon-map-location"></i></span></label>
                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-8 col-xl-8">
                        <select class="form-control m-input"
                                name="work_station[]" id="station"
                                placeholder="Select an option" multiple>
                        </select>
                        <span class="m-form__help"><span class="m--font-bolder">Note!</span> This field option is used for mobile location tagging.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(in_array("save", $this->core_layout->getCurrentActions())): ?>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions">
            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btnSave btn-primary m-btn m-btn--air m-btn--custom btn-submit"><i class="la la-check mr-2"></i>Save</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <input type="hidden" name="id" v-model="vm_tab3.id" class="employee_id"/>
    <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-0x"></div>
</form>
<div class="modal fade" tabindex="-1" role="dialog" id="update_salary_history">
    
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="la la-edit mr-2"></i>Update Salary History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateSalaryModalForm">
                <div class="modal-body">
                
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="salary_employee_id" id="salary_employee_id">
                <input type="hidden" name="salary_employee_position" id="salary_employee_position">
                <!-- <input type="text" id="salary_current_position"> -->
                    <div class="form-group mt-3">
                        <label for="purpose">Effective Date</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                            <input type="text" id="m_datepicker-salary_effective_date" name="salary_effective_date" class="form-control m-input" maxlength="12" size="12"
                            autocomplete="off" data-validation="required" placeholder="Select Date" />
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="purpose">Salary Rate</label>
                        <input type="text" class="form-control money text-left" id="salary_rate" name="salary_rate">
                    </div>

                    <div class="form-group mt-4">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="salary_remarks" name="salary_remarks" autocomplete="off"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnAdd_performance_rating" id="save_salary_rating">Save</button>
                    <button type="button" class="btn btn-danger btnAdd_performance_rating" data-dismiss="modal">Close</button>
                </div>
                </form>
                
            </div>
        </div>
    </div>
<?php $this->load->view("masterfile/employee/modals/change_employee_company"); ?>
<input type="hidden" id="change_employment_info">