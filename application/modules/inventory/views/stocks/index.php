<?php
    $actions = $this->core_layout->getCurrentActions();
    $db = $this->is_model->getDBset();
?>

<style>
    #builder_group_0 {
        width: 100%;
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
                            <h3 class="m-portlet__head-text">
                                Inventory - Stocks
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <div class="btn-group m-btn-group" role="group">
                                    <button type="button" class="m-btn btn btn-secondary btnUpload" data-toggle="modal"
                                            data-target="#modal_query_builder">
                                        <i class="fa fa-search"></i>
                                        Query Builder
                                    </button>
                                    <button type="button" class="m-btn btn btn-secondary btnUpload" data-toggle="modal"
                                            data-target="#modal_fileupload">
                                        <i class="fa fa-upload"></i>
                                        Import
                                    </button>
                                    <button type="button" class="m-btn btn btn-secondary btnNew" data-toggle="modal"
                                            data-target="#modal-reorder_qty">
                                        <i class="fa fa-cart-plus"></i>
                                        Re-order Qty
                                    </button>
                                    <button type="button" class="m-btn btn btn-secondary btnNew" data-toggle="modal"
                                            data-target="#modal-beginning_qty">
                                        <i class="fa fa-plus"></i>
                                        Beg. Balance
                                    </button>
                                    <button type="button" class="m-btn btn btn-secondary btnNew" data-toggle="modal"
                                            data-target="#modal-archives">
                                        <i class="fa fa-archive"></i>
                                        Archives
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered" id="table-inventory_item" width="100%">
                            <thead>
                            <tr>
                                <th>Updated At</th>
                                <th>Image</th>
                                <th>Stock Code</th>
                                <th>Item Description</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Unit</th>
                                <th>Cur Balance</th>
                                <th>Beg. Balance</th>
                                <th>Re-order Qty Lvl</th>
                                <th>Min. Qty</th>
                                <th>Max. Qty</th>
                                <th>Crit. Level %</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
</div>

