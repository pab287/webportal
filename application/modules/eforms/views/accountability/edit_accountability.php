<style>
    @media screen and (max-width: 690px){
        #fix-mobile{
            display: flex;
            flex-wrap: wrap;
        }

        #fix-mobile button, #fix-mobile a{
            flex: 1 1 22%;
            max-width: 100%;
        }

        #fix-mobile button, #fix-mobile a{
            margin-bottom: 10px;
        }

        span.help-block.form-error{
            width: 100% !important;
        }
    }

    @media screen and (max-width: 575px){
        #toogle_but{
            margin-top: 10px;
        }
    }

    @media screen and (max-width: 480px){
        #fix-mobile button, #fix-mobile a{
            flex: 0 0 100%;
            max-width: 100%;
        }
        .pull-right{
            float: unset;
            margin: 0 15px;
        }
    }

    span.help-block.form-error{
        width: 32.5% !important;
    }
</style>

<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="view_accountability?id=<?php echo $_GET['id']; ?>"
                                   title="Back to Viewing"
                                   class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">Accountability Form Detail</h3>
                        </div>
                    </div>
                </div>
                <form id="frm_status_new">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-md-7">
                                <input id="company_to" name="company_to" placeholder="" class="form-control"
                                       type="hidden" readonly>
                                <input id="department_to" name="department_to" placeholder="" class="form-control"
                                       type="hidden" readonly>
                                <input id="contract_check" name="contract_check" placeholder="" class="form-control"
                                       value='0' type="hidden" readonly>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-12 col-form-label">
                                        Issued to
                                    </label>
                                    <div class="col-md-7 col-lg-7 col-sm-12">
                                        <select id="issued_to" name="issued_to" data-validation="required"></select>
                                        <!-- <select disabled id="contractor" name="contractor"></select> -->
                                    </div>
                                    <div class="col-md-3 col-lg-3 col-sm-12" id='toogle_but'>
                                        <button id='con_but' type='button' class="btn m-btn--sm btn-primary btn-block btnEdit">Change to Contractor</button>
                                        <button id='emp_but' type='button' style='margin-top:0px' class="btn m-btn--sm btn-success btn-block btnEdit">Change to Employee</button>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-12 col-form-label">Date Issued</label>
                                    <div class="col-md-3 col-lg-3 col-sm-3 col-xs-12 input-group date" id="issue_dtpicker">
                                        <input class="form-control m-input" type="text" name="issue_dt" id="issue_dt" maxlength="22" autocomplete="off" data-validation="required" />
                                        <span class="input-group-addon"><i class="la la-calendar glyphicon-th"></i></span>
                                    </div>
                                    <div class='col-md-3 col-lg-3 col-sm-3 col-xs-12 m-checkbox-list'>
                                        <label class='m-checkbox m-checkbox--success m--margin-top-5 m--font-boldest pull-right'>
                                            <input class="form-control m-input" name='urgent' id="urgent" type='checkbox' />
                                            <strong class="m--font-danger">URGENT</strong>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-12 col-form-label">
                                        Contents
                                    </label>
                                    <div  id="fix-mobile" class="col-md-10 col-lg-10 col-sm-12 text-right">
                                        <a class="btn m-btn--sm btn-warning btnNew text-white" data-toggle='modal'
                                           data-target='#add_asset_modal'>
                                                <span>
                                                    <i class="la la-plus"></i>            
                                                    <span>                             
                                                        Asset    
                                                    </span>
                                                </span>
                                        </a>
                                        <a class="btn m-btn--sm btn-info btnNew text-white" data-toggle='modal'
                                           data-target='#add_vehicle_modal'>
                                                <span>
                                                    <i class="la la-plus"></i>            
                                                    <span>                             
                                                        Vehicle    
                                                    </span>
                                                </span>
                                        </a>
                                        <a class="btn m-btn--sm btn-success btnNew text-white" data-toggle='modal'
                                           data-target='#add_multiple_modal'>
                                                <span>
                                                    <i class="la la-plus"></i>            
                                                    <span>                             
                                                        Multiple assets   
                                                    </span>
                                                </span>
                                        </a>
                                        <button type="button" class="btn m-btn--sm btn-danger btnNew"
                                                data-toggle='modal' data-target='#clear_asset_modal' id="clear_temp">
                                            <i class="la la-trash"></i>
                                            Clear
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <div class="col-12">
                                        <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                            <table class="table table-striped table-bordered" name="tblcontent"
                                                   id="tbltemp" width="100%">
                                                <thead>
                                                <tr>
                                                    <th>Asset Code</th>
                                                    <th>Description</th>
                                                    <th>Remarks</th>
                                                    <th>(₱) Amount</th>
                                                    <th>Action</th>
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
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit" class="btn btn-submit btn-accent m-btn m-btn--custom btnNew">
                                <span><i class='fa fa-save'></i> </span>
                                Save
                            </button>
                            <a href="<?php echo base_url("eforms/accountability/masterfile"); ?>">
                                <button type="button"
                                        class="btn m-btn m-btn--custom btn-metal text-white m-btn--air btnNew">
                                        <span>
                                            Close
                                        </span>
                                </button>
                            </a>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>
    <!--asset modal-->
    <div class="modal fade" id="add_asset_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         style="display: block;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Add Asset
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                ×
                            </span>
                    </button>
                </div>
                <form id="add_asset_form" name="add_item_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="col-12 modal-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">
                                Asset Code:
                            </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12 input-group">
                                <input type='text' class="form-control" name='asset' id='asset' autocomplete="off" />
                                <button id='asset_search' class='btn m-btn--sm btn-success input-group-addon btnAdvance_search' type='button'>
                                    <span><i class="la la-search"></i></span>
                                </button>
                            </div>
                        </div>
                        <br>
                        <div class="form-group m-form__group row">
                            <div class="col-12">
                                <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" name="tblnewasset"
                                           id="tblnewasset" width="100%">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Asset Code</th>
                                            <th>Asset Name</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <input type="hidden" name="asset_id" id="asset_id" class="form-control"/>
                        <input type="hidden" name="asset_cost" id="asset_cost" class="form-control"/>
                        <input type="hidden" name="asset_qty" id="asset_qty" class="form-control" value="1"/>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label form-control-label">Asset Code: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <input type="text" name="asset_code" id="asset_code" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Asset Name: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="asset_name" rows="3" id="asset_name" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Description: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="asset_desc" rows="4" id="asset_desc" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Remarks: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="asset_remarks" rows="4" id="asset_remarks"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group m-form__group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Components: </label>
                            <div class="col-12">
                                <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" name="tblassetcomp" id="tblassetcomp" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Asset Code</th>
                                                <th>Asset Name</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                        <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--asset modal-->
    <!--vehicle modal-->
    <div class="modal fade" id="add_vehicle_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         style="display: block;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="add_vehicle_form" name="add_vehicle_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="col-12 modal-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">
                                Asset Code:
                            </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12 input-group">
                                <input type='text' class="form-control" name='vehicle' id='vehicle' autocomplete="off" />
                                <button id='vehicle_search' class='btn m-btn--sm btn-success input-group-addon btnAdvance_search'
                                        type='button'>
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                </button>
                            </div>
                        </div>
                        <br>
                        <div class="form-group m-form__group row">
                            <div class="col-12">
                                <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" name="tblnewvehicle"
                                           id="tblnewvehicle" width="100%">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Asset Code</th>
                                            <th>Asset Name</th>
                                            <th>Description</th>
                                            <th>Plate No.</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <input type="hidden" name="asset_id" id="vehicle_id" class="form-control"/>
                        <input type="hidden" name="asset_cost" id="vehicle_cost" class="form-control"/>
                        <input type="hidden" name="asset_qty" id="vehicle_qty" class="form-control" value="1"/>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label form-control-label">Asset Code: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <input type="text" name="asset_code" id="vehicle_code" disabled class="form-control" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Asset Name: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="asset_name" rows="3" disabledid="vehicle_name"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Description: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="asset_desc" rows="3" disabled id="vehicle_desc"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Remarks: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="asset_remarks" rows="3" id="vehicle_remarks"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group m-form__group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Components: </label>
                            <div class="col-12">
                                <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" name="tblvehiclecomp" id="tblvehiclecomp" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Asset Code</th>
                                                <th>Asset Name</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                        <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--vehicle modal-->
    <!--multipe modal-->
    <div class="modal fade" id="add_multiple_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         style="display: block;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Add Multiple Assets
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                ×
                            </span>
                    </button>
                </div>
                <form id="add_multiple_form" name="add_multiple_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input id="multiple_name" name="asset_remarks" value="" class="form-control" type="hidden">
                    <div class="col-12 modal-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">
                                Asset Code:
                            </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12 input-group">
                                <input type='text' class="form-control" name='multiple' id='multiple' autocomplete="off" />
                                <button id='multiple_search' class='btn m-btn--sm btn-success input-group-addon btnAdvance_search'
                                        type='button'>
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                </button>
                            </div>
                        </div>
                        <br>
                        <div class="form-group m-form__group row">
                            <div class="col-12">
                                <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" name="tblmultiple"
                                           id="tblmultiple" width="100%">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Asset Code</th>
                                            <th>Asset Name</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="form-group m-form__group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">
                                Added List:
                            </label>
                            <div class="col-12">
                                <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" name="tbladdedlist"
                                           id="tbladdedlist" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Asset Code</th>
                                            <th>Description</th>
                                            <th>(₱) Amount</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--multiple modal-->
    <!--edit-asset modal-->
    <div class="modal fade" id="edit_asset_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         style="display: block;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Edit Asset
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                ×
                            </span>
                    </button>
                </div>
                <form id="edit_asset_form" name="edit_item_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="col-12 modal-body">
                        <div class="form-group row m--hide">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">
                                Asset Code:
                            </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12 input-group">
                                <input type='text' class="form-control" name='edit_asset' disabled id='edit_asset'/>
                                <button id='edit_search' class='btn m-btn--sm btn-success input-group-addon btnAdvance_search'
                                        type='button'>
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                </button>
                            </div>
                        </div>
                        <br>
                        <div class="m-separator m-separator--solid d-xl-12 m--hide"></div>
                        <input type="hidden" name="edit_asset_id" id="edit_asset_id" class="form-control"/>
                        <input type="hidden" name="edit_asset_cost" id="edit_asset_cost" class="form-control"/>
                        <input type="hidden" name="edit_asset_qty" id="edit_asset_qty" class="form-control" value="1"/>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label form-control-label">Asset Code: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <input type="text" name="edit_asset_code" id="edit_asset_code" class="form-control" disabled />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Asset Name: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="edit_asset_name" rows="3" id="edit_asset_name" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Description: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="edit_asset_desc" rows="4" id="edit_asset_desc" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Remarks: </label>
                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                <textarea class="form-control" name="edit_asset_remarks" rows="4" id="edit_asset_remarks"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group m-form__group row m--hide">
                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 form-control-label">Components: </label>
                            <div class="col-12">
                                <div class="m_datatable m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" name="tblassetcomp_edit"
                                           id="tblassetcomp_edit" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Asset Code</th>
                                            <th>Asset Name</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                        <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--delete-asset modal-->
    <div class="modal fade" id="delete_asset_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         style="display: block;">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Delete</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="delete_asset_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="col-12 modal-body">
                        <div class="form-group m-form__group row">
                            <label class="col-12 col-form-label form-control-label">
                                Are you sure you want to remove this item?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnDelete">Yes</button>
                        <button type="button" class="btn btn-secondary btnClose" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--clear-asset modal-->
    <div class="modal fade" id="clear_asset_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         style="display: block;">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Clear</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="clear_asset_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="col-12 modal-body">
                        <div class="form-group m-form__group row">
                            <label class="col-12 col-form-label form-control-label">
                                Are you sure you want to remove all added items?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnDelete">Yes</button>
                        <button type="button" class="btn btn-secondary btnClose" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>