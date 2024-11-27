<input type="file" id="images" name="images[]" multiple
       style="position: fixed; top: -500px; visibility: hidden;"
       accept="image/*">
<div class="position-fixed" style="-500%; display: none;">
    <!-- -->
    <input type="text" id="company_code" value="<?= $rs->company_code ?>">
    <input type="text" id="company_code_desc" value="<?= $rs->company_name ?>">
    <br/><br/>

    <input type="text" id="asset_category" value="<?= $rs->asset_category ?>">
    <input type="text" id="asset_category_desc" value="<?= $rs->asset_cat_description ?>">
    <br/><br/>

    <input type="text" id="department_code" value="<?= $rs->department_code ?>">
    <input type="text" id="department_code_desc" value="<?= $rs->dep_description ?>">
    <br/><br/>

    <input type="text" id="sub_cat_code" value="<?= $rs->sub_cat_code ?>">
    <br/><br/>

    <input type="text" id="area_id" value="<?= $rs->area_id ?>"> <!--station-->
    <input type="text" id="area" value="<?= $rs->location_description ?>"> <!--station-->
    <br/><br/>

    <input type="text" id="location" value="<?= $rs->location ?>">
    <input type="text" id="location_desc" value="<?= $rs->station_description ?>">
    <br/><br/>

    <input type="text" id="status" value="<?= $rs->status ?>">
    <input type="text" id="status_desc" value="<?= $rs->status_name ?>">
</div>