<div class="modal fade" id="modal-inventory_item-new" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <form action="<?php echo base_url('inventory/add_item'); ?>" method="POST" id="form-item-new"
                  enctype="multipart/form-data">
                <div class="modal-header"><h5 class="modal-title">New Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label">Stock Code</label>
                        <input type="text" name="sku" class="form-control" data-validation="required" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Item Description</label>
                        <input type="text" name="name" class="form-control" data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Unit
                        </label>
                        <select class="form-control" tabindex="-1" data-validation="required" aria-hidden="true"
                                name="unit" style="width: 100% !important;" id="unit-collection">
                            <option disabled="disabled" selected="selected" value="">Select Unit</option>
                            <?php
                                $unitCollection = $this->crud->getCollection(array(), $db.".uom");
                                if ($unitCollection):
                                    foreach ($unitCollection as $_unitCollection):
                                        ?>
                                        <option value="<?php echo $_unitCollection["id"] ?>"><?php echo $_unitCollection["uom_code"] ?></option>
                                    <?php
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Category
                        </label>
                        <select class="form-control" tabindex="-1" data-validation="required" aria-hidden="true"
                                name="category_id" style="width: 100% !important;" id="category-collection">
                            <option disabled="disabled" selected="selected" value="">Select Category</option>
                            <?php
                                $categoryCollection = $this->crud->getCollection(array("status" => 1), $db.".category");
                                if ($categoryCollection):
                                    foreach ($categoryCollection as $_categoryCollection):
                                        ?>
                                        <option value="<?php echo $_categoryCollection["id"] ?>"><?php echo $_categoryCollection["name"] ?></option>
                                    <?php
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Priority
                        </label>
                        <select class="form-control" tabindex="-1" data-validation="required" aria-hidden="true"
                                name="priority_id" style="width: 100% !important;" id="priority-collection">
                            <option disabled="disabled" selected="selected" value="">Select Category</option>
                            <?php
                                $priorityCollection = $this->crud->getCollection(array("status" => 1), $db.".priority");
                                if ($priorityCollection):
                                    foreach ($priorityCollection as $_priorityCollection):
                                        ?>
                                        <option value="<?php echo $_priorityCollection["id"] ?>"><?php echo $_priorityCollection["name"] ?></option>
                                    <?php
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Re-order Quantity Level  <span data-toggle="m-tooltip" data-original-title="The actual quantity to be purchase."><i class="la la-question-circle"></i></span></label>
                        <input type="number" id="nReorderQtyLvl" name="reorder_qty_level" class="form-control"
                               data-validation="required" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Re-order Quantity  <span data-toggle="m-tooltip" data-original-title="The quantity which triggers the notification to request for replenistment of stocks."><i class="la la-question-circle"></i></span></label>
                        <input type="number" id="nReorderQty" name="reorder_qty" class="form-control"
                               data-validation="required" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Beginning Balance Quantity</label>
                        <input type="number" id="nBeginningQty" name="beginning_qty" class="form-control"
                               data-validation="required" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" >Minimum Quantity <span data-toggle="m-tooltip" data-original-title="The quantity that sets the limit to issue stocks."><i class="la la-question-circle"></i></span></label>
                        <input type="number" name="min_qty" class="form-control"
                               data-validation="required" value="0" min="0">
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Maximum Quantity <span data-toggle="m-tooltip" data-original-title="The quantity that sets the limit to recieve stocks."><i class="la la-question-circle"></i></span></label>
                        <input type="number" name="max_qty" class="form-control"
                               data-validation="required" value="0" min="0">
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Critical Level Percentage</label>
                        <input type="number" name="critical_level_percentage" class="form-control"
                               data-validation="required" value="0" min="0">
                    </div>

                    <div class="form-group m-form__group">
                        <label>
                            Browse Image
                        </label>
                        <label class="custom-file">
                            <input type="file" id="image" name="image">
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-inventory_item-edit" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <form action="<?php echo base_url('inventory/update_items'); ?>" method="POST" id="form-item-edit"
                  enctype="multipart/form-data">
                <input type="hidden" class="inptId" name="id" v-model="item_update.id"/>
                <div class="modal-header">
                    <h5 class="modal-title">Update Item</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label">Item Description</label>
                        <input type="text" name="name" class="form-control" data-validation="required"
                               v-model="item_update.name">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Category
                        </label>
                        <select class="form-control" tabindex="-1" data-validation="required" aria-hidden="true"
                                name="category_id" style="width: 100% !important;" id="category-collection"
                                v-model="item_update.category_id">
                            <option disabled="disabled" selected="selected" value="">Select Unit</option>
                            <?php
                                $categoryCollection = $this->crud->getCollection(array("status" => 1), $db.".category");
                                if ($categoryCollection):
                                    foreach ($categoryCollection as $_categoryCollection):
                                        ?>
                                        <option value="<?php echo $_categoryCollection["id"]; ?>"><?php echo $_categoryCollection["name"]; ?></option>
                                    <?php
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Priority
                        </label>
                        <select class="form-control" tabindex="-1" data-validation="required" aria-hidden="true"
                                name="priority_id" style="width: 100% !important;" id="priority-collection"
                                v-model="item_update.priority_id">
                            <option disabled="disabled" selected="selected" value="">Select Unit</option>
                            <?php
                                $priorityCollection = $this->crud->getCollection(array("status" => 1), $db.".priority");
                                if ($priorityCollection):
                                    foreach ($priorityCollection as $_priorityCollection):
                                        ?>
                                        <option value="<?php echo $_priorityCollection["id"]; ?>"><?php echo $_priorityCollection["name"]; ?></option>
                                    <?php
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Stock Code</label>
                        <input type="text" name="sku" class="form-control" data-validation="required"
                               v-model="item_update.sku">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Unit
                        </label>
                        <select class="form-control" tabindex="-1" data-validation="required" aria-hidden="true"
                                name="unit" style="width: 100% !important;" id="unit-collection"
                                v-model="item_update.unit">
                            <option disabled="disabled" selected="selected" value="">Select Unit</option>
                            <?php
                                $unitCollection = $this->crud->getCollection(array(), $db.".uom");
                                if ($unitCollection):
                                    foreach ($unitCollection as $_unitCollection):
                                        ?>
                                        <option value="<?php echo $_unitCollection["id"]; ?>"><?php echo $_unitCollection["uom_code"]; ?></option>
                                    <?php
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Re-order Quantity Level <span data-toggle="m-tooltip" data-original-title="The actual quantity to be purchase."><i class="la la-question-circle"></i></span></label>
                        <input type="number" id="nReorderQtyLvl" name="reorder_qty_level" class="form-control"
                               data-validation="required" value="0" v-model="item_update.reorder_qty_level">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Re-order Quantity  <span data-toggle="m-tooltip" data-original-title="The quantity which triggers the notification to request for replenistment of stocks."><i class="la la-question-circle"></i></span></label>
                        <input type="number" id="nReorderQty" name="reorder_qty" class="form-control"
                               data-validation="required" value="0" v-model="item_update.reorder_qty">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Minimum Quantity <span data-toggle="m-tooltip" data-original-title="The quantity that sets the limit to issue stocks."><i class="la la-question-circle"></i></span></label>
                        <input type="number" name="min_qty" class="form-control"
                               data-validation="required" value="0" v-model="item_update.min_qty">
                    </div>

                    <!--v-if="parseToDouble(item_update.initial_max_qty) <= 0"-->
                    <div class="form-group">
                        <label class="form-control-label">Maximum Quantity <span data-toggle="m-tooltip" data-original-title="The quantity that sets the limit to recieve stocks."><i class="la la-question-circle"></i></span></label>
                        <input type="number" name="max_qty" class="form-control"
                               data-validation="required" value="0" min="0" v-model="item_update.max_qty">
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Critical Level Percentage</label>
                        <input type="number" name="critical_level_percentage" class="form-control"
                               data-validation="required" value="0" min="0"
                               v-model="item_update.critical_level_percentage">
                    </div>

					<div class="form-group">
                        <label class="form-control-label">Default as Fuel?</label>
							<label class="m-checkbox">
								<input type="checkbox" name="default_as_fuel" class="form-control" v-model="item_update.default_as_fuel">
								<span></span>
							</label>
                    </div>

                    <div class="form-group m-form__group">
                        <label>
                            Browse Image
                        </label>
                        <label class="custom-file">
                            <input type="file" id="image" name="image">
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnUpdate">Save</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-reorder_qty" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Item <small>Re-order Quantity</small></h5>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group row">
                    <label class="col-form-label col-lg-2 col-md-2 col-sm-12">Search Item</label>
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <select class="form-control m-select2" id="search_item" name="search_item"
                                aria-hidden="true"></select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12">
                        <button type="button" class="btn btn-secondary btnNew btnAddItemToList"><i
                                    class="fa fa-plus"></i></button>
                    </div>
                </div>
                <form id="frm-reorder_qty">
                    <table class="table table-striped table-bordered" id="table-reorder_qty" style="width: 100%;">
                        <col width="20%">
                        <col width="35%">
                        <col width="20%">
                        <col width="20%">
                        <col width="5%">
                        <thead>
                        <tr>
                            <th>Stock Code</th>
                            <th>Item Description</th>
                            <th>Current Re-order</th>
                            <th>Needed Re-order</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit_reorder btnUpdate">Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-beginning_qty" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Item <small>Beginning Quantity</small></h5>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group row">
                    <label class="col-form-label col-lg-2 col-md-2 col-sm-12">Search Item</label>
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <select class="form-control m-select2" id="search_item_bq" name="search_item"
                                aria-hidden="true"></select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12">
                        <button type="button" class="btn btn-secondary btnNew btnAddItemBQToList"><i
                                    class="fa fa-plus"></i></button>
                    </div>
                </div>
                <form id="frm-beginning_qty">
                    <table class="table table-striped table-bordered" id="table-beginning_qty" style="width: 100%;">
                        <col width="20%">
                        <col width="35%">
                        <col width="20%">
                        <col width="20%">
                        <col width="5%">
                        <thead>
                        <tr>
                            <th>Stock Code</th>
                            <th>Item Description</th>
                            <th>Current Beg. Balance</th>
                            <th>Needed Beg. Balance</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit_bq btnUpdate">Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-inventory_item-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <input type="hidden" id="removeItem" value="0"/>
            <div class="modal-header">
                <h5 class="modal-title">Delete Item</h5>
            </div>
            <div class="modal-body"><i class="la la-warning"></i> Are you sure you want to delete this item?</div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-delete btnDelete">Delete</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-inventory_item-archive" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <input type="hidden" id="archiveItem" value="0"/>
            <div class="modal-header">
                <h5 class="modal-title">Archive Item</h5>
            </div>
            <div class="modal-body"><i class="la la-warning"></i> Are you sure you want to archive this item?</div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-archive btnArchive">Archive</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <form name="uploadForm" id="uploadForm" action="<?php echo site_url("inventory/moveUploadedFile") ?>"
                  method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group">
                        <label>
                            File Browser
                        </label>
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnNew btn-submit">Upload</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_fileupload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
						<span class="btn btn-success fileinput-button">
							<i class="glyphicon glyphicon-plus"></i>
							<span>Select CSV File</span>
                            <!-- The file input field used as target for the file upload widget -->
							<input id="fileupload" type="file" name="files[]"/>
						</span>
                    </div>
                    <div class="col-md-8">
                        <div id="progress" class="progress" style="display: none; margin: 2% 0%;">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                    </div>
                </div>
                <!-- The global progress bar -->
                <!-- The container for the uploaded files -->
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div id="files" class="files"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- archive modal -->
<div class="modal fade" id="modal-archives" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archives</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered" id="table-inventory_item_archives" width="100%">
                    <thead>
                    <tr>
                        <th>Stock Code</th>
                        <th>Item Description</th>
                        <th>Archived By</th>
                        <th>Date Archived</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- history modal -->
