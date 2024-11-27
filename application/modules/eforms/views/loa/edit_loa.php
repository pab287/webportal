<style>
    @media screen and (max-width: 480px){
        #half_date div:last-child, #other_date div:last-child, #under_date div:last-child{
            margin-top: 10px;
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
                                <a type="button" href="view_loa?id=<?php echo $_GET['id']; ?>" title="Go to Masterfile"
                                   class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Edit Leave of Absence
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">

                    </div>
                </div>
                <form action="#" id="form_loa" class="form-horizontal">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Employee *
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <select id="select2_employee" name="employee" data-validation="required"
                                                onchange="emp_details()">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Company
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <textarea 
                                            name="company" 
                                            rows="5" cols="50" 
                                            class="form-control"
                                            readonly>
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Nature of Leave *
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <select class="form-control" 
                                            id="nature" name="nature" 
                                            data-validation="required">
                                            <option></option>
                                            <option value="Bereavement Leave">Bereavement Leave</option>
                                            <option value="Emergency Leave">Emergency Leave</option>
                                            <option value="Leave of Absence">Leave of Absence</option>
                                            <option value="Maternity Leave">Maternity Leave</option>
                                            <option value="Paternity Leave">Paternity Leave</option>
                                            <option value="Sick Leave">Sick Leave</option>
                                            <option value="Vacation Leave">Vacation Leave</option>
                                            <option value="Solo Parent Leave">Single / Solo Parent Leave</option>
                                            <option value="Vacation Leave">Special Leave</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Address on Leave *
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <textarea id="address_on_leave" 
                                            name="address" 
                                            rows="5" cols="50" 
                                            class="form-control"
                                            data-validation="required" 
                                            @input="getCurrentValue(event)">{{vm_tab1.address}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Reason for Leave *
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <textarea id="reason" 
                                            name="reason" 
                                            rows="5" cols="50"
                                            minlength="30" 
                                            maxlength="255"
                                            placeholder="Minimum of 30 character and a max of 255 character"
                                            class="form-control" 
                                            data-validation="required" 
                                            data-validation-length="min30"
                                            @input="getCurrentValue(event)">{{vm_tab1.reason}}</textarea>
                                            <div class="col-md-9" id="reason_v"></div>
                                    </div>
                                    
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Phone on Leave *
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <input id="phone_on_leave" 
                                        class="form-control m-input" 
                                        type="number" 
                                        name="phone" 
                                        autocomplete="off" 
                                        data-validation="required" 
                                        @input="getCurrentValue(event)"
                                        v-model="vm_tab1.phone" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">Type *</label>
                                    <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                        <select class="form-control" id="type" name="type" data-validation="required" onchange="type_change()">
                                            <option value="1">UNDERTIME</option>
                                            <option value="2">HALF DAY</option>
                                            <option value="3">WHOLE DAY</option>
                                            <option value="4">OTHER</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="under_date">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Date/Time Options *
                                    </label>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class='input-group date' id="under_from">
                                            <input class="form-control m-input" 
                                                type="text" name="under_from" 
                                                data-validation="required"
                                                v-model="vm_tab1.date_from"
                                                autocomplete="off" placeholder="FROM" />
                                            <span class="input-group-addon">
                                              <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>

                                        <span class="m-form__help m--regular-font-size-sm2 text-muted pull-right mt-2">
                                            NOTE: TIME IS IN 24 HOUR TIME FORMAT.
                                        </span>
                                    </div>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class='input-group timepicker'>
                                            <input type="text" name="under_to" 
                                            class="form-control" 
                                            id="under_to"
                                            autocomplete="off" 
                                            data-validation="required"
                                            v-model="vm_tab1.date_to" placeholder="TO" />
                                        </div>

                                        <span class="m-form__help m--regular-font-size-sm2 text-muted pull-right mt-2">
                                            NOTE: TIME IS IN 24 HOUR TIME FORMAT.
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="half_date">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Date/Time Options *
                                    </label>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class='input-group date' id="half_from">
                                            <input class="form-control m-input" 
                                                type="text" name="half_from"
                                                autocomplete="off" 
                                                data-validation="required" 
                                                placeholder="SELECT DATE" />
                                            <span class="input-group-addon">
                                              <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-12">
                                        <select class="form-control" name="half" onchange="type_change()" data-validation="required">
                                            <option value="1">AM</option>
                                            <option value="2">PM</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="whole">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Date/Time Options *
                                    </label>
                                    <div class="col-md-5 col-xs-12">
                                        <div class='input-group date' id="whole_date">
                                            <input class="form-control m-input" type="text" 
                                                autocomplete="off"
                                                name="whole_date" 
                                                data-validation="required"
                                                placeholder="SELECT DATE" />
                                            <span class="input-group-addon">
                                              <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="other_date">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Date/Time Options *
                                    </label>
                                    <div class="col-md-5 col-xs-12">
                                        <div class='input-group date' id="date_from">
                                            <input class="form-control m-input" 
                                                type="text" autocomplete="off"
                                                data-validation="required"
                                                name="date_from" placeholder="FROM" />
                                            <span class="input-group-addon">
                                              <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>

                                        <span class="m-form__help m--regular-font-size-sm2 text-muted pull-right mt-2">
                                            NOTE: TIME IS IN 24 HOUR TIME FORMAT.
                                        </span>
                                    </div>
                                    <div class="col-md-5 col-xs-12">
                                        <div class='input-group date' id="date_to">
                                            <input class="form-control m-input" 
                                                type="text" autocomplete="off"
                                                data-validation="required"
                                                name="date_to" placeholder="TO" />
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
                        <div class="modal-footer">
                            <button type="submit" id="btnSaveLoa"
                                    class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnUpdate">
                                <i class="la la-floppy-o"></i> Update
                            </button>
                            <a onclick="view_back()">
                                <button type="button"
                                        class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnCancel">
                                    <span>
                                        Cancel
                                    </span>
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>