<div class="m-content">
    <div class="row">
        <div class="col-xl-9  order-2 order-xl-1">
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <!--<i class="flaticon-truck"></i>-->
                                <a href="../fixed_masterfile"
                                   data-toggle="m-tooltip" data-original-title="Back to Masterfile"
                                   data-delay='{"show": 600}'
                                   data-skin="dark"
                                   class="" style="text-decoration: none;">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                                EDIT FIXED ASSET
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav navbar-expand-sm nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line--primary m-tabs-line--2x"
                            role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab"
                                   href="#tab-details" role="tab" aria-expanded="true">
                                    Details
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-components" role="tab" aria-expanded="false"
                                   onclick="loadTab(this, 'tab_asset_components', true)">
                                    Components
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-documents" role="tab" aria-expanded="false"
                                   onclick="loadTab(this, 'tab_asset_documents', true)">
                                    Documents
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-accountability" role="tab" aria-expanded="false"
                                   onclick="loadTab(this, 'tab_asset_accountability', true)">
                                    Accountability
                                </a>
                            </li>
                            <!-- <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-borrowing" role="tab" aria-expanded="false"
                                   onclick="loadTab(this, 'tab_asset_borrowing_history', true)">
                                    Borrowing
                                </a>
                            </li> -->
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content mt-3">
                        <div class="tab-pane active" id="tab-details" role="tabpanel" aria-expanded="true">
                            <form id="frm-edit-fixed-asset">
                                <input type="hidden" name="csrf_token"
                                       value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <input type="hidden" name="id" value="<?= $rs->id ?>">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Brand
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="brand" autocomplete="off"
                                                   data-validation="required"
                                                   value="<?= $rs->brand ?>" id="update_asset_brand" onkeyup="edit_generate_asset_name()">
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
                                                   data-validation="required"
                                                   value="<?= $rs->modelno ?>" id="update_asset_model" onkeyup="edit_generate_asset_name()">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Name
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="name" data-validation="required" autocomplete="off" id="update_asset_name" value="<?= $rs->name ?>">
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
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-xs-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Category
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control m-input" id="select2-asset-category"
                                                    data-validation="required" name="asset_category" disabled></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-xs-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Type
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control m-input" id="select2-sub-category"
                                                    data-validation="required" name="sub_cat_code" disabled></select>
                                        </div>
                                    </div>
                                    <!-- Hidden as per requested by CMD -->
                                    <div class="col-xl-1 col-lg-1 col-md-2 col-sm-12" hidden>
                                        <div class="form-group m-form__group">
                                            <label>
                                                Generated
                                            </label>
                                            <div class="d-block m-switch m-switch--icon">
                                                <label>
                                                    <input type="checkbox" name="isGen" oninput="setDisabledOnAssetCode()"
                                                           <?= $rs->isGen == 1 ? "checked" : "" ?>>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Asset Code
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="assetacode" autocomplete="off" value="<?= $rs->assetacode ?>" readonly>
                                        </div>
                                    </div>
                                    <!-- end -->
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>
                                                Stock Code
                                            </label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="gl_code" autocomplete="off" value="<?= $rs->gl_code ?>">
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
                                                      name="assetname" data-validation="required" autocomplete="off"><?= $rs->assetname ?></textarea>
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
                                                      name="remarks2" autocomplete="off"><?= $rs->remarks2 ?></textarea>
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
                                                   data-validation="required" value="<?= $rs->supplier ?>">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>PO No.</label>
                                            <input type="text" class="form-control m-input input-auto-height" name="po_no" autocomplete="off"
                                                   value="<?= $rs->po_no ?>">
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
                                                   data-validation="required"
                                                   value="<?= $rs->serialno ?>">
                                        </div>
                                    </div>
                                    <div id="checkno_container" class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>Check No.</label>
                                            <textarea id="asset_checkno" class="form-control m-input input-auto-height"
                                                   name="check_no" autocomplete="off"><?= $rs->check_no ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <?php if($rs->datepurchased != "0000-00-00"){ ?>
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12" id="purchased_date">
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
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12" id="recovered_date">
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
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12" id="purchased_date">
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
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12" id="recovered_date">
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
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
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
                                                       name="date_received" autocomplete="off"
                                                       value="<?= $dateReceived; ?>">
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
                                                       name="warranty_date" autocomplete="off"
                                                       value="<?= $warranty_date; ?>">
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
                                                   name="rr_no" autocomplete="off" value="<?= $rs->rr_no ?>">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>Add'l Cost</label>
                                            <input type="text" class="form-control m-input input-auto-height text-right money-mask"
                                                   style="font-weight: bold;"
                                                   name="beg_addcost" autocomplete="off" onkeyup="setTotalCost()"
                                                   value="<?= $rs->beg_addcost ?>">
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
                                                       style="font-weight: bold;" onkeyup="setTotalCost()"
                                                       value="<?= number_format($rs->purchaseprice, 2, '.', ',') ?>">
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
                                                       name="total_cost" autocomplete="off" data-validation="required" readonly
                                                       value="<?= number_format($rs->total_cost, 2, '.', ',') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 row">
                                    <div class="form-group m-form__group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" style="text-transform: uppercase;">Created By</label>
                                        <input type="text" class="form-control" disabled value="<?= $rs->createdBy ?>">
                                    </div>
                                    <div class="form-group m-form__group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" style="text-transform: uppercase;">Created At</label>
                                        <input type="text" class="form-control" disabled
                                               value="<?= !empty($rs->dateCreated) ? date('Y-m-d, h:i:s a', strtotime($rs->dateCreated)) : null ?>">
                                    </div>
                                    <div class="form-group m-form__group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" style="text-transform: uppercase;">Updated By</label>
                                        <input type="text" class="form-control" disabled value="<?= $rs->_updatedBy ?>">
                                    </div>
                                    <div class="form-group m-form__group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" style="text-transform: uppercase;">Updated At</label>
                                        <input type="text" class="form-control" disabled
                                               value="<?= empty($rs->updatedBy) ? "" : date('Y-m-d, h:i:s a', strtotime($rs->dateUpdated)) ?>">
                                    </div>
                                </div>

                                <div class="mt-5 row">
                                    <div class="form-group m-form__group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" style="text-transform: uppercase;">Inventory By</label>
                                        <input type="text" class="form-control" id="inv_checked_by" disabled value="<?= $rs->checked_by ?>">
                                    </div>
                                    <div class="form-group m-form__group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" style="text-transform: uppercase;">Inventory Date</label>
                                        <input type="text" class="form-control" disabled id="inv_checked_date"
                                               value="<?= empty($rs->inventory_check_date) ? "" : date('Y-m-d', strtotime($rs->inventory_check_date)) ?>">
                                    </div>
                                </div>

                                <div class="mt-5 row justify-content-end">
                                    <div class="justify-content-end">
                                        <button type="button" class="btn btn-lg btn-success m-btn btnUpdate mr-2 mb-1" data-toggle="modal"
                                                data-target="#inventory-check-modal">Inventory Check
                                        </button>
                                        <?php if(in_array("update", $this->core_layout->getCurrentActions())){ ?>
                                        <button class="btn btn-lg btn-primary m-btn btnSave mb-1" type="submit">Save Changes</button>
                                        <?php } ?>
                                    </div>
                                </div>
                                <input type="hidden" id="archive_remarks_update" name="archive_remark">
                            </form>
                        </div>

                        <div class="tab-pane" id="tab-components" role="tabpanel" aria-expanded="false"></div>

                        <div class="tab-pane" id="tab-documents" role="tabpanel" aria-expanded="false"></div>

                        <div class="tab-pane" id="tab-accountability" role="tabpanel" aria-expanded="false"></div>

                        <!-- <div class="tab-pane" id="tab-borrowing" role="tabpanel" aria-expanded="false"></div> -->
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
                                <button type="button"
                                        onclick="openFileSelect()"
                                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-portlet__nav-link m-portlet__nav-link--icon btnNew"
                                        data-toggle="m-tooltip" data-skin="dark" data-original-title="Click to add images."
                                        data-delay='{"show": 300}'>
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
                                   href="<?= base_url('uploads/files/images/assets/' . $rs->id . '/' . $row->name) ?>">
                                    <div class="images-preview-container__image"
                                         style="background-image: url('<?= base_url('uploads/files/images/assets/' . $rs->id . '/' . $row->name) ?>')">
                                    </div>
                                </a>
                                <div class="images-preview-container__image__actions d-flex">
                                    <button title="" type="button"
                                            class="mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary
                                                   m-btn--icon m-btn--icon-only btnNew <?= $row->name == $rs->pic_filename ? 'selected' : '' ?>"
                                            data-name="<?= $row->name ?>"
                                            onclick="setPrimaryPictureFromUploaded('<?= $row->name ?>', '<?= $rs->id ?>', this)"
                                            data-toggle="m-tooltip"
                                            data-original-title="Set as Primary" data-skin="dark" data-placement="bottom"
                                            data-delay='{"show": 300}'>
                                        <i class="la la-check"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-default m-btn--hover-danger m-btn m-btn--pill m-btn--icon m-btn--icon-only btnNew"
                                            data-index="" data-name="" onclick="removeUploaded('<?= $row->name ?>', <?= $row->id ?>, this)"
                                            data-toggle="m-tooltip"
                                            data-original-title="Remove Image" data-skin="dark" data-placement="bottom"
                                            data-delay='{"show": 300}'>
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

    <div class="modal fade document-modal-container" modal-exempt-custom data-keyboard="false" data-backdrop="static" tabindex="-1"
         role="dialog"></div>
    <?php include_once("modals/new_asset_component_modal.php"); ?>
</div>

<div class="modal fade" id="confirm-update-fix-asset" tabindex="-1" role="dialog">
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
                    Are you sure to update asset information?
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew" onclick="updateAsset()">
                    Yes
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    No
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alert-dialog" data-backdrop="static" tabindex="-1" role="dialog">
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

<div class="modal fade" id="fail-upload-alert-dialog" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div style="text-transform: none; font-weight: 400; font-size: 20px;" class="mb-4">
                    Asset information was updated but some problems occurred on uploading the images.
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

<div class="modal fade" id="re-include-component-confirm-dialog" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="dialog">
        <form class="modal-content" onsubmit="processIncludeComponent(this, event)">
            <div class="modal-header">
                <h5 class="modal-title">Re-include Component</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <p class="m--regular-font-size-lg2">Are you sure to re-include this component?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">
                    Yes
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    No
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="exclude-reason-dialog" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="dialog">
        <form class="modal-content" onsubmit="processExcludeComponent(this, event)">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-header">
                <h5 class="modal-title normal-case">EXCLUDE COMPONENT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label for="exclude-reason-text-area">Remarks</label>
                    <textarea class="form-control m-input" name="remarks"
                              id="exclude-reason-text-area" rows="3"></textarea>
                </div>

                <div class="form-group m-form__group mt-4">
                    <label for="exclude-reason-text-area">Is it Damaged ?</label>
                    <div>
                        <span class="m-switch m-switch--icon-check d-flex flex-row align-items-center">
                            <label class="mb-0">
                                <input type="checkbox" name="isDamage" onchange="setIsDamageStr(this)">
                                <span></span>
                            </label>
                            <span class="ml-2 is-damage-identifier"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">
                    Continue
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="component-list-dialog" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asset List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body pt-3">
                <div class="row d-flex flex-row align-items-end">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary m-tabs-line--2x mb-0"
                            role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="" role="tab"
                                   aria-expanded="true" onclick="getAssetList(1, event)">
                                    MOTHER ASSETS
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="" role="tab"
                                   aria-expanded="false" onclick="getAssetList(0, event)">
                                    ASSET COMPONENTS
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="offset-xl-1 offset-lg-1 offset-md-1 offset-sm-0"></div>
                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                        <div class="form-group m-form__group pb-0 mb-0">
                            <div class="m-input-icon m-input-icon--left">
                                        <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                                            <span>
                                                <i class="la la-binoculars"></i>
                                            </span>
                                        </span>
                                <div class="input-group">
                                    <input type="search" class="form-control search-all-component-list"
                                           placeholder="Search Here..."
                                           style="height: auto;" autocomplete="off">
                                    <span class="input-group-btn">
                                        <button class="btn btn-secondary btnNew" type="button"
                                                data-toggle="m-tooltip"
                                                data-original-title="Clear Search"
                                                data-skin="dark"
                                                data-placement="bottom"
                                                data-delay='{"show": 300}'
                                                style="border-color: #cdcdcd;"
                                                onclick="clearSearchAllComponentList()">
                                            <i class="la la-close"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 table-responsive-sm">
                    <table class="table table-striped table-bordered"
                           id="all-component-list" width="100%">
                        <thead>
                        <tr>
                            <th></th>
                            <th>CODE</th>
                            <th>NAME</th>
                            <th>ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="remove-component-confirm-dialog" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="dialog">
        <form class="modal-content" onsubmit="processRemoveComponent(this, event)">
            <div class="modal-header">
                <h5 class="modal-title">Remove Component Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <p class="m--regular-font-size-lg2">Are you sure to remove this component?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">
                    Yes
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    No
                </button>
            </div>
        </form>
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
                    <button type="button" onclick="inv_check()" class="btn m-btn btn-submit btn-primary btnNew">
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
                    <h5 class="modal-title"><span class="m--font-bolder">Archive</span> Asset Confirmation</h5>
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