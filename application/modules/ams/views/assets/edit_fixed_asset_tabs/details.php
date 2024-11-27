<form id="frm_edit_fixed_asset" action="<?php echo base_url("ams/assets/process_edit"); ?>">
    <input type="hidden" name="id" value="<?php echo $this->uri->segment('4');?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Name
                </label>
                <div class="col-10">
                    <input class="form-control m-input" type="text" name="name" v-model="vm_edit_frm.name" data-validation="required" />
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Description
                </label>
                <div class="col-10">
                    <textarea class="form-control m-input m-input--air" rows="4" name="assetname" v-model="vm_edit_frm.assetname" data-validation="required" ></textarea>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Remarks
                </label>
                <div class="col-10">
                    <textarea class="form-control m-input m-input--air" rows="3" v-model="vm_edit_frm.remarks" name="remarks"></textarea>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Company
                </label>
                <div class="col-10">
                    <select id="select2_company" name="company_id" data-validation="required">

                    </select>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Department
                </label>
                <div class="col-10">
                    <select id="select2_department" name="department_id" data-validation="required">
                        
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
                    Type
                </label>
                <div class="col-10">
                    <select id="select2_type" name="sub_cat_id" data-validation="required">
                        
                    </select>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Asset Code
                </label>
                <div class="col-10">
                    <input class="form-control m-input" type="text" name="assetacode" readonly="readonly" v-model="vm_edit_frm.assetacode"/>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Stock Code
                </label>
                <div class="col-10">
                    <input class="form-control m-input" type="text" name="gl_code" v-model="vm_edit_frm.gl_code" />
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Station
                </label>
                <div class="col-10">
                    <select id="select2_station" name="location_id" data-validation="required" data-validation="required">
                    
                    </select>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Location
                </label>
                <div class="col-10">
                    <select id="select2_area" name="area" data-validation="required">
                    
                    </select>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-2 col-form-label">
                    Status
                </label>
                <div class="col-10">
                    <select id="select2_status" name="status" data-validation="required" data-validation="required">
                    
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row" >
                <div class="custom-image_container col-12">
                    <div id="img_primary" style="margin: 0 auto; text-align: center;">
                    <img id='img_prim' class='img-responsive' style="max-width: 486px; margin: 0 auto;" src='<?php echo base_url("assets/images/ams/images/no_image.jpg"); ?>' /><br/>
                    <button type="button" style="margin: 0 auto; width: auto;" class="btn btn-sm btn-success btnChange btnNew" data-toggle="modal" data-target="#modalUploadImage"><i class="fa fa-camera"></i> Change</button>
                    </div>
                    <div id="alt_images"></div> 
                </div>
            </div>
        </div>
    </div>
    <div class="m-separator m-separator--space m-separator--dashed"></div>
    <div class="row">
        <div class="col-4">
            <div class="form-group m-form__group row">
                <label class="col-4 col-form-label">
                    Updated By
                </label>
                <div class="col-8">
                    <input class="form-control m-input" type="text" v-model="vm_edit_frm.updatedBy" />
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-4 col-form-label">
                    Updated Date
                </label>
                <div class="col-8">
                    <div class="input-group date" >
                        <input class="form-control m-input" type="text" v-model="vm_edit_frm.dateUpdated" />
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group m-form__group row">
                <label class="col-4 col-form-label">
                    Created By
                </label>
                <div class="col-8">
                    <input class="form-control m-input" type="text" v-model="vm_edit_frm.createdBy" />
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-4 col-form-label">
                    Created Date
                </label>
                <div class="col-8">
                    <div class="input-group date" >
                        <input class="form-control m-input" type="text"v-model="vm_edit_frm.dateCreated" />
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group m-form__group row">
                <label class="col-4 col-form-label">
                    Inventory By
                </label>
                <div class="col-8">
                    <input class="form-control m-input" type="text" v-model="vm_edit_frm.inventory_check_by" />
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-4 col-form-label">
                Inventory Date
                </label>
                <div class="col-8">
                    <div class="input-group date">
                        <input class="form-control m-input" type="text" v-model="vm_edit_frm.inventory_check_date" />
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                    <input class="form-control m-input" type="text" name="supplier" data-validation="required" v-model="vm_edit_frm.supplier" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    CHECK NO
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input" type="text" name="check_no" v-model="vm_edit_frm.check_no" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    P.O. No.
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input" type="text" name="po_no" v-model="vm_edit_frm.po_no" />
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group m-form__group row">  
                                <label class="col-2 col-form-label">
                                    Serial No.
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input" type="text" name="serialno" data-validation="required" v-model="vm_edit_frm.serialno"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    Brand
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input" type="text" name="brand" data-validation="required" v-model="vm_edit_frm.brand" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    Model name/No.
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input" type="text" name="modelno" data-validation="required" v-model="vm_edit_frm.modelno" />
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
                                    <input class="form-control m-input" type="text" name="date_purchased" v-model="vm_edit_frm.datepurchased" />
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
                                    <div class="input-group date" id="date_received_picker" >
                                        <input class="form-control m-input" type="text" name="date_received" v-model="vm_edit_frm.date_received" />
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
                                    RECEIVING REPORT
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input" type="text" name="rrno" v-model="vm_edit_frm.rr_no" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-md-4">
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    Purchased Price
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input text-right" type="text" id="purchaseprice" name="purchaseprice" v-model="vm_edit_frm.purchaseprice" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    Additional Cost
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input text-right" type="text" id="beg_addcost"  name="beg_addcost" v-model="vm_edit_frm.beg_addcost" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    Grand Total
                                </label>
                                <div class="col-10">
                                    <input class="form-control m-input text-right" type="text" name="total_cost" v-model="vm_edit_frm.total_cost" />
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="m-separator m-separator--space m-separator--dashed"></div>

                <div class="m-form__actions m-form__actions text-right">
                    <div class="row">
                        <div class="col-lg-12 ml-lg-auto">
                            <button type="button" class="btn btn-secondary btnNew">
                                Cancel
                            </button> 
                            <button type="submit" class="btn btn-brand">
                                <i class="la la-floppy-o"></i> 
                                Update
                            </button> 
                            <button type="button" class="btn btn-success">
                                Small split button
                            </button>
                        </div>
                    </div>
                </div>

            <!--end::Portlet-->
        </div>
    </div>
</form>