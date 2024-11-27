<div class="modal fade" id="new-asset-component-dialog">
    <div class="modal-dialog modal-lg" role="dialog"
         style="">
        <div class="modal-content">
            <form action="<?= base_url("ams/assets/save_new_asset/1") ?>" id="frm-new-asset-component-dialog">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="isGen" value="1">

                <div class="modal-header">
                    <h5 class="modal-title">New Asset Component</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Brand
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control m-input input-auto-height lock-asset_name"
                                        name="brand" autocomplete="off"
                                        data-validation="required" id="asset_brand" onkeyup="generate_asset_name()">
                                        <footer class="blockquote-footer">(Put N/A or NONE to enter name manually)</footer>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Model Name/No.
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height lock-asset_name"
                                        name="modelno" autocomplete="off"
                                        data-validation="required" id="asset_model" onkeyup="generate_asset_name()">
                                        <footer class="blockquote-footer">(Put N/A or NONE to enter name manually)</footer>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="asset_name" class="form-control m-input input-auto-height"
                                       name="name" data-validation="required" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Company
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" data-validation="required"
                                        id="dlg-select2-company" name="company_code"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Department
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" data-validation="required"
                                        id="dlg-select2-asset-department" name="department_code"></select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-5 col-lg-5 col-md-5 col-xs-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Category
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" id="dlg-select2-asset-category"
                                        data-validation="required" name="asset_category"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-xs-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Type
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" id="dlg-select2-sub-category"
                                        data-validation="required" name="sub_cat_code"></select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Stock Code
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="gl_code" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <div class="form-group m-form__group">
                                <label>
                                    Description
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control m-input" rows="3"
                                          name="assetname" data-validation="required" autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                Default Location
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input"
                                        id="dlg-select2-station"
                                        name="location"
                                        data-validation="required"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Location
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input"
                                        id="dlg-select2-location"
                                        name="area_id"
                                        data-validation="required"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Status</label>
                                <select class="form-control m-input" id="dlg-select2-status" name="status">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Remarks</label>
                                <textarea class="form-control m-input" rows="3"
                                          name="remarks2" autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- OTHER INFORMATION  -->

                    <div class="mb-5" style="margin-top: 64px;">
                        <p style="font-size: 16px; font-weight: 500;">
                            <span class="mr-2"><i class="flaticon-information"></i></span>
                            <span>OTHER INFORMATION</span>
                        </p>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Supplier
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="supplier" autocomplete="off"
                                       data-validation="required">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>PO No.</label>
                                <input type="text" class="form-control m-input input-auto-height" name="po_no" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Check No.</label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="check_no" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Serial No.
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="serialno" autocomplete="off"
                                       data-validation="required">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Brand
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="brand" autocomplete="off"
                                       data-validation="required">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Model Name/No.
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="modelno" autocomplete="off"
                                       data-validation="required">
                            </div>
                        </div> -->

                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12" id="purchased_date">
                            <div class="form-group m-form__group">
                                <label>Date Purchased</label>
                                <div class="input-group date dt-picker">
                                    <input type="text" class="form-control m-input input-auto-height"
                                           name="datepurchased" autocomplete="off" id="modal_purchased_date">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar"></i>
                                    </span>
                                </div>
                                <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12" id="recovered_date">
                            <div class="form-group m-form__group">
                                <label>Date Recovered</label>
                                <div class="input-group date dt-picker">
                                    <input type="text" class="form-control m-input input-auto-height"
                                           name="recovered_date" autocomplete="off" id="modal_recovered_date">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar"></i>
                                    </span>
                                </div>
                                <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Date Received</label>
                                <div class="input-group date dt-picker">
                                    <input type="text" class="form-control m-input input-auto-height"
                                           name="date_received" autocomplete="off">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar"></i>
                                    </span>
                                </div>
                                <div class="m-form__help pt-2">Format: mm/dd/yyyy</div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Warranty Date</label>
                                <div class="input-group date dt-picker">
                                    <input type="text" class="form-control m-input input-auto-height"
                                           name="warranty_date" autocomplete="off">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar"></i>
                                    </span>
                                </div>
                                <div class="m-form__help pt-2">Format: mm/dd/yyyy</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Receiving Report </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="rr_no" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Add'l Cost</label>
                                <input type="text" class="form-control m-input input-auto-height text-right money-mask"
                                       style="font-weight: bold;"
                                       name="beg_addcost" autocomplete="off" onkeyup="setTotalCost()">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Purchase Price
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control m-input input-auto-height text-right money-mask"
                                           name="purchaseprice" autocomplete="off" data-validation="required"
                                           style="font-weight: bold;">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Grand Total
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control m-input input-auto-height text-right"
                                           style="font-weight: bold;"
                                           name="total_cost" autocomplete="off" data-validation="required" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnNew">
                        Continue
                    </button>
                    <button type="button" class="btn btn-danger text-light btnNew" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#modal_purchased_date").change(function(e){
        var date_purchased = $("#modal_purchased_date").val();
        if(date_purchased == ""){
            $("#recovered_date").attr('hidden', false);
        }else{
            $("#recovered_date").attr('hidden', true);
        }

    });

    $("#modal_recovered_date").change(function(e){
        var date_recovered = $("#modal_recovered_date").val();
        if(date_recovered == ""){
            $("#purchased_date").attr('hidden', false);
        }else{
            $("#purchased_date").attr('hidden', true);
        }

    });

function generate_asset_name(){
    var brand_name = $("#asset_brand").val().toLowerCase();
    var model_name = $("#asset_model").val().toLowerCase();
    var name = "";
    
    if((brand_name == "n/a" || brand_name == "none") && (model_name == "n/a" || model_name == "none")){
        name = "";
        $("#asset_name").attr('readonly', false);
    }else if((model_name == "n/a" || model_name == "none") && (brand_name != "n/a" || brand_name != "none")){
        name = brand_name;
    }else if((brand_name == "n/a" || brand_name == "none") && (model_name != "n/a" || model_name != "none")){
        name = model_name;
    }else{
        name = brand_name.concat(" ", model_name);
    }
    $("#asset_name").val(name);
    
}
</script>