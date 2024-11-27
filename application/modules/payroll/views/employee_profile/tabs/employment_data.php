<form id="frmEditEmploymentData" class="m-form m-form--fit m-form--label-align-right" method="post"
      action="<?php echo site_url("payroll/employee/update_employee_employment_data"); ?>">
    <input type="hidden" name="id" v-model="vm_tab3.id"/>
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="current_status" v-model="vm_tab3._status">
    <input type="hidden" name="current_company_id" v-model="vm_tab3.current_company_id">
    <input type="hidden" name="current_department_id" v-model="vm_tab3.current_department_id">
    <input type="hidden" name="current_supervisor" v-model="vm_tab3.current_supervisor">
    <input type="hidden" name="current_position_id" v-model="vm_tab3.current_position_id">
    <input type="hidden" name="biometricno" v-model="vm_tab3.biometricno">
    <div class="m-portlet__body">
        <div class="row m--margin-bottom-10">
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="company_id" class="col-5 col-form-label text-right">Company:</label>
                    <div class="col-7">
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
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="department_id" class="col-5 col-form-label text-right">Department:</label>
                    <div class="col-7">
                        <select id="m--input-department_id" class="form-control m-input select2" name="department_id" placeholder="Select an option"
                                data-validation="required" v-model="vm_tab3.department_id"></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="position" class="col-5 col-form-label text-right">Position:</label>
                    <div class="col-7">
                        <select id="m--input-position_id" class="form-control m-input select2" name="position" placeholder="Select an option"
                                data-validation="required" v-model="vm_tab3.position"></select>
                    </div>
                </div>
            </div>
            <div class="col-5 col-md-5" hidden>
                <div class="form-group m-form__group row">
                    <label for="work_status" class="col-5 col-form-label text-right">CLASSIFICATION:</label>
                    <div class="col-7">
                        <select class="form-control m-input" name="employee_status"
                                placeholder="Select an option" id="classification" data-validation="required"
                                v-model="vm_tab3.employee_status">
                            <option value=""></option>
                            <option value="Active">ACTIVE</option>
                            <option value="Inactive">INACTIVE</option>
                            <!--<option value="Contractor">CONTRACTOR</option>-->
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10" hidden>
            <div class="col-5 col-md-5 offset-5">
                <div class="form-group m-form__group row">
                    <label for="work_status" class="col-5 col-form-label text-right">STATUS:</label>
                    <div class="col-7">
                        <select class="form-control m-input"
                                name="work_status" id="status"
                                placeholder="Select an option" data-validation="required"
                                v-model="vm_tab3.work_status">
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="tin_no" class="col-5 col-form-label text-right">TIN #:</label>
                    <div class="col-7">
                        <input type="text" id="tin_no" name="tin_no" class="form-control m-input" maxlength="25"
                               size="25" autocomplete="off"
                               v-model="vm_tab3.tin_no"
                               onchange="vmTab3.vm_tab3.tin_no = $(this).val()"/>
                    </div>
                </div>
            </div>
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="tax_status" class="col-5 col-form-label text-right">Tax Status:</label>
                    <div class="col-7">
                        <select id="tax_status" class="form-control m-input select2" name="tax_status"
                                placeholder="Select an option"
                                data-validation="required" v-model="vm_tab3.tax_status"
                                oninput="vmTab3.vm_tab3.tax_status = $(this).val();">
                            <option value=""></option>
                            <option value="S">Single</option>
                            <option value="M">Married</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="S4">S4</option>
                            <option value="M1">M1</option>
                            <option value="M2">M2</option>
                            <option value="M3">M3</option>
                            <option value="M4">M4</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="phealth_no" class="col-5 col-form-label text-right">Philhealth #:</label>
                    <div class="col-7">
                        <input type="text" id="phealth_no" name="phealth_no" class="form-control m-input"
                               maxlength="25" size="25" autocomplete="off"
                               v-model="vm_tab3.phealth_no"
                               onchange="vmTab3.vm_tab3.phealth_no = $(this).val()"/>
                    </div>
                </div>
            </div>
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="pagibig_no" class="col-5 col-form-label text-right">Pag-ibig #:</label>
                    <div class="col-7">
                        <input type="text" id="pagibig_no" name="pagibig_no" class="form-control m-input" maxlength="25"
                               size="25" autocomplete="off"
                               v-model="vm_tab3.pagibig_no"
                               onchange="vmTab3.vm_tab3.pagibig_no = $(this).val()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="umid_no" class="col-5 col-form-label text-right">UMID #:</label>
                    <div class="col-7">
                        <span class="m-switch m-switch--sm">
                            <label>
                                <input
                                        type="checkbox"
                                        id="umid_no"
                                        data-identifier="umid_no-detail"/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="sss_no" class="col-5 col-form-label text-right">SSS/UMID #:</label>
                    <div class="col-7">
                        <input type="text" id="sss_no" name="sss_no" class="form-control m-input"
                               maxlength="25" size="25" autocomplete="off"
                               v-model="vm_tab3.sss_no"
                               onchange="vmTab3.vm_tab3.sss_no = $(this).val()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-10">
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="work_mode" class="col-5 col-form-label text-right">Work Mode:</label>
                    <div class="col-7">
                        <select class="form-control m-input select2" name="work_mode" placeholder="Select an option" data-validation="required"
                                v-model="vm_tab3.work_mode">
                            <option value="Time Based">Time Based</option>
                            <option value="Flexi Time">Flexi Time</option>
                            <option value="Activity Based">Activity Based</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="payroll_type" class="col-5 col-form-label text-right">Payroll Type:</label>
                    <div class="col-7">
                        <select class="form-control m-input select2" name="payroll_type" id="m--input-payroll_type_id" placeholder="Select an option" data-validation="required"
                                v-model="vm_tab3.payroll_type">
                        </select>
                    </div>
                </div>
            </div>
        </div>   
    </div>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions">
            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btnSave btn-accent m-btn m-btn--air m-btn--custom btn-submit">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php $this->load->view("payroll/employee_profile/modals/change_employee_company"); ?>