<input type="file" id="images" name="images[]" multiple
       style="position: fixed; top: -500px; visibility: hidden;"
       accept="image/*">

    
<div class="m-content">
    <div class="row">
        <div class="col-xl-9 order-2 order-xl-1">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon">
                            <!--<i class="flaticon-truck"></i>-->
                            <a type="button" href="../vehicle_components_masterfile" title="Back to Masterfile"
                            data-delay='{"show": 600}'
                            class="" style="text-decoration: none;">
                                <i class="la la-arrow-left"></i>
                            </a>
                        </span>
                            <h3 class="m-portlet__head-text">
                                EDIT VEHICLE COMPONENT
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav navbar-expand-sm nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line--primary m-tabs-line--2x"
                            role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link tab_details active" data-toggle="tab"
                                   href="#tab-details" role="tab" aria-expanded="true">
                                    Details
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link tab_accountability" data-toggle="tab"
                                   href="#tab-accountability" role="tab" aria-expanded="false">
                                    Accountability
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content mt-3">
                        <div class="tab-pane active" id="tab-details">
                        <form id="frm-edit-vehicle-component">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="id" value="<?= $rs->id ?>">
                                <?php if (!empty($rs->mother_code)): ?>
                                    <div class="row">
                                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group m-form__group">
                                                <label>
                                                    MOTHER ASSET
                                                </label>
                                                <input type="text" disabled class="form-control m-input input-auto-height"
                                                    name="mother_code" data-validation="required" autocomplete="off" value="<?= $rs->mother_code ?>">
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group m-form__group">
                                                <label>
                                                    &nbsp;
                                                </label>
                                                <input type="text" disabled class="form-control m-input input-auto-height"
                                                    name="mother_name" data-validation="required" autocomplete="off" value="<?= $rs->mother_name ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Brand
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                data-validation="required" name="brand" autocomplete="off"
                                                value="<?= $rs->brand ?>" id="edit_comp_vehicle_brand" onkeyup="edit_generate_vehicle_comp_name()">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Model Name / No.
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                data-validation="required" name="model" autocomplete="off"
                                                value="<?= $rs->model ?>" id="edit_comp_vehicle_model" onkeyup="edit_generate_vehicle_comp_name()">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Name
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                name="name" data-validation="required" autocomplete="off"
                                                value="<?= $rs->name ?>" id="edit_comp_vehicle_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Company
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control m-input" data-validation="required"
                                                    id="select2-company" name="company_code" ></select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Category
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control m-input" data-validation="required"
                                                    id="select2-asset-category" name="asset_category" disabled></select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Sub Category
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control m-input" id="select2-sub-category"
                                                    data-validation="required" name="sub_cat_code" disabled></select>
                                        </div>
                                    </div>
                                </div>    
                                <div class="row mt-2">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Type
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control m-input" id="select2-vehicle-type"
                                                    name="category"
                                                    data-validation="required" disabled>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Asset Code
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                name="gen_code" autocomplete="off"
                                                value="<?= $rs->gen_code ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>Stock Code</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                name="assetcode" autocomplete="off" value="<?= $rs->assetcode ?>">
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
                                                    name="description" data-validation="required"
                                                    autocomplete="off"><?= $rs->description ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select class="form-control m-input" id="select2-status" name="status2" data-validation="required">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Remarks</label>
                                            <textarea class="form-control m-input" rows="3"
                                                    name="status" autocomplete="off"><?= $rs->status ?></textarea>
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
                                    <div class="col-sm-12 col-md-12 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Supplier
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                name="supplier" autocomplete="off"
                                                data-validation="required" value="<?= $rs->supplier ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-4">
                                        <div class="form-group m-form__group mb-0">
                                            <label>
                                                Operational
                                            </label>
                                            <div>
                                                <span class="m-switch m-switch--icon-check">
                                                <label>
                                                    <input type="checkbox"
                                                        name="isOper" <?= (int)$rs->isOper === 1 ? 'checked' : '' ?>>
                                                    <span></span>
                                                </label>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>Serial No.</label>
                                            <input type="text" class="form-control m-input input-auto-height" name="serialno"
                                                autocomplete="off"
                                                value="<?= $rs->serialno ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div id="checkno_container" class="col-sm-12 col-md-12 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>Check No.</label>
                                            <textarea id="vehicle_checkno" class="form-control m-input input-auto-height" name="check_no"
                                                autocomplete="off" rows="2"><?= $rs->check_no ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>PO No.</label>
                                            <input type="text" class="form-control m-input input-auto-height" name="po_no"
                                                autocomplete="off"
                                                value="<?= $rs->po_no ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-4">
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
                                </div>

                                <div class="row mt-3">
                                    <?php if($rs->datepurchased != "0000-00-00"){ ?>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" id="purchased_date">
                                        <div class="form-group m-form__group">
                                            <label>Date Purchased</label>
                                            <?php
                                                $temp0 = $rs->datepurchased;
                                                $datePurchased = "";
                                                if($temp0 && $temp0 !== "0000-00-00"){
                                                    $temp0 = str_replace('/', '-', $temp0);
                                                    $datePurchased = date("Y/m/d", strtotime(trim($temp0)));
                                                }
                                            ?>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                    name="datepurchased" autocomplete="off" 
                                                    value="<?= $datePurchased; ?>">
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                                        </div>
                                    </div>
                                    <?php }else if($rs->recovered_date != "0000-00-00"){ ?>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" id="recovered_date">
                                        <div class="form-group m-form__group">
                                            <label>Date Recovered</label>
                                            <?php
                                                $temp0 = $rs->recovered_date;
                                                $dateRecovered = "";
                                                if($temp0 && $temp0 !== "0000-00-00"){
                                                    $temp0 = str_replace('/', '-', $temp0);
                                                    $dateRecovered = date("Y/m/d", strtotime(trim($temp0)));
                                                }
                                            ?>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                    name="recovered_date" autocomplete="off" 
                                                    value="<?= $dateRecovered; ?>">
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                                        </div>
                                    </div>
                                    <?php }else{ ?>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" id="purchased_date">
                                        <div class="form-group m-form__group">
                                            <label>Date Purchased</label>
                                            <?php
                                                $temp0 = $rs->datepurchased;
                                                $datePurchased = "";
                                                if($temp0 && $temp0 !== "0000-00-00"){
                                                    $temp0 = str_replace('/', '-', $temp0);
                                                    $datePurchased = date("Y/m/d", strtotime(trim($temp0)));
                                                }
                                            ?>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                    name="datepurchased" autocomplete="off" 
                                                    value="<?= $datePurchased; ?>">
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" id="recovered_date">
                                        <div class="form-group m-form__group">
                                            <label>Date Recovered</label>
                                            <?php
                                                $temp0 = $rs->recovered_date;
                                                $dateRecovered = "";
                                                if($temp0 && $temp0 !== "0000-00-00"){
                                                    $temp0 = str_replace('/', '-', $temp0);
                                                    $dateRecovered = date("Y/m/d", strtotime(trim($temp0)));
                                                }
                                            ?>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                    name="recovered_date" autocomplete="off" 
                                                    value="<?= $dateRecovered; ?>">
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                                        </div>
                                    </div>    
                                    <?php } ?>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>Date Received</label>
                                            <?php
                                                $temp1 = $rs->date_received;
                                                $dateReceived = "";
                                                if($temp1 && $temp1 !== "0000-00-00"){
                                                    $temp1 = str_replace('/', '-', $temp1);
                                                    $dateReceived = date("Y/m/d", strtotime(trim($temp1)));
                                                }
                                            ?>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                    name="date_received" autocomplete="off" value="<?= $dateReceived; ?>">
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>Warranty Date</label>
                                            <?php
                                                $temp1 = $rs->warranty_date;
                                                $warranty_date = "";
                                                if($temp1 && $temp1 !== "0000-00-00"){
                                                    $temp1 = str_replace('/', '-', $temp1);
                                                    $warranty_date = date("Y/m/d", strtotime(trim($temp1)));
                                                }
                                            ?>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                    name="warranty_date" autocomplete="off" value="<?= $warranty_date; ?>">
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: yyyy/mm/dd</div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>Receiving Report Ref. No.</label>
                                            <input type="text" class="form-control m-input input-auto-height" name="rr_no"
                                                autocomplete="off"
                                                value="<?= $rs->rr_no ?>">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Purchase Price
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control m-input input-auto-height text-right"
                                                    name="purchaseprice" autocomplete="off" data-validation="required"
                                                    style="font-weight: bold;"
                                                    value="<?= number_format($rs->purchaseprice, 2, '.', ',') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Grand Total
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control m-input input-auto-height text-right"
                                                    name="total_cost" autocomplete="off" data-validation="required" readonly
                                                    value="<?= number_format($rs->total_cost, 2, '.', ',') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 row">
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Created By</label>
                                        <input type="text" class="form-control" disabled value="<?= $rs->created_by ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Created At</label>
                                        <input type="text" class="form-control" disabled
                                            value="<?= (empty($rs->created_by) || $rs->created_at === "0000-00-00 00:00:00") ? "" :
                                                date('Y-m-d, h:i:s a', strtotime($rs->created_at)) ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Updated By</label>
                                        <input type="text" class="form-control" disabled value="<?= $rs->updated_by ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Updated At</label>
                                        <input type="text" class="form-control" disabled
                                            value="<?= (empty($rs->updated_at) || $rs->updated_at === "0000-00-00 00:00:00") ? "" :
                                                date('Y-m-d, h:i:s a', strtotime($rs->updated_at)) ?>">
                                    </div>
                                </div>

                                <div class="mt-3 row">
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Inventory By</label>
                                        <input type="text" class="form-control" id="inv_checked_by" disabled value="<?= $rs->checked_by ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Inventory Date</label>
                                        <input type="text" class="form-control" disabled id="inv_checked_date"
                                            value="<?= empty($rs->inventory_check_date) ? "" : date('Y-m-d,', strtotime($rs->inventory_check_date)) ?>">
                                    </div>
                                </div>
                                <div class="flex-row justify-content-end mt-5">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-lg btn-success m-btn btnUpdate mr-2"
                                                data-toggle="modal" data-target="#inventory-check-modal">Inventory Check
                                        </button>
                                        <?php if(in_array("update", $this->core_layout->getCurrentActions())){ ?>
                                        <button class="btn btn-lg btn-primary m-btn btnSave" type="submit">Save Changes</button>
                                        <?php } ?>
                                    </div>
                                </div> 
                                <input type="hidden" id="archive_remarks_update" name="archive_remark">
                            </form>       
                        </div>
                        <div class="tab-pane" id="tab-accountability" role="tabpanel" aria-expanded="false">
                            <div class="row">
                                <div class="col-xl-8 order-2 order-xl-1">
                                </div>
                                <div class="col-xl-4 order-1 order-xl-2">
                                    <div class="form-group m-form__group pb-0">
                                        <div class="m-input-icon m-input-icon--left">
                                            <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                                                <span>
                                                    <i class="la la-binoculars"></i>
                                                </span>
                                            </span>
                                            <div class="input-group">
                                                <input type="search" class="form-control" placeholder="Search Here..."
                                                    style="height: auto;" id="search-vehicle-accountability" autocomplete="off">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-secondary btnNew tab-clear-search" type="button"
                                                            title="Clear Search"
                                                            data-placement="bottom"
                                                            style="border-color: #cdcdcd;">
                                                        <i class="la la-close"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll mt-3">
                                <table class="table table-striped table-bordered" id="tab-accountability-table" width="100%">
                                    <thead>
                                    <tr>
                                        <th>DATE</th>
                                        <th>REFERENCE NO.</th>
                                        <th>ASSIGNED TO</th>
                                        <th>OVERDUE</th>
                                        <th>STATUS</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        
                        
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
                                <button type="button" title="Click to add images" onclick="openFileSelect()"
                                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-portlet__nav-link m-portlet__nav-link--icon btnNew">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body p-0">
                    <div class="primary-image-container">
                    <a data-lightbox='roadtrip' id="primary_pic" class="float-right mr-2 mt-2"><button type="button" class="btn btn-primary m-btn m-btn--pill m-btn--icon m-btn--icon-only btnNew" data-toggle="m-tooltip" data-original-title="View"
                                            data-skin="dark"><i class='la la-expand'></i></button></a>
                    </div>

                    <div class="images-preview-container d-flex flex-wrap">
                        <?php foreach ($images as $row) { ?>
                            <div class="d-flex flex-column images-preview-container__wrapper isUploaded">
                                <a data-lightbox="roadtrip" data-title="<?= $row->name ?>"
                                   href="<?= base_url('uploads/files/images/vehicles/' . $rs->id . '/' . $row->name) ?>">
                                    <div class="images-preview-container__image"
                                         style="background-image: url('<?= base_url('uploads/files/images/vehicles/' . $rs->id . '/' . $row->name) ?>')">
                                    </div>
                                </a>
                                <div class="images-preview-container__image__actions d-flex">
                                    <button title="Set as Primary" data-placement="bottom" type="button"
                                            class="mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary m-btn--icon m-btn--icon-only btnNew <?= $row->name == $rs->primary_pic ? 'selected' : '' ?>"
                                            data-name="<?= $row->name ?>"
                                            onclick="setPrimaryPictureFromUploaded('<?= $row->name ?>', '<?= $rs->id ?>', this)">
                                        <i class="la la-check"></i>
                                    </button>
                                    <button title="Remove Image" data-placement="bottom" type="button"
                                            class="btn btn-default m-btn--hover-danger m-btn m-btn--pill m-btn--icon m-btn--icon-only btnNew"
                                            data-index="" data-name=""
                                            onclick="removeUploaded('<?= $row->name ?>', <?= $row->id ?>, this)">
                                        <i class="la la-trash-o"></i>
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-update-vehicle">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-transform: none; font-size: 18px;">
                    Are you sure to save changes on vehicle component information?
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew" onclick="updateVehicle()">
                    Yes
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    No
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alert-dialog">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title normal-case">Oops! Unable to proceed.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="normal-case">Can't remove an image used as Primary Picture.</h5>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew" data-dismiss="modal">
                    Ok, i understand.
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
                    Vehicle component information was updated but some problems occurred on uploading the images.
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

<div class="modal fade" id="inventory-check-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Inventory check
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                </button>
            </div>
            <form id="inventory-check-form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group m-form__group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <label>Status</label>
                            <select id="inventory-status"></select>
                        </div>

                        <div class="form-group m-form__group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <label>Inventory Date</label>
                            <div class="input-group date" id="inventory-dt">
                                <input class="form-control m-input" type="text" name="inv_dt" id="date_inv" maxlength="22"/>
                                <span class="input-group-addon">
                                    <i class="la la-calendar glyphicon-th"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="inv_check()" class="btn m-btn btn-submit btn-primary btnUpdate">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade archive-remarks" tabindex="-1"  role="dialog">
    <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="m--font-bolder">Archive</span> Vehicle Component Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group">
                        <label for="archive-remarks-text-area">Please leave a remark</label>
                        <textarea name="archive_remark" class="form-control m-input" id="archive-remarks-text-area"
                                    rows="3"
                                    data-validation="required"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btnArchive" type="button">Archive</button>
                    <button class="btn btn-danger btnClose" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
    </div>
</div>
<script type="text/javascript">
    /*** var tempRs = {};
    tempRs = <?php echo json_encode($rs); ?>; ***/

    if ("<?=$rs->company_name?>" && "<?=$rs->company_code?>") {
        var newCompanyOption = new Option("<?=$rs->company_name?>", "<?=$rs->company_code?>", false, true);
        $('#select2-company').append(newCompanyOption).trigger('change');
    }

    if ("<?=$rs->asset_cat_description?>" && "<?=$rs->asset_category?>") {
        var assetCategoryOption = new Option("<?=$rs->asset_cat_description?>", "<?=$rs->asset_category?>", false, true);
        $('#select2-asset-category').append(assetCategoryOption).trigger('change');
        setTimeout(() => {
            if ("<?=$rs->subcat_description?>" && "<?=$rs->sub_cat_code?>") {
                var assetSubCategoryOption = new Option("<?=$rs->subcat_description?>", "<?=$rs->sub_cat_code?>", false, true);
                $('#select2-sub-category').append(assetSubCategoryOption).trigger('change');
            }
        }, 1200);
    }


    if ("<?=$rs->equip_cat_description?>" && "<?=$rs->category?>") {
        var equipmentCategoryOption = new Option("<?=$rs->equip_cat_description?>", "<?=$rs->category?>", false, true);
        $('#select2-vehicle-type').append(equipmentCategoryOption).trigger('change');
    }

    if ("<?=$rs->equip_cat_description?>" && "<?=$rs->category?>") {
        var equipmentCategoryOption = new Option("<?=$rs->equip_cat_description?>", "<?=$rs->category?>", false, true);
        $('#select2-vehicle-type').append(equipmentCategoryOption).trigger('change');
    }

    if ("<?=$rs->location?>" && "<?=$rs->area_id?>") {
        var locationOption = new Option("<?=$rs->location?>", "<?=$rs->area_id?>", false, true);
        $('#select2-location').append(locationOption).trigger('change');
    }

    if ("<?=$rs->status_name?>" && "<?=$rs->status2?>") {
        var statusOption = new Option("<?=$rs->status_name?>", "<?=$rs->status2?>", false, true);
        $('#select2-status').append(statusOption).trigger('change');
    }

    $.ajax({
        url: baseUrl("ams/vehicles/get_sub_category_collection/?cat_id=<?=$rs->asset_category?>"),
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (response) {
            const select2SubCategory = $("#select2-sub-category");

            // select2SubCategory.select2("destroy");
            // $("#select2-sub-category option").each(function () {
            //     $(this).remove();
            // });
            // add empty field to not auto-select first option
            select2SubCategory.select2({placeholder: 'Select Subcategory', width: '100%',});
            // add empty field to not auto-select first option
            select2SubCategory.append(new Option("", "", false, false)).trigger('change');

            const rs_sub_cat_code = <?=$rs->sub_cat_code?>;
            $.each(response, function (key, value) {
                const isSame = parseInt(value.sub_cat_id) === parseInt(rs_sub_cat_code);
                var newOption = new Option(value.sub_cat_desc, value.sub_cat_code, false, isSame);
                select2SubCategory.append(newOption).trigger('change');
            });
        }
    });
    //override default tab functions
    $(document).ready(function(){
        $(".tab_details").on('click', function(){
        $(".tab-pane").removeClass('active');
        });
        $(".tab_accountability").click(function(){
            $(".tab-pane").removeClass('active');
        });

        if($("#vehicle_checkno").val().length >= 50){
            $("#checkno_container").removeClass('col-xl-4');
            $("#checkno_container").addClass('col-xl-12');
        }else{  
            $("#checkno_container").removeClass('col-xl-12 col-lg-12 col-md-12 col-sm-12');
            $("#checkno_container").addClass('col-xl-4 col-lg-4 col-md-4 col-sm-12');
        }
        
    });

    $("#vehicle_checkno").keyup(function(){
    // alert("50 na");

        if($("#vehicle_checkno").val().length >= 50){
            $("#checkno_container").removeClass('col-xl-4');
            $("#checkno_container").addClass('col-xl-12');
        }else{  
            $("#checkno_container").removeClass('col-xl-12 col-lg-12 col-md-12 col-sm-12');
            $("#checkno_container").addClass('col-xl-4 col-lg-4 col-md-4 col-sm-12');
        }
    });
    //end
</script>