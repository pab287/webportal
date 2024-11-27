<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <form id="frm_newVehicle" action="<?php echo base_url("ams/vehicles/process_new"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" value="" name="id"/>
                <input type="hidden" id="total_cost2" name="total_cost2">
                <input type="hidden" id="purchaseprice2" name="purchaseprice2">
                <input type="hidden" id="beg_addcost" name="beg_addcost">
                <input type="hidden" id="beg_addcost2" name="beg_addcost2">
                <input type="hidden" id="salvage_value2" name="salvage_value2">
                <input type="hidden" id="isO" name="isO">
                <input type="hidden" name="gl_code"/>

                <div class="m-portlet m-portlet--mobile">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    New Vehicle
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">

                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Name
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="name" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Company
                                    </label>
                                    <div class="col-10">
                                        <select id="select2_company" name="company_code" data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Category
                                    </label>
                                    <div class="col-10">
                                        <select id="select2_category" name="category_id" data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Sub Category
                                    </label>
                                    <div class="col-10">
                                        <select id="sub_cat_id" name="sub_cat_id" data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Type
                                    </label>
                                    <div class="col-10">
                                        <select id="category" name="category" data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Stock Code
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="assetacode" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Description
                                    </label>
                                    <div class="col-10">
                                        <textarea class="form-control m-input m-input--air" rows="3" name="assetname"
                                                  data-validation="required"></textarea>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Remarks
                                    </label>
                                    <div class="col-10">
                                        <textarea class="form-control m-input m-input--air" rows="3" name="remarks"></textarea>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Status
                                    </label>
                                    <div class="col-10">
                                        <select id="select2_status" name="status" data-validation="required">
                                            <option disabled="" selected="">&nbsp;</option>
                                            <option value="brandnew">Brand New</option>
                                            <option value="surplus">Surplus</option>
                                            <option value="junk">Junk</option>
                                            <option value="lost">Lost</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Manufacturer
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="brand" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Chassis No.
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="chasisno" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Year Model
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="year" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Model No.
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="model" data-validation="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="custom-image_container col-12">
                                        <div id="img_primary" style="margin: 0 auto; text-align: center;">
                                            <img id='img_prim' class='img-responsive' style="max-width: 486px; margin: 0 auto;"
                                                 src='<?php echo base_url("assets/images/ams/images/no_image.jpg"); ?>'/><br/>
                                            <button type="button" style="margin: 0 auto; width: auto;" class="btn btn-sm btn-success btnChange btnNew"
                                                    data-toggle="modal" data-target="#modalUploadImage"><i class="fa fa-camera"></i> Change
                                            </button>
                                        </div>
                                        <div id="alt_images"></div>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Engine No.
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="engineno" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Body No.
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="bodyno" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Tire Size
                                    </label>
                                    <div class="col-10">
                                        <input class="form-control m-input" type="text" name="tire_size" data-validation="required"/>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="m-separator m-separator--space m-separator--dashed"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <!--start::Portlet-->
                                <div class="m-portlet m-portlet--mobile">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <h3 class="m-portlet__head-text">
                                                    Other information
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-portlet__body">
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Supplier
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" name="supplier" data-validation="required"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        CHECK NO
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" name="check_no"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        P.O. No.
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" name="po_no"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Color
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" name="color"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Location
                                                    </label>
                                                    <div class="col-10">
                                                        <select id="select2_station" name="area" data-validation="required">

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-4 col-form-label">
                                                        Operational
                                                    </label>
                                                    <div class="col-8">
                                                        <span class="m-switch m-switch--icon">
                                                          <label>
                                                            <input type="checkbox" name="isOperation">
                                                            <span></span>
                                                          </label>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Date Purchased
                                                    </label>
                                                    <div class="col-10">
                                                        <div class="input-group date" id="date_purchased_picker">
                                                            <input class="form-control m-input" type="text" name="date_purchased"/>
                                                            <span class="input-group-addon">
                                                                <i class="la la-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Date Received
                                                    </label>
                                                    <div class="col-10">
                                                        <div class="input-group date" id="date_received_picker">
                                                            <input class="form-control m-input" type="text" name="date_received"/>
                                                            <span class="input-group-addon">
                                                                <i class="la la-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Pruchased Price
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" id="purchaseprice" name="purchaseprice"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Plate No.
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" name="plateno" data-validation="required"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Operator
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" name="driver"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group m-form__group row">
                                                    <label class="col-2 col-form-label">
                                                        Grand Total
                                                    </label>
                                                    <div class="col-10">
                                                        <input class="form-control m-input" type="text" name="total_cost"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Portlet-->
                            </div>
                        </div>
                        <div class="m-portlet__foot m-portlet__foot">
                            <div class="m-form__actions m-form__actions text-right">
                                <div class="row">
                                    <div class="col-lg-12 ml-lg-auto">
                                        <button type="button" class="btn btn-secondary btnNew">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-brand">
                                            <i class="la la-floppy-o"></i>
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>
            <!--end::Portlet-->
        </div>
    </div>
</div>

<?php $this->load->view("assets/modals/upload_content", array("is_asset" => true)); ?>