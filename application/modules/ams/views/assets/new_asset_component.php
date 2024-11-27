<input type="file" id="images" name="images[]" multiple
       style="position: fixed; top: -500px; visibility: hidden;"
       accept="image/*">

<form class="m-content" id="frm-new-asset-component">
    <input type="hidden" name="csrf_token"
           value="<?php echo $this->security->get_csrf_hash(); ?>">

    <div class="row">
        <div class="col-xl-9 order-2 order-xl-1">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <!--<i class="flaticon-truck"></i>-->
                                <a type="button" href="asset_components_masterfile"
                                   class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew"
                                   data-toggle="m-tooltip" data-original-title="Back to Masterfile"
                                   data-delay='{"show": 600}' data-skin="dark">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                                NEW ASSET COMPONENT
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Brand
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="brand" autocomplete="off"
                                       data-validation="required" id="comp_asset_brand" onkeyup="generate_asset_comp_name()">
                                       <footer class="blockquote-footer">(Put N/A or NONE to enter name manually)</footer>
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
                                       data-validation="required" id="comp_asset_model" onkeyup="generate_asset_comp_name()">
                                       <footer class="blockquote-footer">(Put N/A or NONE to enter name manually)</footer>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="name" data-validation="required" autocomplete="off" id="comp_asset_name">
                                       <footer class="blockquote-footer">(Auto-generated from Model and Brand)</footer>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Company
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" data-validation="required"
                                        id="select2-company" name="company_code"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Department
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" data-validation="required"
                                        id="select2-asset-department" name="department_code"></select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-xs-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Category
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" id="select2-asset-category"
                                        data-validation="required" name="asset_category"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-xs-12">
                            <div class="form-group m-form__group">
                                <label>
                                    Type
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control m-input" id="select2-sub-category"
                                        data-validation="required" name="sub_cat_code"></select>
                            </div>
                        </div>
                        <!-- Hidden as per requested by CMD -->
                        <div class="col-xl-1 col-lg-1 col-md-2 col-sm-12" hidden>
                            <div class="form-group m-form__group">
                                <label>
                                    Generate
                                </label>
                                <div class="d-block m-switch m-switch--icon">
                                    <label>
                                        <input type="checkbox" name="isGen" checked oninput="setDisabledOnAssetCode()">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12" hidden>
                            <div class="form-group m-form__group">
                                <label>
                                    Asset Code
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="gen_code" autocomplete="off">
                            </div>
                        </div>
                        <!-- End -->
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
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
                                        id="select2-station"
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
                                        id="select2-location"
                                        name="area_id"
                                        data-validation="required"></select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select class="form-control m-input" id="select2-status" name="status" data-validation="required">
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
                                <label>
                                    Serial No.
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control m-input input-auto-height"
                                       name="serialno" autocomplete="off"
                                       data-validation="required">
                            </div>
                        </div>
                        <div id="checkno_container" class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group m-form__group">
                                <label>Check No.</label>
                                <textarea id="asset_checkno" class="form-control m-input input-auto-height"
                                                   name="check_no" autocomplete="off" rows="1"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12" id="purchased_date">
                            <div class="form-group m-form__group">
                                <label>Date Purchased</label>
                                <div class="input-group date dt-picker">
                                    <input type="text" class="form-control m-input input-auto-height"
                                           name="datepurchased" autocomplete="off">
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
                                           name="recovered_date" autocomplete="off">
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
                                           style="font-weight: bold;" onkeyup="setTotalCost()">
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
                    <div class="mt-5 d-flex justify-content-end">
                        <button class="btn btn-lg btn-default m-btn btnNew mr-3" onclick="formReset()" type="button">Reset</button>
                        <button class="btn btn-lg btn-primary m-btn">Submit</button>
                    </div>
                </div>
            </div>

            
        </div>
        <div class="col-xl-3 order-1 order-xl-2">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                    <i class="la la-camera"></i>
                        </span>
                            <h3 class="m-portlet__head-text">Images</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <button type="button" onclick="openFileSelect()"
                                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-portlet__nav-link m-portlet__nav-link--icon btnNew"
                                        data-toggle="m-tooltip" data-original-title="Click to add images"
                                        data-delay='{"show": 300}' data-skin="dark">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body p-0">
                    <div class="primary-image-container">
                    </div>

                    <div class="images-preview-container d-flex flex-wrap">
                        <!-- <div class="images-preview-container__wrapper d-flex flex-column">
                             <div class="images-preview-container__image"></div>
                             <div class="images-preview-container__image__actions d-flex">
                                 <button type="button"
                                         class="btn btn-default m-btn--hover-danger m-btn m-btn--pill m-btn--icon m-btn--icon-only btnNew">
                                     <i class="la la-trash-o"></i>
                                 </button>
                             </div>
                         </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="confirm-save">
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
                    Are you sure to save new asset component information?
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew" onclick="saveNewAssetComponent()">
                    Yes
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    No
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="fail-upload-alert-dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div style="text-transform: none; font-weight: 400; font-size: 20px;" class="mb-4">
                    New Asset Component information was saved but some problems occurred on uploading the images.
                </div>

                <div style="text-transform: none; font-weight: 400; font-size: 14px;" class="mb-3">
                    These following images has problems uploading.
                </div>
                <div class="image-list">
                </div>

                <div class="auto-selected-primary">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew" data-dismiss="modal">
                    Ok, i understand.
                </button>
            </div>
        </div>
    </div>
</div>