<div class="modal fade" id="modal-history" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History of Transactions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered" id="table-inventory_item_history" width="100%">
                    <thead>
                    <tr>
                        <th>Stock Code</th>
                        <th>Item Description</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Qty</th>
                        <th>Running Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- query builder modal -->
<div class="modal fade" id="modal_query_builder" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Query Builder</h5>
            </div>
            <div class="modal-body">
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="form-group">
                        <label class="control-label col-md-2">Fields</label>
                        <div class="col-md-8">
                            <select id="rpt_flds" name="rpt_flds[]" data-placeholder="Select field" multiple="true"
                                    class="form-control"></select>
                        </div>
                    </div>
                </div>
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="form-group">
                        <label class="control-label col-md-2">Order By</label>
                        <div>
                            <div class="col-md-8">
                                <select id="rpt_order" name="rpt_order" data-placeholder="Select field"
                                        class="form-control"></select>
                            </div>
                            <br/>
                            <div class="col-md-4">
                                <select id="order_by" name="order_by" data-placeholder="Select field"
                                        class="form-control">
                                    <option value="ASC">Ascending</option>
                                    <option value="DESC">Descending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <label class="control-label col-md-2">Filter</label>
                    <div id="builder" class="col-lg-12"></div>
                </div>
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="col-lg-12 m--align-right">
                        <div class="btn-group">
                            <button class="btn btn-success" id="generate"><i class="fa fa-search"></i> Generate</button>
                            <button class="btn btn-default"><i class="fa fa-refresh"></i> Reset</button>
                        </div>
                    </div>
                </div>
                <input id="query" name="query" type="hidden" class="form-control" readonly>
                <hr>
                <div id="resTable">

                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view("stocks/modals/pi_history_modal") ?>
<?php $this->load->view("stocks/modals/transaction_details_for_pi_modal") ?>
<?php $this->load->view("stocks/modals/transaction_listing_for_pi_modal") ?>
<?php $this->load->view("stocks/modals/template_modal") ?>

<script type="text/javascript" src="<?= base_url('assets/js/inventory/items/pi_history.script.js') ?>"></script>

