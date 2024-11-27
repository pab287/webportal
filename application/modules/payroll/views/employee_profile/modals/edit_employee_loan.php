<div class="modal fade" role="dialog" id="edit-employee-loan" tabindex="-1" role="dialog">
    <form id="frm-edit-employee-loan" enctype="multipart/form-data">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Loan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <label for="allowance" class="form-control-label required">Loan</label>
                            <select name="loan_id" id="loan_id-edit" class="form-control"
                                    data-validation="required"></select>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group">
                            <label for="required" class="required">Amount</label>
                            <input type="text" name="amount" autocomplete="off"
                                   class="form-control text-right"
                                   data-validation="required">
                        </div>
                    </div>

                    <div id="hasReferenceLoansEdit">
                        <div class="row m--hide m-animate-fade-in-up" v-if="hasrefs === true">
                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 form-group">
                                <input type="hidden" name="reference" v-model="reference" />
                                <label for="required" class="">CA Reference #</label>
                                <select name="reference_id" id="ca_reference" class="form-control m--hide">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 row">
                        <div class="form-group col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <label class="form-control-label required">Deduction Type</label>
                            <div class="m-radio-inline mt-2">
                                <label class="m-radio mb-0">
                                    <input type="radio" name="deduction_type" value="1">
                                    Fix amount<span></span>
                                </label>
                                <label class="m-radio mb-0">
                                    <input type="radio" name="deduction_type" value="0" checked />
                                    Percentage<span></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group">
                            <label for="required" class="required" id="deduct_type_value_label">
                                Value
                            </label>
                            <input type="text" name="deduct_type_value" value="20" data-validation="required"
                                   autocomplete="off" class="form-control text-right">
                        </div>
                    </div>

                    <div id="has_interest_charge" class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm m--hide mb-0">
                        <div class="m-portlet__body">
                            <div class="row">
                                <div class="form-group col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                    <label class="form-control-label required">Payroll - Last Payment Charge</label>
                                    <div class="m-checkbox-inline mt-2">
                                        <label class="m-checkbox mb-0">
                                            <input type="radio" name="last_interest_charge" value="1" />
                                            Yes<span></span>
                                        </label>
                                        <label class="m-checkbox mb-0">
                                            <input type="radio" name="last_interest_charge" value="0" checked />
                                            No<span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                    <div class="alert alert-danger mb-0" role="alert">
                                        <strong>For posting</strong> of last pay CA interest charge.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 row">
                        <div class="form-group col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <label class="form-control-label required">Status</label>
                            <div class="m-radio-inline mt-2">
                                <label class="m-radio mb-0">
                                    <input type="radio" name="active" value="1">
                                    Active<span></span>
                                </label>
                                <label class="m-radio mb-0">
                                    <input type="radio" name="active" value="0" checked />
                                    Suspend<span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 row">
                        <div class="form-group m-form__group col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <label for="exampleInputEmail1">
                                Upload Attachments
                            </label>
                            <div></div>
                            <label class="custom-file">
                                <input type="file" id="file2" name="files[]" accept=".png, .PNG, .jpg, .JPG, .jpeg, .JPEG" multiple class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4 row">
                        <div class="col-lg-12">
                            <div class="gallery row photos"></div>
                        </div>
                    </div>

                    <div class="mt-4 row">
                        <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 form-group">
                            <label>DN Reference #</label>
                            <input id="debit_note" type="text" name="debit_note" autocomplete="off" class="form-control" maxlength="12" />
                        </div>
                        <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <label class="form-control-label">Remarks</label>
                            <textarea name="remarks" cols="20" rows="5" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 row">
                        <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <label style="font-weight: 600" for="">Recent Remarks </label>
                            <p id="for_remarks"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnUpdate">Save Changes</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>