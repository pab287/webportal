<form id="frmEditAdditionalData" class="m-form m-form--fit m-form--label-align-right" method="post"
      action="<?php echo site_url("hris/masterfile/update_employee_additional_info"); ?>">
    <input type="hidden" name="id" v-model="vm_tab2.id"/>
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="m-portlet__body">
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="tin_no" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">TIN #:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" id="tin_no" name="tin_no" class="form-control m-input" maxlength="25"
                               size="25" autocomplete="off"
                               v-model="vm_tab2.tin_no"
                               onchange="vmTab2.vm_tab2.tin_no = $(this).val()"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="tax_status" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Tax Status:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <select id="tax_status" class="form-control m-input select2" name="tax_status"
                                placeholder="Select an option"
                                data-validation="required" v-model="vm_tab2.tax_status"
                                oninput="vmTab2.vm_tab2.tax_status = $(this).val();">
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
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="phealth_no" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Philhealth #:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" id="phealth_no" name="phealth_no" class="form-control m-input"
                               maxlength="25" size="25" autocomplete="off"
                               v-model="vm_tab2.phealth_no"
                               onchange="vmTab2.vm_tab2.phealth_no = $(this).val()"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="pagibig_no" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Pag-ibig #:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" id="pagibig_no" name="pagibig_no" class="form-control m-input" maxlength="25"
                               size="25" autocomplete="off"
                               v-model="vm_tab2.pagibig_no"
                               onchange="vmTab2.vm_tab2.pagibig_no = $(this).val()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="umid_no" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">UMID #:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
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
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="sss_no" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">SSS/UMID #:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" id="sss_no" name="sss_no" class="form-control m-input"
                               maxlength="25" size="25" autocomplete="off"
                               v-model="vm_tab2.sss_no"
                               onchange="vmTab2.vm_tab2.sss_no = $(this).val()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-25">
            <div class="col-10 ml-auto"><h3 class="m-form__header m-form__section">Father's Details</h3></div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="fat_name" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Full name:</label>
                    <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                        <input type="text" name="fat_name" class="form-control m-input" maxlength="100" size="100"
                               autocomplete="off" v-model="vm_tab2.fat_name"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="fat_contact" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Contact No:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" name="fat_contact" class="form-control m-input" maxlength="25" size="25"
                               autocomplete="off" v-model="vm_tab2.fat_contact"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
                <div class="form-group m-form__group row">
                    <label for="fat_addr" class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-form-label">Current Address:</label>
                    <div class="col-sm-9 col-md-9 col-lg-9 col-xl-9">
                        <input type="text" name="fat_addr" class="form-control m-input" maxlength="200" size="200"
                               autocomplete="off" v-model="vm_tab2.fat_addr"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="fat_company" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Company:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" name="fat_company" class="form-control m-input" maxlength="200" size="200"
                               autocomplete="off" v-model="vm_tab2.fat_company"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="fat_occupation" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Occupation:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" name="fat_occupation" class="form-control m-input" maxlength="100" size="100"
                               autocomplete="off" v-model="vm_tab2.fat_occupation"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="fat_deceased" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Deceased:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <span class="m-switch m-switch--sm">
                            <label>
                                <input
                                        type="checkbox"
                                        id="deceased_father"
                                        data-identifier="fathers_detail"
                                        v-model="vm_tab2.fat_deceased"
                                        true-value="1"
                                        false-value="0"
                                        />
                                        
                                <span></span>
                            </label>
                        </span>
                        <input type="hidden" v-model="vm_tab2.fat_deceased" name="fat_deceased">
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-25">
            <div class="col-10 ml-auto"><h3 class="m-form__header m-form__section">Mother's Details</h3></div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="mot_name" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Full name:</label>
                    <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                        <input type="text" name="mot_name" class="form-control m-input" maxlength="100" size="100"
                               autocomplete="off"
                               v-model="vm_tab2.mot_name"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="mot_contact" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Contact No:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" name="mot_contact" class="form-control m-input" maxlength="25" size="25"
                               autocomplete="off"
                               v-model="vm_tab2.mot_contact"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
                <div class="form-group m-form__group row">
                    <label for="mot_addr" class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-form-label">Current Address:</label>
                    <div class="col-sm-9 col-md-9 col-lg-9 col-xl-9">
                        <input type="text" name="mot_addr" class="form-control m-input" maxlength="200" size="200"
                               autocomplete="off"
                               v-model="vm_tab2.mot_addr"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="mot_company" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Company:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" name="mot_company" class="form-control m-input" maxlength="200" size="200"
                               autocomplete="off"
                               v-model="vm_tab2.mot_company"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="mot_occupation" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Occupation:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" name="mot_occupation" class="form-control m-input" maxlength="100" size="100"
                               autocomplete="off"
                               v-model="vm_tab2.mot_occupation"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="mot_deceased" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Deceased:</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <span class="m-switch m-switch--sm">
                            <label>
                                <input
                                        type="checkbox"
                                        id="deceased_mother"
                                        data-identifier="mothers_detail"
                                        v-model="vm_tab2.mot_deceased"
                                        v-bind:value="vm_tab2.mot_deceased"
                                        true-value="1"
                                        false-value="0"/>
                                <span></span>
                            </label>
                        </span>
                        <input type="hidden" v-model="vm_tab2.mot_deceased" name="mot_deceased">
                    </div>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-25">
            <div class="col-10 ml-auto"><h3 class="m-form__header m-form__section">Partner's Details</h3></div>
        </div>
        <div class="row m--margin-bottom-20">
            <div class="col-10 col-md-10">
                <div class="form-group m-form__group row">
                    <div class="col-2">&nbsp;</div>
                    <div class="col-sm-10 col-lg-10 col-md-10 col-lg-10">
                        <div class="m-checkbox-inline">
                            <label class="m-checkbox">
                                <input id="pt_married" type="radio" name="partner_type"
                                       class="m-input--partners_detail m--partner_switch"
                                       data-identifier="has--partners_detail" value="1" v-model="vm_tab2.partner_type"/>
                                Married<span></span>
                            </label>
                            <label class="m-checkbox">
                                <input id="pt_partner" type="radio" name="partner_type"
                                       class="m-input--partners_detail m--partner_switch"
                                       data-identifier="has--partners_detail" value="2" v-model="vm_tab2.partner_type"/>
                                Partner<span></span>
                            </label>
                            <label class="m-checkbox">
                                <input id="pt_single" type="radio" name="partner_type"
                                       class="m-input--partners_detail m--partner_switch"
                                       data-identifier="has--partners_detail" value="0" v-model="vm_tab2.partner_type"/>
                                Single<span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="vm_tab2.partner_type == 1">
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="name_partner" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Full name:</label>
                        <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                            <input type="text" id="name_partner" name="spo_name"
                                class="form-control m-input has--partners_detail" maxlength="100"
                                size="100" autocomplete="off" v-model="vm_tab2.spo_name"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="contact_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Contact No:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="contact_partner" name="spo_contact"
                                class="form-control m-input has--partners_detail" maxlength="25"
                                size="25" autocomplete="off" v-model="vm_tab2.spo_contact"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
                    <div class="form-group m-form__group row">
                        <label for="address_partner" class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-form-label">Current Address:</label>
                        <div class="col-sm-9 col-md-9 col-lg-9 col-xl-9">
                            <input type="text" id="address_partner" name="spo_addr"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="200" size="200" autocomplete="off" v-model="vm_tab2.spo_addr"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="company_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Company:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="company_partner" name="spo_company"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="200" size="200" autocomplete="off" v-model="vm_tab2.spo_company"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="occupation_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Occupation:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="occupation_partner" name="spo_occupation"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="100" size="100" autocomplete="off" v-model="vm_tab2.spo_occupation"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="deceased_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Deceased:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <span class="m-switch m-switch--sm">
                                <label>
                                    <input
                                            type="checkbox"
                                            id="deceased_partner"
                                            class="m-input has--partners_detail"
                                            data-identifier="partners_detail"
                                            v-model="vm_tab2.spo_deceased"
                                            v-bind:value="vm_tab2.spo_deceased"
                                            true-value="1"
                                            false-value="0"/>
                                    <span></span>
                                </label>
                            </span>
                            <input type="hidden" v-model="vm_tab2.spo_deceased" name="spo_deceased">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="vm_tab2.partner_type == 2">
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="name_partner" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Full name:</label>
                        <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                            <input type="text" id="name_partner" name="partners_name"
                                class="form-control m-input has--partners_detail" maxlength="100"
                                size="100" autocomplete="off" v-model="vm_tab2.partners_name"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="contact_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Contact No:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="contact_partner" name="partners_contact"
                                class="form-control m-input has--partners_detail" maxlength="25"
                                size="25" autocomplete="off" v-model="vm_tab2.partners_contact"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
                    <div class="form-group m-form__group row">
                        <label for="address_partner" class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-form-label">Current Address:</label>
                        <div class="col-sm-9 col-md-9 col-lg-9 col-xl-9">
                            <input type="text" id="address_partner" name="partners_addr"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="200" size="200" autocomplete="off" v-model="vm_tab2.partners_addr"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="company_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Company:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="company_partner" name="partners_company"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="200" size="200" autocomplete="off" v-model="vm_tab2.partners_company"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="occupation_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Occupation:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="occupation_partner" name="partners_occupation"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="100" size="100" autocomplete="off" v-model="vm_tab2.partners_occupation"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="deceased_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Deceased:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <span class="m-switch m-switch--sm">
                                <label>
                                    <input
                                            type="checkbox"
                                            id="deceased_partner"
                                            class="m-input has--partners_detail"
                                            data-identifier="partners_detail"
                                            v-model="vm_tab2.partners_deceased"
                                            v-bind:value="vm_tab2.partners_deceased"
                                            true-value="1"
                                            false-value="0"/>
                                    <span></span>
                                </label>
                            </span>
                            <input type="hidden" v-model="vm_tab2.partners_deceased" name="partners_deceased">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="name_partner" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Full name:</label>
                        <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                            <input type="text" id="name_partner" name="partners_name"
                                class="form-control m-input has--partners_detail" maxlength="100"
                                size="100" autocomplete="off" v-model="vm_tab2.partners_name"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="contact_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Contact No:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="contact_partner" name="partners_contact"
                                class="form-control m-input has--partners_detail" maxlength="25"
                                size="25" autocomplete="off" v-model="vm_tab2.partners_contact"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
                    <div class="form-group m-form__group row">
                        <label for="address_partner" class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-form-label">Current Address:</label>
                        <div class="col-sm-9 col-md-9 col-lg-9 col-xl-9">
                            <input type="text" id="address_partner" name="partners_addr"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="200" size="200" autocomplete="off" v-model="vm_tab2.partners_addr"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m--margin-bottom-10">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="company_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Company:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="company_partner" name="partners_company"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="200" size="200" autocomplete="off" v-model="vm_tab2.partners_company"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="occupation_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Occupation:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <input type="text" id="occupation_partner" name="partners_occupation"
                                class="form-control m-input m-input has--partners_detail"
                                maxlength="100" size="100" autocomplete="off" v-model="vm_tab2.partners_occupation"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="deceased_partner" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Deceased:</label>
                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                            <span class="m-switch m-switch--sm">
                                <label>
                                    <input
                                            type="checkbox"
                                            id="deceased_partner"
                                            class="m-input has--partners_detail"
                                            name="partners_deceased"
                                            data-identifier="partners_detail"
                                            v-model="vm_tab2.partners_deceased"
                                            v-bind:value="vm_tab2.partners_deceased"
                                            true-value="1"
                                            false-value="0"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row m--margin-bottom-25">
            <div class="col-10 ml-auto"><h3 class="m-form__header m-form__section">Emergency Details</h3></div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="emer_name" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Contact Person*</label>
                    <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                        <input type="text" id="emer_name" name="emer_name" class="form-control m-input" maxlength="100"
                               size="100" autocomplete="off"
                               v-model="vm_tab2.emer_name" data-validation="required"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="emer_contact" class="col-sm-6 col-md-6 col-lg-4 col-xl-5 col-form-label">Contact No*</label>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-7">
                        <input type="text" id="emer_contact" name="emer_contact" class="form-control m-input"
                               maxlength="25" size="25"
                               autocomplete="off" v-model="vm_tab2.emer_contact" data-validation="required"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
                <div class="form-group m-form__group row">
                    <label for="emer_addr" class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-form-label">Address*</label>
                    <div class="col-sm-9 col-md-9 col-lg-9 col-xl-9">
                        <input type="text" id="emer_addr" name="emer_addr" class="form-control m-input" maxlength="200"
                               size="200" autocomplete="off"
                               v-model="vm_tab2.emer_addr" data-validation="required"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row mb-5">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="emer_name" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Added By</label>
                    <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                        <input type="text" class="form-control m-input" maxlength="100"
                               size="100" autocomplete="off"
                               :value="vm_tab2.added_by" readonly disabled/>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                <div class="form-group m-form__group row">
                    <label for="emer_name" class="col-sm-6 col-md-4 col-lg-4 col-xl-5 col-form-label">Added Date</label>
                    <div class="col-sm-6 col-md-8 col-lg-8 col-xl-7">
                        <input type="text" class="form-control m-input" maxlength="100"
                               size="100" autocomplete="off"
                               :value="dateFormat(vm_tab2.add_date)" readonly disabled/>
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
                    <button type="submit" class="btn btnSave btn-primary m-btn m-btn--air m-btn--custom btn-submit">
                    <i class="la la-check mr-2"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-0x"></div>
</form>
<input type="hidden" id="change_additional_info">