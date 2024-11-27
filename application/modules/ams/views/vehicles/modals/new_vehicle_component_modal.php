<div class="modal fade" id="new-vehicle-component-dialog">
    <div class="modal-dialog modal-lg" role="dialog">
        <div class="modal-content">
            <form action id="frm-new-vehicle-component-dialog">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">

                <div class="modal-header">
                    <h5 class="modal-title">New Vehicle Component</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <input type="hidden" id="vehicle_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Brand
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       data-validation="required" name="brand" autocomplete="off" id="comp_vehicle_brand" onkeyup="generate_vehicle_comp_name()">
                                       <footer class="blockquote-footer">(Put N/A or NONE to enter name manually)</footer>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Model Name / No.
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       data-validation="required" name="model" autocomplete="off" id="comp_vehicle_model" onkeyup="generate_vehicle_comp_name()">
                                       <footer class="blockquote-footer">(Put N/A or NONE to enter name manually)</footer>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="comp_vehicle_name" class="form-control m-input input-auto-height"
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
                                    Category
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" data-validation="required"
                                        id="dlg-select2-asset-category" name="asset_category"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Sub Category
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" id="dlg-select2-sub-category"
                                        data-validation="required" name="sub_cat_code"></select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        
                        <div class="col">
                            <div class="form-group m-form__group">
                                <label>
                                    Type
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" id="dlg-select2-vehicle-type"
                                        name="category"
                                        data-validation="required">
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group m-form__group">
                                <label>Stock Code</label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="assetcode" autocomplete="off">
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
                                          name="description" data-validation="required" autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-3">
                            <div class="form-group m-form__group">
                                <label>Status</label>
                                <select class="form-control m-input" id="dlg-select2-status" name="status2">
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group m-form__group">
                                <label>Remarks</label>
                                <textarea class="form-control m-input" rows="3"
                                          name="status" autocomplete="off"></textarea>
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
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
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
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group mb-0">
                                <label>
                                    Operational
                                </label>
                                <div>
                                    <span class="m-switch m-switch--icon-check">
                                    <label>
                                        <input type="checkbox" name="isOper">
                                        <span></span>
                                    </label>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Serial No.</label>
                                <input type="text" class="form-control m-input input-auto-height" name="serialno" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Check No.</label>
                                <input type="text" class="form-control m-input input-auto-height" name="check_no" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>PO No.</label>
                                <input type="text" class="form-control m-input input-auto-height" name="po_no" autocomplete="off">
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
                    </div>

                    <div class="row mt-5">
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
                                <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
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
                                <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Receiving Report Ref. No.</label>
                                <input type="text" class="form-control m-input input-auto-height" name="rr_no" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Purchase Price
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control m-input input-auto-height text-right"
                                           name="purchaseprice" autocomplete="off" data-validation="required"
                                           style="font-weight: bold;" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Grand Total
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control m-input input-auto-height text-right"
                                           name="total_cost" autocomplete="off" data-validation="required" readonly value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <button class="btn btn-warning m-btn mr-1 btnSave m--font-boldest"
                                onclick="openGenerateMulipleModal()"
                                type="button">Save Multiple Vehicle Component
                        </button>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 text-right">
                        <button type="button" onclick="saveNewVehicleComponent()" class="btn btn-primary btnNew">
                            Save
                        </button>
                        <button type="button" class="btn btn-danger text-light btnNew" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" modal-exempt-custom data-backdrop="static" id="generate-multiple-vehicle-component-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" id="frm-generate-multiple-vehicle-component">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Multiple Vehicles</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="m-alert m-alert--outline alert m-alert--outline-2x alert-info mb-5"
                         role="alert">
                        This will automatically generate asset codes.
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-xl-8 col-lg-8 col-md-8 col-sm-12 d-flex flex-column justify-content-center">
                            <span class="m--font-bolder">NUMBER OF ASSETS TO GENERATE?</span>
                        </label>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <input type="number" min="1" class="form-control text-right" id="count" name="count" value="1"
                                   data-validation="required">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">
                        Generate
                    </button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="confirm-save-new-vehicle-component">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-transform: none; font-size: 18px;">
                    Are you sure to save new vehicle component?
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew" onclick="saveNewVehicleComponent()">
                    Yes
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    No
                </button>
            </div>
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
    

function generate_vehicle_comp_name(){
    var brand_name = $("#comp_vehicle_brand").val().toLowerCase();
    var model_name = $("#comp_vehicle_model").val().toLowerCase();
    var name = "";
    
    if((brand_name == "n/a" || brand_name == "none") && (model_name == "n/a" || model_name == "none")){
        name = "";
        $("#comp_vehicle_name").attr('readonly', false);
    }else if((model_name == "n/a" || model_name == "none") && (brand_name != "n/a" || brand_name != "none")){
        name = brand_name;
    }else if((brand_name == "n/a" || brand_name == "none") && (model_name != "n/a" || model_name != "none")){
        name = model_name;
    }else{
        name = brand_name.concat(" ", model_name);
    }
    $("#comp_vehicle_name").val(name);
}   
</script>