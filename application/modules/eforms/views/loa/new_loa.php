<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
              <span class="m-portlet__head-icon">
                  <a type="button" href="masterfile" title="Go to Masterfile"
                     class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                      <i class="la la-arrow-left"></i>
                  </a>
              </span>
                <h3 class="m-portlet__head-text">
                    New Leave of Absence
                </h3>
                </div>
                    </div>
                    <div class="m-portlet__head-tools">

                    </div>
                </div>
                <div class="m-portlet__body">
                    <form action="#" id="form_loa" class="form-horizontal">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-xs-12 col-form-label required">
                                        Employee
                                    </label>
                                    <div class="col-md-10 col-xs-12">
                                        <select id="select2_employee" name="employee" data-validation="required"
                                                onchange="emp_details()">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-xs-12 col-form-label">
                                        Company
                                    </label>
                                    <div class="col-md-10 col-xs-12">
                                        <textarea name="company" rows="4" cols="50" class="form-control"
                                                  readonly> </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-xs-12 col-form-label required">
                                        Nature of Leave
                                    </label>
                                    <div class="col-md-9 col-xs-12">
                                        <select class="form-control select2" id="nature" name="nature"
                                                data-validation="required">
                                            <option></option>
                                            <option value="Bereavement Leave">Bereavement Leave</option>
                                            <option value="Emergency Leave">Emergency Leave</option>
                                            <option value="Leave of Absence">Leave of Absence</option>
                                            <option value="Maternity Leave">Maternity Leave</option>
                                            <option value="Paternity Leave">Paternity Leave</option>
                                            <option value="Sick Leave">Sick Leave</option>
                                            <option value="Solo Parent Leave">Single / Solo Parent Leave</option>
                                            <option value="Vacation Leave">Special Leave</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-xs-12 col-form-label required">
                                        Address on Leave
                                    </label>
                                    <div class="col-md-9 col-xs-12">
                                        <textarea name="address" rows="5" cols="50" class="form-control" maxlength="100"
                                                  data-validation="required"></textarea>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-xs-12 col-form-label required">
                                        Reason for Leave
                                    </label>
                                    <div class="col-md-9 col-xs-12">
                                        <textarea name="reason" id="reason"
                                            placeholder="Minimum of 30 character and a max of 255 character"
                                            minlength="30" 
                                            class="form-control" 
                                            rows="5" 
                                            maxlength="255"
                                            data-validation="required" 
                                            data-validation-length="min30"></textarea>
                                            <div class="col-md-6" id="reason_v"></div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-xs-12 col-form-label required">
                                        Phone on Leave
                                    </label>
                                    <div class="col-md-9 col-xs-12">
                                        <input class="form-control m-input" id="phone" name="phone" type="text" autocomplete="off" data-validation="required" onkeypress="isNumberKey(event)" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-xs-12 col-form-label required">
                                        Type
                                    </label>
                                    <div class="col-md-10 col-xs-12">
                                        <select class="form-control select2" id="type" name="type" 
                                            data-validation="required"
                                            onchange="type_change()">
                                            <option value=""></option>
                                            <option value="1">UNDERTIME</option>
                                            <option value="2">HALF DAY</option>
                                            <option value="3">WHOLE DAY</option>
                                            <option value="4">OTHER</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="under_date">
                                    <label class="col-md-2 col-xs-12 col-form-label required">
                                        Date/Time Options
                                    </label>
                                    <div class="col-md-5 col-xs-12">
                                        <div class='input-group date' id="under_from">
                                            <input class="form-control m-input" 
                                                type="text" 
                                                name="under_from"
                                                autocomplete="off" 
                                                placeholder="FROM" 
                                                data-validation="required" />
                                            <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>

                                        <span class="m-form__help m--regular-font-size-sm2 text-muted pull-right mt-2">
                                            NOTE: TIME IS IN 24 HOUR TIME FORMAT.
                                        </span>
                                    </div>
                                    <div class="col-md-5 col-xs-12">
                                        <div class='input-group timepicker'>
                                            <input type="text" 
                                                name="under_to" 
                                                class="form-control" 
                                                id="under_to" 
                                                data-validation="required"
                                                autocomplete="off" 
                                                placeholder="TO" />
                                        </div>
                                        <span class="m-form__help m--regular-font-size-sm2 text-muted pull-right mt-2">
                                            NOTE: TIME IS IN 24 HOUR TIME FORMAT.
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="half_date">
                                    <label class="col-2 col-form-label required">
                                        Date/Time Options
                                    </label>
                                    <div class="col-md-5">
                                        <div class='input-group date' id="half_from">
                                            <input class="form-control m-input" 
                                                type="text" 
                                                name="half_from" 
                                                data-validation="required" 
                                                autocomplete="off" 
                                                placeholder="SELECT DATE" />
                                            <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control" name="half" onchange="type_change()" data-validation="required">
                                            <option value="1">AM</option>
                                            <option value="2">PM</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="whole">
                                    <label class="col-2 col-form-label required">
                                        Date/Time Options
                                    </label>
                                    <div class="col-md-5">
                                        <div class='input-group date' id="whole_date">
                                            <input class="form-control m-input" 
                                                type="text" 
                                                name="whole_date" 
                                                data-validation="required"
                                                autocomplete="off" 
                                                placeholder="SELECT DATE" />
                                            <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="other_date">
                                    <label class="col-2 col-form-label required">
                                        Date/Time Options
                                    </label>
                                    <div class="col-md-5">
                                        <div class='input-group date' id="date_from">
                                            <input class="form-control m-input" 
                                                type="text" name="date_from" 
                                                data-validation="required"
                                                autocomplete="off" 
                                                placeholder="FROM" />
                                            <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>

                                        <span class="m-form__help m--regular-font-size-sm2 text-muted pull-right mt-2">
                                            NOTE: TIME IS IN 24 HOUR TIME FORMAT.
                                        </span>
                                    </div>
                                    <div class="col-md-5">
                                        <div class='input-group date' id="date_to">
                                            <input class="form-control m-input" 
                                                type="text" 
                                                name="date_to" 
                                                data-validation="required"
                                                autocomplete="off" 
                                                placeholder="TO" />
                                            <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>

                                        <span class="m-form__help m--regular-font-size-sm2 text-muted pull-right mt-2">
                                            NOTE: TIME IS IN 24 HOUR TIME FORMAT.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="modal-footer">
                            <button type="submit" id="btnSaveLoa"
                                    class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnSave">
                                <i class="la la-floppy-o"></i> Save
                            </button>
                            <a href="<?php echo site_url("eforms/loa/masterfile"); ?>">
                                <button type="button"
                                        class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnCancel">
                                <span>
                                    Cancel
                                </span>
                                </button>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>