<input type="file" id="images" name="images[]" multiple
       style="position: fixed; top: -500px; visibility: hidden;"
       accept="image/*">

<div class="m-content">
    <div class="row">
        <div class="col-xl-9  order-2 order-xl-1">
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title d-flex flex-row align-items-center">
                            <button class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill mr-3"
                                    onclick="window.history.back();">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            <h3 class="m-portlet__head-text mb-0">
                                <?= $title ?>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line--primary m-tabs-line--2x"
                            role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab"
                                   href="#tab-details" role="tab" aria-expanded="true">
                                    Details
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-components" role="tab" aria-expanded="false">
                                    Components
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-costing" role="tab" aria-expanded="false">
                                    Costing
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-documents" role="tab" aria-expanded="false">
                                    Documents
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-accountability" role="tab" aria-expanded="false">
                                    Accountability
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab"
                                   href="#tab-maintenance" role="tab" aria-expanded="false">
                                    Maintenance
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content mt-3">
                        <div class="tab-pane active" id="tab-details" role="tabpanel" aria-expanded="true">
                            <form id="frm-edit-vehicle">
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
                                                    value="<?= $rs->manufaturer ?>" id="update_asset_brand" disabled>
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
                                                    value="<?= $rs->model ?>" id="update_asset_model" disabled>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label>Name</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="name" data-validation="required" autocomplete="off"
                                                   value="<?= $rs->name ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Company</label>
                                            <input type="text" class="form-control" disabled value="<?= $rs->company_name ?>">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Category</label>
                                            <input type="text" class="form-control" disabled value="<?= $rs->asset_cat_description ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Sub Category</label>
                                            <input type="text" class="form-control" disabled value="<?= $sub_category ?>">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Type</label>
                                            <input type="text" class="form-control" disabled value="<?= $rs->equip_cat_description ?>">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Stock Code</label>
                                            <input type="text" class="form-control" disabled value="<?= $rs->assetcode ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Description</label>
                                            <textarea class="form-control m-input" rows="3"
                                                      name="description" data-validation="required"
                                                      autocomplete="off" disabled><?= $rs->description ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Manufacturer</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   data-validation="required" name="manufaturer" autocomplete="off"
                                                   value="<?= $rs->manufaturer ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Chasis No.</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   data-validation="required" name="chasisno" autocomplete="off"
                                                   value="<?= $rs->chasisno ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Year Model</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   data-validation="required" name="year" autocomplete="off"
                                                   value="<?= $rs->year ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Model No.</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   data-validation="required" name="model" autocomplete="off"
                                                   value="<?= $rs->model ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Engine No.</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   data-validation="required" name="engineno" autocomplete="off"
                                                   value="<?= $rs->engineno ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Body No.</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   data-validation="required" name="bodyno" autocomplete="off"
                                                   value="<?= $rs->bodyno ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Tire Size</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   data-validation="required" name="tire_size" autocomplete="off"
                                                   value="<?= $rs->tire_size ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-xl-3">
                                        <div class="d-flex flex-row align-items-center">
                                            <div class="form-group m-form__group flex-grow-1 flex-shrink-1">
                                                <label>Status</label>
                                                <input type="text" class="form-control" disabled value="<?= $status_name ?>">
                                            </div>
                                            <?php if (strtolower($status_name) !== "archived"): ?>
                                                <div class="mr-3"></div>
                                                <div class="flex-grow-0 flex-shrink-1" style="margin-top: 10px;">
                                                    <button style="height: 32px; line-height: 1;"
                                                            type="button" class="btn btn-primary m-btn btnUpdate"
                                                            data-toggle="modal" data-target="#recover-modal">Recover
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Remarks</label>
                                            <textarea class="form-control m-input" rows="3"
                                                      name="status" autocomplete="off" disabled><?= $rs->status ?></textarea>
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

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Operator</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="driver" autocomplete="off"
                                                   value="<?= $rs->driver ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Plate No.</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="plateno" autocomplete="off"
                                                   data-validation="required"
                                                   value="<?= $rs->plateno ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Location</label>
                                            <input type="text" class="form-control" disabled value="<?= $rs->location_description ?>">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Color</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="color" autocomplete="off"
                                                   value="<?= $rs->color ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Supplier</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="supplier" autocomplete="off"
                                                   data-validation="required"
                                                   value="<?= $rs->supplier ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group mb-0">
                                            <label>
                                                Operational
                                            </label>
                                            <div>
                                    <span class="m-switch m-switch--icon-check">
                                        <label>
                                            <input type="checkbox" disabled
                                                   name="isOper" <?= (int)$rs->isOper === 1 ? 'checked' : '' ?>>
                                            <span></span>
                                        </label>
                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>PO No.</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="po_no" autocomplete="off"
                                                   value="<?= $rs->po_no ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Check No.</label>
                                            <input type="text" class="form-control m-input input-auto-height"
                                                   name="check_no" autocomplete="off"
                                                   value="<?= $rs->check_no ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Date Purchased</label>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                       name="datepurchased" autocomplete="off"
                                                       value="<?= date('m/d/Y', strtotime($rs->datepurchased)) ?>" disabled>
                                                <span class="input-group-addon">
                                        <i class="la la-calendar"></i>
                                    </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: mm/dd/yyyy</div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Date Received</label>
                                            <div class="input-group date dt-picker">
                                                <input type="text" class="form-control m-input input-auto-height"
                                                       name="date_received" autocomplete="off"
                                                       value="<?= date('m/d/Y', strtotime($rs->date_received)) ?>" disabled>
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="m-form__help pt-2">Format: mm/dd/yyyy</div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Purchase Price</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control m-input input-auto-height text-right"
                                                       name="purchaseprice" autocomplete="off"
                                                       data-validation="required"
                                                       style="font-weight: bold;"
                                                       value="<?= number_format($rs->purchaseprice, 2, '.', ',') ?>"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group m-form__group">
                                            <label>Grand Total</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control m-input input-auto-height text-right"
                                                       name="total_cost" autocomplete="off" data-validation="required"
                                                       readonly
                                                       value="<?= number_format($rs->total_cost, 2, '.', ',') ?>"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END OTHER INFORMATION -->

                                <div class="mt-5 row">
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Created By</label>
                                        <input type="text" class="form-control" disabled value="<?= $rs->created_by ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Created At</label>
                                        <input type="text" class="form-control" disabled
                                               value="<?= date('F d, Y h:i:s a', strtotime($rs->created_at)) ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Updated By</label>
                                        <input type="text" class="form-control" disabled value="<?= $rs->updated_by ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="">Updated At</label>
                                        <input type="text" class="form-control" disabled
                                               value="<?= empty($rs->updated_by) ? "" : date('F d, Y h:i:s a', strtotime($rs->updated_at)) ?>"">
                                    </div>
                                </div>

                                <div class="mt-5 d-flex justify-content-end"></div>
                            </form>
                        </div>

                        <div class="tab-pane" id="tab-components" role="tabpanel" aria-expanded="false">
                            <?php $this->load->view('tabs_vehicles/tab_vehicle_components'); ?>
                        </div>

                        <div class="tab-pane" id="tab-costing" role="tabpanel" aria-expanded="false">
                            <?php $this->load->view('tabs_vehicles/tab_vehicle_costing'); ?>
                        </div>

                        <div class="tab-pane" id="tab-documents" role="tabpanel" aria-expanded="false">
                            <?php $this->load->view('tabs_vehicles/tab_documents'); ?>
                        </div>

                        <div class="tab-pane" id="tab-accountability" role="tabpanel" aria-expanded="false">
                            <?php $this->load->view('tabs_vehicles/tab_accountability'); ?>
                        </div>

                        <div class="tab-pane" id="tab-maintenance" role="tabpanel" aria-expanded="false">
                            <?php $this->load->view('tabs_vehicles/tab_maintenance'); ?>
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
                    <div class="m-portlet__head-tools"></div>
                </div>
                <div class="m-portlet__body p-0">
                    <div class="primary-image-container"
                         style="background-image: url('<?= base_url('uploads/files/images/vehicles/' . $id . '/' . $rs->primary_pic) ?>')"></div>

                    <div class="images-preview-container d-flex flex-wrap">
                        <?php foreach ($images as $row) { ?>
                            <div class="d-flex flex-column images-preview-container__wrapper isUploaded">
                                <a data-lightbox="roadtrip" data-title="<?= $row->name ?>"
                                   href="<?= base_url('uploads/files/images/vehicles/' . $rs->id . '/' . $row->name) ?>">
                                    <div class="images-preview-container__image"
                                         style="background-image: url('<?= base_url('uploads/files/images/vehicles/' . $rs->id . '/' . $row->name) ?>')">
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog"
     id="recover-modal">
    <form id="recover-form">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recover Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <label for="recover-status">STATUS</label>
                            <select name="status2" class="form-control" id="recover-status" data-validation="required">
                                <option value=""></option>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status->code ?>"><?= strtoupper($status->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <label for="inventory_check_date">Inventory Check Date</label>
                            <div class="input-group date" id="datepicker">
                                <input type="text" class="form-control m-input" name="inventory_check_date"
                                       readonly="" placeholder="Select date" id="inventory_check_date" data-validation="required">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="remarks2">Remarks</label>
                        <textarea name="status" id="remarks2" class="form-control" data-validation="required"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnUpdate">Save Changes</button>
                    <button type="button" class="btn btn-danger btnUpdate" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>