<script type="text/javascript">
    var history_id = 0;
    var _currentActions = <?php echo json_encode($actions); ?>;
    var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
    var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";

    var _tableItem = $("#table-inventory_item");
    var _tableReorderQty = $("#table-reorder_qty");
    var _tableBeginningQty = $("#table-beginning_qty");

    var _modalItem = $("#modal-inventory_item-new");
    var _modalItemEdit = $("#modal-inventory_item-edit");
    var _modalItemDelete = $("#modal-inventory_item-delete");
    var _modalItemArchive = $("#modal-inventory_item-archive");

    $('body').tooltip({
        selector: '[data-toggle="m-tooltip"]'
    });

    var _dtReorderTable = $("#table-reorder_qty").DataTable({
        dom: "t",
        ordering: false,
        columns: [
            {data: "sku"},
            {data: "name"},
            {data: "current_qty", className: "text-center"},
            {data: "needed_qty", className: "text-center"},
            {data: "action", className: "text-center"},
        ]
    });

    var _dtBeginningTable = $("#table-beginning_qty").DataTable({
        dom: "t",
        ordering: false,
        columns: [
            {data: "sku"},
            {data: "name"},
            {data: "current_qty", className: "text-center"},
            {data: "needed_qty", className: "text-center"},
            {data: "action", className: "text-center"},
        ]
    });

    var _dtArchivedTable = $("#table-inventory_item_archives").DataTable({
        dom: "frtlp",
        destroy: true,
        serverSide: true,
        processing: true,
        autoWidth: false,
        ajax: {
            url: "<?php echo base_url("inventory/get_archive_list"); ?>",
            type: "post",
            dataType: "json",
            data: {_csrf_token: _csrf_hash, limit: "All"},
            global: false
        }, columns: [
            {data: "sku", width: "10%"},
            {data: "name"},
            {data: "archived_by", width: "20%"},
            {data: "archived_date", width: "20%"},
            {
                data: null,
                width: "8%",
                className: "text-center",
                orderable: false,
                render: function (data, type, row) {
                    if (_actions.includes("btnRestore")) {
                        return `<button class="btn btn-sm btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-accent"
                                        data-toggle="m-tooltip" data-skin="dark" data-original-title="Restore"
                                        onclick='confirmRestore(${row.id}, "${row.sku}")'>
                                    <i class="la la-reply"></i>
                                </button>`;
                    }

                    return `<button class="btn btn-sm btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill" disabled>
                                    <i class="la la-reply"></i>
                                </button>`;
                }
            }
            /*** { data: "remarks"}, ***/
        ]
    });

    function confirmRestore(id, sku) {
        const tmpModal = $("#template-modal");
        $(".modal-title", tmpModal).html("Confirm Restore");
        $(".modal-body", tmpModal).empty().append(`<p style="font-size: 14px;">ARE YOU SURE TO RESTORE ITEM WITH SKU: <span>${sku}</span>?</p>`);
        $(".confirm", tmpModal).html("Yes");
        $(".cancel", tmpModal).html("No");
        $("#frm-template", tmpModal).removeAttr("data-view");
        $("#frm-template", tmpModal).attr("action", base_url + 'inventory/restore_item/' + id);
        tmpModal.modal("show");
    }

    $("#frm-template")
        .on("submit", function (e) {
            e.preventDefault();
            const url = $(this).attr("action");
            const view = $(this).attr("data-view");

            $.ajax({
                url,
                data: $(this).serialize(),
                type: "POST",
                dataType: "JSON",
                success: function (response) {
                    const tmpModal = $("#template-modal");
                    if (response) {
                        $("#modal-inventory_item-new").modal("hide");
                        _dtArchivedTable.ajax.reload();

                        $("form", tmpModal).attr("action", "");
                        tmpModal.modal("hide");
                        _dtTable.ajax.reload();
                        toastr.success("Item was successfully restored.", "Item Restored.", {timeOut: 10000});
                    } else {
                        toastr.error("An error occurred.", "Restore Error.", {timeOut: 10000});
                    }
                }
            });
        });

    var _dtHistoryTable = $("#table-inventory_item_history").DataTable({
        dom: "rtlp",
        serverSide: true,
        processing: true,
		ordering: false,
        ajax: {
            url: "<?php echo base_url("inventory/get_history_list"); ?>",
            type: "post",
            dataType: "json",
            data: function (d) {
                d.id = history_id;
            }
        }, columns: [
            {data: "sku"},
            {data: "name"},
            {data: "transaction_date"},
            {data: "reference_no"},
            {data: "qty"},
            {data: "running_balance"},
        ],
        pageLength: 0,
        paging: false,
    });

    var _dtTable = $("#table-inventory_item").DataTable({
        dom: '<"toolbar">frtlip',
        destroy: true,
        serverSide: true,
        processing: true,
        ajax: {
            url: "<?php echo base_url("inventory/get_item_list"); ?>",
            type: "post",
            dataType: "json",
            data: {_csrf_token: _csrf_hash, limit: "All"},
            global: false,
        }, columns: [
            {data: "updated_at", visible: false},
            {data: "image", orderable: false},
            {data: "sku"},
            {data: "name"},
            {data: "category"},
            {data: "priority"},
            {data: "uom_code"},
            {data: "qty"},
            {data: "beginning_qty"},
            {data: "reorder_qty_level"},
            {data: "min_qty"},
            {data: "max_qty"},
            {data: "critical_level_percentage"},
            {data: null, className: "text-center"},
        ], columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            },
            width: "8%"
        }, {
            targets: "_all",
            defaultContent: "",
        }],
        order: [[0, "desc"]],
        initComplete: function (settings, json) {
            if (typeof roleActionUpdate == "function") {
                roleActionUpdate();
            }


        }
    });

    $("div.toolbar").html('<button type="button" id="inventory_item-new" class="m-portlet__nav-link btn m-btn--square btn-success btnNew" data-toggle="modal" data-target="#modal-inventory_item-new"><i class="fa fa-plus"></i> New </button>');

    function itemDatatableActions($id) {
        if ($id) {
            var _actionButton = "";
            if (typeof _currentActions !== "undefined" && jQuery.inArray("edit", _currentActions) !== -1) {
                _actionButton += " " +
                    "<button type='button' " +
                    "        data-toggle='m-tooltip'" +
                    "        data-original-title='Edit'" +
                    "        data-skin='dark'" +
                    "        class='btn btn-default btn-sm m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
                    "        data-id='" + $id + "'>" +
                    "           <i class='la la-edit'></i>" +
                    "</button>";
            }
            if (typeof _currentActions !== "undefined" && jQuery.inArray("delete", _currentActions) !== -1) {
                _actionButton += " " +
                    " <button type='button' " +
                    "         data-toggle='m-tooltip'" +
                    "         data-original-title='Delete'" +
                    "         data-skin='dark'" +
                    "         class='btn btn-default btn-sm m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemoveItem' " +
                    "         data-id='" + $id + "'>" +
                    "           <i class='la la-trash'></i>" +
                    "</button>";
            }
            if (typeof _currentActions !== "undefined" && jQuery.inArray("archive", _currentActions) !== -1) {
                _actionButton += " " +
                    " <button type='button' " +
                    "         data-toggle='m-tooltip'" +
                    "         data-original-title='Archive'" +
                    "         data-skin='dark'" +
                    "         class='btn btn-default btn-sm m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnArchiveItem' " +
                    "         data-id='" + $id + "'>" +
                    "           <i class='la la-archive'></i>" +
                    "</button>";
            }
            if (typeof _currentActions !== "undefined" && jQuery.inArray("history", _currentActions) !== -1) {
                _actionButton += " " +
                    " <button type='button' " +
                    "         data-toggle='m-tooltip'" +
                    "         data-original-title='Transaction History'" +
                    "         data-skin='dark'" +
                    "         class='btn btn-default btn-sm m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnHistoryItem' " +
                    "         data-id='" + $id + "'>" +
                    "           <i class='la la-bar-chart'></i>" +
                    "</button>";
            }
            if (typeof _currentActions !== "undefined" && jQuery.inArray("history", _currentActions) !== -1) {
                _actionButton += " " +
                    " <button type='button' " +
                    "         data-toggle='m-tooltip'" +
                    "         data-original-title='Physical Inventory History'" +
                    "         data-skin='dark'" +
                    "         onclick='openPiHistory(" + $id + ")'" +
                    "         class='btn btn-default btn-sm m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnPhysicalInventoryHistory' " +
                    "         data-id='" + $id + "'>" +
                    "           <i class='fa fa-dropbox'></i>" +
                    "</button>";
            }

            return _actionButton;
        } else {
            return false;
        }
    }

    $.validate({
        form: '#form-item-new',
        lang: 'en',
        onSuccess: function (form) {
            const sku = $("[name='sku']", $("#form-item-new")).val();
            checkDuplicateSku(sku)
                .then((response) => {
                    if (response) {
                        const isArchived = parseInt(response.status) === 0;
                        const msg = isArchived ? `<p class="m--font-bolder" style="font-size: 16px;">AN ITEM WITH SKU:
                                                <span class="m--font-boldest">${sku}</span>
                                                    ALREADY EXISTS BUT CURRENTLY IN<span class="m--font-boldest"> ARCHIVE.
                                                </span> WOULD YOU LIKE TO RESTORE IT?
                                              </p>` :
                            `<p class="m--font-bolder" style="font-size: 16px;">AN ITEM WITH SKU:
                            <span class="m--font-boldest">${sku}</span> ALREADY EXISTS.</p>`;
                        const tmpModal = $("#template-modal");
                        $(".modal-title", tmpModal).html(`DUPLICATED SKU`);
                        $(".modal-body", tmpModal).html(msg);

                        if (isArchived) {
                            $("#frm-template", tmpModal).attr("action", base_url + 'inventory/restore_item/' + response.id);
                            $("#frm-template", tmpModal).attr("data-view", "new-item");
                            $(".confirm", tmpModal).html(`YES`).show();
                            $(".cancel", tmpModal).html(`NO`);
                        } else {
                            $("#frm-template", tmpModal).removeAttr("action");
                            $(".confirm", tmpModal).hide();
                            $(".cancel", tmpModal).html(`OKAY, GOT IT.`);
                        }
                        tmpModal.modal("show");
                    } else {
                        var Files = new FormData(form[0]);
                        $.ajax({
                            url: form[0].action,
                            type: "POST",
                            data: Files,
                            async: false,
                            contentType: false,
                            cache: false,
                            processData: false,
                            beforeSend: function () {
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function (data) {
                                if (data.response) {
                                    form[0].reset();
                                    toastr.success(data.toastr_msg, "Added inventory item", 5000);
                                    //$("#modal-item-new").modal("hide");
                                    $("#unit-collection").val('').trigger('change');
                                    $("#category-collection").val('').trigger('change');
                                    $("#priority-collection").val('').trigger('change');
                                    $("#form-item-new").trigger("reset");
                                    setTimeout(function () {
                                        _dtTable.ajax.reload();
                                    }, 500);
                                } else {
                                    toastr.error(data.toastr_msg, "Error inventory item", 5000);
                                }
                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");

                            }
                        });
                    }
                });

            return false;
        },
    });

    //init validate upload form
    $.validate({
        form: '#uploadForm',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#uploadForm").find("file").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.response) {
                        toastr.success(data.toastr_msg, "Added inventory item", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Error inventory item", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");

                }
            });
            return false;
        },
    });

    jQuery(document).on("click", ".btn-submit_reorder", function () {
        var _formData = $("#frm-reorder_qty").serialize();

        $.ajax({
            url: "<?php echo site_url("inventory/set_reorder_items"); ?>",
            type: "post",
            dataType: "json",
            data: _formData,
            success: function (json) {
                if (json.response) {
                    toastr.success("Re-order item(s) has been updated.", "Re-order Quantity");
                    _dtReorderTable.clear();
                    _dtReorderTable.draw();

                    $("#modal-reorder_qty").modal("hide");
                    $("#search_item").val("").trigger("change");
                    setTimeout(function () {
                        _dtTable.ajax.reload();
                    }, 500);
                } else {
                    toastr.error("Failed to update re-ordered item(s)!", "Re-order Quantity");
                }
            }
        });
    });

    jQuery(_tableBeginningQty, '#begin_bal').on("keypress", function () {
        return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57);
    });

    jQuery(document).on("click", ".btn-submit_bq", function () {
        var _formData = $("#frm-beginning_qty").serialize();

        $.ajax({
            url: "<?php echo site_url("inventory/set_beginning_items"); ?>",
            type: "post",
            dataType: "json",
            data: _formData,
            success: function (json) {
                if (json.response) {
                    toastr.success("Beginning item(s) quantity has been updated.", "Beginning Quantity");
                    _dtBeginningTable.clear();
                    _dtBeginningTable.draw();

                    $("#modal-beginning_qty").modal("hide");
                    $("#search_item_bq").val("").trigger("change");
                    setTimeout(function () {
                        _dtTable.ajax.reload();
                    }, 500);
                } else {
                    toastr.error("Failed to update beginning item(s) quantity!", "Beginning Quantity");
                }
            }
        });
    });

    jQuery(document).on("click", ".btnEditItem", function () {
        var _dataId = $(this).data("id");

        $.ajax({
            url: "<?php echo site_url("inventory/get_inventory_item"); ?>",
            type: "post",
            dataType: "json",
            data: {id: _dataId},
            success: function (json) {
                if (json.response) {
                    vm.item_update = json.value;
                    _modalItemEdit.modal("show");
                } else {
                    toastr.error(json.toastr_msg, "Error inventory item", 5000);
                }
            }

        });
    });

    jQuery(document).on("click", "#table-inventory_item .btnRemoveItem", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var _inptAcl = _modalItemDelete.find("#removeItem");
        if (typeof _inptAcl !== "undefined") {
            _inptAcl.val(dataId);
            $(_modalItemDelete).modal("show");
        }
    });

    jQuery(document).on("click", "#table-inventory_item .btnArchiveItem", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var _inptAcl = _modalItemArchive.find("#archiveItem");
        if (typeof _inptAcl !== "undefined") {
            _inptAcl.val(dataId);
            $(_modalItemArchive).modal("show");
        }
    });

    jQuery(document).on("click", ".btn-submit-delete", function () {
        var _btnSubmit = jQuery(this);
        var _dataId = jQuery(this).parent(".modal-footer").parent(".modal-content").children("input#removeItem").val();
        if (typeof _dataId !== "undefined") {
            jQuery.ajax({
                url: "<?php echo base_url("inventory/remove_item"); ?>",
                type: "post",
                data: {id: _dataId},
                beforeSend: function () {
                    if (typeof _btnSubmit !== "undefined") {
                        if (!_btnSubmit.hasClass("m-btn--custom m-loader m-loader--light m-loader--right")) {
                            _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        }
                    }
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Remove iventory item", 5000);
                        $(_modalItemDelete).modal("hide");
                        setTimeout(function () {
                            _dtTable.ajax.reload();
                        }, 500);
                    } else {
                        toastr.error(json.toastr_msg, "Error removing inventory item", 5000);
                    }
                    if (typeof _btnSubmit !== "undefined") {
                        if (_btnSubmit.hasClass("m-btn--custom m-loader m-loader--light m-loader--right")) {
                            _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        }
                    }
                }
            });
        }
    });

    jQuery(document).on("click", ".btn-submit-archive", function () {
        var _btnSubmit = jQuery(this);
        var _dataId = jQuery(this).parent(".modal-footer").parent(".modal-content").children("input#archiveItem").val();
        if (typeof _dataId !== "undefined") {
            jQuery.ajax({
                url: "<?php echo base_url("inventory/archive_item"); ?>",
                type: "post",
                data: {id: _dataId},
                beforeSend: function () {
                    if (typeof _btnSubmit !== "undefined") {
                        if (!_btnSubmit.hasClass("m-btn--custom m-loader m-loader--light m-loader--right")) {
                            _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        }
                    }
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Remove iventory item", 5000);
                        $(_modalItemArchive).modal("hide");
                        setTimeout(function () {
                            _dtArchivedTable.ajax.reload();
                        }, 500);
                    } else {
                        toastr.error(json.toastr_msg, "Error archiving inventory item", 5000);
                    }
                    if (typeof _btnSubmit !== "undefined") {
                        if (_btnSubmit.hasClass("m-btn--custom m-loader m-loader--light m-loader--right")) {
                            _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        }
                    }
                }
            });
        }
    });

    $("#inventory_item-new").on("click", function () {
        $("#modal-inventory_item-new").modal("show");
    });

    var _items = {id: 0, name: "", sku: "", unit: 0};
    var vm = new Vue({
        el: "#form-item-edit",
        data: {item_update: _items},
        methods: {
            parseToDouble(value) {
                return parseFloat(value);
            }
        },
        mounted: function () {
            $.validate({
                form: '#form-item-edit',
                lang: 'en',
                onSuccess: function (form) {
                    var _url = form[0].action;
                    var _data = jQuery(form[0]).serialize();
                    var _btnSubmit = $(form[0]).find(".btn-submit");

                    var Files = new FormData(form[0]);
                    $.ajax({
                        url: _url,
                        type: "POST",
                        data: Files,
                        async: false,
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function () {
                            if (typeof _btnSubmit !== "undefined") {
                                _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        },
                        success: function (data) {
                            if (data.response) {
                                toastr.success(data.toastr_msg, "Update iventory item", 5000);
                                _modalItemEdit.modal("hide");
                                setTimeout(function () {
                                    _dtTable.ajax.reload();
                                }, 500);
                            } else {
                                toastr.error(data.toastr_msg, "Error iventory item", 5000);
                            }
                            if (typeof _btnSubmit !== "undefined") {
                                _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        }
                    });

                    return false;
                }
            });
        }
    });

    jQuery(_tableReorderQty, "tbody").on("click", ".btnRemoveRow", function () {
        var _self = $(this);
        _dtReorderTable.row(_self.parents('tr')).remove().draw();
    });

    jQuery(_tableBeginningQty, "tbody").on("click", ".btnRemoveRow", function () {
        var _self = $(this);
        _dtBeginningTable.row(_self.parents('tr')).remove().draw();
    });

    jQuery(document).on("click", ".btnAddItemBQToList", function () {
        var itemId = $("#search_item_bq").val();
        $.ajax({
            url: "<?php echo site_url("inventory/get_searched_item_bq"); ?>",
            type: "post",
            dataType: "json",
            data: {id: itemId},
            success: function (json) {
                if (json.data) {
                    var _draw = true;
                    var _rowData = json.data;
                    var _dtData = _dtBeginningTable.data();

                    jQuery.each(_dtData, function (i, v) {
                        if (v.id == _rowData.id) {
                            _draw = false;
                        }
                    });

                    if (_draw == true) {
                        _dtBeginningTable.row.add(json.data).draw();
                        $("#search_item_bq").val("").trigger("change");
                    }
                    if (_draw == false) {
                        toastr.error("Selected item already on the list!", "Beginning Quantity");
                    }
                }
            }
        });
    });

    jQuery(document).on("click", ".btnAddItemToList", function () {
        var itemId = $("#search_item").val();
        $.ajax({
            url: "<?php echo site_url("inventory/get_searched_item"); ?>",
            type: "post",
            dataType: "json",
            data: {id: itemId},
            success: function (json) {
                if (json.data) {
                    var _draw = true;
                    var _rowData = json.data;
                    var _dtData = _dtReorderTable.data();

                    jQuery.each(_dtData, function (i, v) {
                        if (v.id == _rowData.id) {
                            _draw = false;
                        }
                    });

                    if (_draw == true) {
                        _dtReorderTable.row.add(json.data).draw();
                        $("#search_item").val("").trigger("change");
                    }
                    if (_draw == false) {
                        toastr.error("Selected item already on the list!", "Re-order Quantity");
                    }
                }
            }
        });
    });

    $('#unit-collection').select2();
    $('#category-collection').select2();
    $('#priority-collection').select2();

    $(document).on("click", ".btnHistoryItem", function () {
        $("#modal-history").modal("show");

        var id = $(this).attr("data-id");
        history_id = id;
        _dtHistoryTable.ajax.reload();

    });

    $(function () {
        'use strict';
        // Change this to the location of your server-side upload handler:
        var url = "<?php echo site_url("core/upload/excel_files"); ?>";
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var a_count = result.added_count;
                    var e_count = result.existing_count;

                    $.ajax({
                        url: "<?php echo site_url("core/upload/get_loaded_files"); ?>" + "/" + result.filename,
                        dataType: "json",
                        success: function (json) {
                            console.log(json);
                        }
                    });
                    $.each(result.files, function (index, file) {
                        var uploadContent = "<p class='custom-row'><span>" + file.client_name + "</span>";
                        if (typeof e_count !== "undefined") {
                            uploadContent += "<span class='m-menu__link-badge pull-right' style='margin-left: 5px;'><span class='m-badge m-badge--danger m-badge--wide'>Existing Items Total " + e_count + "</span></span>";
                        }
                        if (typeof a_count !== "undefined") {
                            uploadContent += "<span class='m-menu__link-badge pull-right' style='margin-left: 5px;'><span class='m-badge m-badge--info m-badge--wide'>Added Items Total " + a_count + "</span></span>";
                        }

                        uploadContent += '<span class="m-menu__link-badge pull-right"><span class="m-badge m-badge--success m-badge--wide">uploaded</span></span></p>';


                        $('<div/>').addClass("uploaded-file").html(uploadContent).appendTo('#files');
                    });
                    toastr.success(result.message);
                } else {
                    toastr.error(result.message);
                }

            },
            progressall: function (e, data) {
                $("#progress").show();
                var progress = parseInt(data.loaded / data.total * 100, 10);
                var progressTotal = 0;

                var steps = setInterval(function () {
                    progressTotal += 10;
                    $('#progress .progress-bar').css('width', progressTotal + '%');
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $('#progress .progress-bar').css('width', progressTotal + '%');
                        }, 1500);
                    }
                }, 10);

                if (progress == 100) {
                    setTimeout(function () {
                        $("#progress").hide();
                    }, 1000);
                }
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });

    // query builder start
    function init_builder() {
        var rules_basic = {
            condition: 'AND',
            rules: [{"id": "sku"}]
        };

        $('#builder').queryBuilder({
            'bt-tooltip-errors': {delay: 100},

            filters: [
                {id: 'sku', label: 'Stock Code', type: 'string'},
                {id: 'name', label: 'Item Description', type: 'string'},
                {id: 'qty', label: 'Quantity', type: 'integer'},
                {
                    id: 'unit',
                    label: 'Uom',
                    type: 'integer',
                    input: 'select',
                    values: <?php $this->items_model->uomList(); ?>,
                    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
                },
                {
                    id: 'category_id',
                    label: 'Category',
                    type: 'integer',
                    input: 'select',
                    values: <?php $this->items_model->categoryList(); ?>,
                    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null'],
                    plugin: 'select2',
                    plugin_config: {dropdownParent: $("#modal_query_builder")}
                },
                {
                    id: 'priority_id',
                    label: 'Priority',
                    type: 'integer',
                    input: 'select',
                    values: <?php $this->items_model->priorityList(); ?>,
                    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null'],
                    plugin: 'select2',
                    plugin_config: {dropdownParent: $("#modal_query_builder")}
                },
                {id: 'beginning_qty', label: 'Beg. Balance', type: 'integer'},
                {id: 'reorder_qty', label: 'Re-Order Qty', type: 'integer'},
                {
                    id: 'status',
                    label: 'Status',
                    type: 'integer',
                    input: 'select',
                    values: {0: "Cancelled", 1: "Active"},
                    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
                },
            ],

            rules: rules_basic
        });

        $('#btn-reset').on('click', function () {
            $('#builder').queryBuilder('reset');
        });

        $('#btn-set').on('click', function () {
            $('#builder').queryBuilder('setRules', rules_basic);
        });

        $('#btn-get').on('click', function () {
            var result = $('#builder').queryBuilder('getRules');

            if (!$.isEmptyObject(result)) {
                alert(JSON.stringify(result, null, 2));
            }
        });


    }

    function generate() {
        if ($('#table_fields tr').length == 1) {
            alert('Add some Fields first!');
        } else {
            var tbl_hdr_val = [];
            var tbl_hdr_text = [];
            $('[name="td_val[]"]').each(function () {
                tbl_hdr_val.push($(this).text());
            });
            $('[name="td_text[]"]').each(function () {
                tbl_hdr_text.push($(this).text());
            });
            list(tbl_hdr_val, tbl_hdr_text);
        }
    }

    jQuery(document).ready(function () {
        $('#search_item_bq').select2({
            width: "100%",
            placeholder: "Search Item",
            ajax: {
                url: '<?php echo site_url("inventory/select2_get_current_items"); ?>',
                dataType: "json",
                type: "post",
            },
        });
        $('#search_item').select2({
            width: "100%",
            placeholder: "Search Item",
            ajax: {
                url: '<?php echo site_url("inventory/select2_get_current_items"); ?>',
                dataType: "json",
                type: "post",
            },
        });

        $('[name="rpt_flds[]"]').select2({width: "100%", data: dataorder});
        $('[name="rpt_order"]').select2({width: "100%", data: dataorder});
        $('#rpt_flds').select2('val', ['items.name,items.sku,items.qty,items.unit,items.category_id,items.priority_id,items.beginning_qty,items.reorder_qty,items.created_by,items.updated_by,items.created_at,items.updated_at,items.status']);
        init_builder();
    });

    var dataorder = [
        {"id": "", "text": ""},
        {"id": "sku", "text": "Stock Code"},
        {"id": "name", "text": "Stock Description"},
        {"id": "qty", "text": "Qty"},
        {"id": "unit", "text": "Uom"},
        {"id": "category_id", "text": "Category"},
        {"id": "priority_id", "text": "Priority"},
        {"id": "beginning_qty", "text": "Beg. Qty"},
        {"id": "reorder_qty", "text": "Re-order Qty"},
        {"id": "status", "text": "Status"},
    ];

    var initResTable = '<table class="table table-striped table-bordered" id="table-inventory_item_qb" width="100%"><thead><tr></tr></thead><tbody></tbody></table>';

    $("#generate").on("click", function () {
        var resDataCol = [];
        $("#table-inventory_item_qb").DataTable({destroy: true});
        //$('#table-inventory_item_qb').empty();
        $('#resTable').empty();
        $("#resTable").append(initResTable);
        var flds = $('#rpt_flds').select2('data');
        var orderbyfld = $('#rpt_order').select2('data');
        var selectedflds = "";
        var order_by = "";

        $.each(flds, function (k, v) {
            selectedflds += v.id + ", ";
            $("#table-inventory_item_qb thead tr").append("<th>" + v.text + "</th>");
            resDataCol.push({data: v.id});
        });
        var filters = $('#builder').queryBuilder('getSQL', false);
        order_by = $("select[name=order_by]").val();
        $.ajax({
            url: "<?php echo site_url('inventory/generate_stocks_report')?>",
            type: "POST",
            data: {selectedflds: selectedflds, filters: filters.sql, order_by: order_by, orderbyfld: orderbyfld[0].id},
            beforeSend: function () {
                $("#generate").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                $("#generate").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                $("#table-inventory_item_qb").DataTable({
                    dom: 'Bfrtlip',
                    lengthChange: true,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    buttons: ["pdfHtml5", "excelHtml5"],
                    data: data.data,
                    columns: resDataCol,

                });

            }
        });
    });

    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    $(document).on('hidden.bs.modal', '.modal', function () {
        $('.modal:visible').length && $(document.body).addClass('modal-open');
    });

    $("#form-item-new [name='sku']")
        .on("blur", function () {
            const sku = $(this).val();
            checkDuplicateSku(sku)
                .then((response) => {
                    if (response) {
                        const isArchived = parseInt(response.status) === 0;
                        const msg = isArchived ? `<p class="m--font-bolder" style="font-size: 16px;">AN ITEM WITH SKU:
                                                <span class="m--font-boldest">${sku}</span>
                                                    ALREADY EXISTS BUT CURRENTLY IN<span class="m--font-boldest"> ARCHIVE.
                                                </span> WOULD YOU LIKE TO RESTORE IT?
                                              </p>` :
                            `<p class="m--font-bolder" style="font-size: 16px;">AN ITEM WITH SKU:
                            <span class="m--font-boldest">${sku}</span> ALREADY EXISTS.</p>`;
                        const tmpModal = $("#template-modal");
                        $(".modal-title", tmpModal).html(`DUPLICATED SKU`);
                        $(".modal-body", tmpModal).html(msg);

                        if (isArchived) {
                            $("#frm-template", tmpModal).attr("action", base_url + 'inventory/restore_item/' + response.id);
                            $("#frm-template", tmpModal).attr("data-view", "new-item");
                            $(".confirm", tmpModal).html(`YES`).show();
                            $(".cancel", tmpModal).html(`NO`);
                        } else {
                            $("#frm-template", tmpModal).removeAttr("action");
                            $(".confirm", tmpModal).hide();
                            $(".cancel", tmpModal).html(`OKAY, GOT IT.`);
                        }
                        tmpModal.modal("show");
                    }
                });
        });

    async function checkDuplicateSku(sku) {
        const result = await $.ajax({
            global: false,
            url: `${base_url + 'inventory/check_duplicate_sku'}`,
            type: "POST",
            data: {sku},
            dataType: "JSON"
        });

        return result;
    }
</script>
