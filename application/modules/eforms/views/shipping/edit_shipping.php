<style>
    .btn {
        margin-bottom: 3px;
    }

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
    }

    @media screen and (max-width: 480px){
        #fix-mobile button, #fix-mobile a{
            flex: 0 0 100%;
            max-width: 100%;
        }
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
                                <a type="button" href="view_shipping?id=<?php echo $_GET['id'];?>" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Shipping Advice Detail
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <form id="frm_status_edit">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-2 col-form-label">
                                        Type:
                                    </label>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-10">
                                        <select id="type" name="cat" disabled data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-2 col-form-label">
                                        Priority:
                                    </label>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-10">
                                        <select id="priority" name="priority" data-validation="required">
                        
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                        File Under:
                                    </label>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                        <select id="file_under" name="company_from"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                        File department:
                                    </label>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                        <select id="select_department" name="department_from"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                        Requested by:
                                    </label>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                        <select id="requested_by" name="requested_by"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">  
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2 col-form-label">
                                        Contents
                                    </label>
                                    <div id="fix-mobile" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10 text-right">
                                        <a class="btn btn-primary m-btn--sm btnNew text-white" data-toggle='modal' data-target='#add_item_modal'> 
                                            <span>
                                                <i class="la la-plus"></i>            
                                                <span>                             
                                                    Add Item    
                                                </span>
                                            </span>      
                                        </a>
                                        <a class="btn btn-primary m-btn--sm btnNew text-white" data-toggle='modal' data-target='#add_asset_modal' > 
                                            <span>
                                                <i class="la la-plus"></i>            
                                                <span>                             
                                                    Add Assets    
                                                </span>
                                            </span>      
                                        </a>
                                        <button type="button" class="btn m-btn--sm btn-danger btnNew " id="clear">
                                            <i class="la la-trash"></i>
                                                Clear
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <div class="col-12">
                                        <div class="m_datatable m-datatable--scroll col-12 table-responsive">
                                            <table class="table table-striped table-bordered" name="tblcontent" id="table-shipping-content" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>Asset/Stock Code</th>
                                                        <th>Quantity</th>
                                                        <th>Name</th>
                                                        <th>Purpose</th>
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
                        <div class="row">
                            <div class="col-md-6">  
                                <div id="ex-left">
                                
                                </div>
                                <div id="in-left">
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                            Ship to:
                                        </label>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                            <select id="select_ship_to" name="ship_to" data-validation="required">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                            Company:
                                        </label>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                            <textarea class="form-control m-input" id="company" name="company" rows="4" data-validation="required" v-model="vm_tab1.ship_to_company" disabled></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                            Location:
                                        </label>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                            <select id="location" name="location" data-validation="required">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                            Address:
                                        </label>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                            <textarea name="ship_to_address" class="form-control" rows="4" v-model="vm_tab1.ship_to_address" data-validation="required"></textarea>
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                        Ship Date & Time:
                                    </label>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9 input-group date" id="due_dt">
                                        <input class="form-control m-input" type="text" name="ship_date" id="date" maxlength="22" v-model="moment(vm_tab1.ship_date).format('MM/D/Y h:m ')" data-validation="required"/>
                                        <span class="input-group-addon">
                                                <i class="la la-calendar glyphicon-th" id="due_dt"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                </div>  
                                <div class="form-group m-form__group row m-radio-inline">
                                    <label class="col-3 col-form-label">
            
                                    </label> &nbsp; &nbsp; &nbsp;
                                    <label class="m-radio">
                                        <input class="form-control m-input" type="radio" name="type" value="service" checked/>
                                            Service
                                            <span></span>
                                    </label>
                                    <label class="m-radio">
                                        <input class="form-control m-input" type="radio" name="type" value="others">
                                            Others
                                        <span></span>
                                    </label>
                                </div>
                                <div id="service_component">
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                            Service Vehicle:
                                        </label>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                            <select id="service" name="vehicle"  data-validation="required">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                            Driver:
                                        </label>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                            <select id="driver" name="driver"  data-validation="required">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="other_component">
                                    
                                </div> 
                                <div class="form-group m-form__group row">
                                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-form-label">
                                        Transporter:
                                    </label>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                        <input class="form-control m-input" name="transporter" id="transporter" v-model="vm_tab1.transporter" data-validation="required"/>
                                    </div>
                                </div>
                                <div id="courier" class="form-group m-form__group row">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>  
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnNew">
                                <span>
                                    <i class="la la-save"></i>
                                    <span>
                                        Save
                                    </span>
                                </span>
                            </button>
                            <a href="<?php echo base_url('eforms/shipping/masterfile'); ?>">
                                <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnNew">
                                    <span>
                                        Close
                                    </span>
                                </button>
                            </a>   
                        </div>    
                    </div>
                </form>
            </div>
		</div>
	</div>
</div>


<!--item moda-->
<div class="modal fade" id="add_item_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Add Item
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div id="edit_shipping_select_item_content">
                <form id="add_item_form" name="add_item_form">
                    <div class="col-12 modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group">
                            <label class="col-12 form-control-label">
                                Item Look Up
                            </label>
                            <div class="col-12">
                                <select id="items" name="items"  data-validation="required">

                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 col-form-label form-control-label">
                                Quantity:
                            </label>
                            <div class="row">
                            <label class="col-1 col-form-label form-control-label">
                
                            </label>
                            <div class="col-5">
                                <input type="text" name="quantity" class="form-control" id="quantity" />
                            </div>
                            <div class="col-5">
                                <input type="text" name="uom" class="form-control" id="uom"/>
                                <span style="color: red;"><small>UOM <i>(Unit of Measurement)</i></small></span>
                            </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 form-control-label">
                                Name:
                            </label>
                            <div class="col-12">
                                <textarea class="form-control" name="description" rows="3" id="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 form-control-label">
                                Purpose:
                            </label>
                            <div class="col-12">
                                <textarea class="form-control" name="purpose" rows="3" id="purpose"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnNew">
                            Save
                        </button>
                        <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                            Close
                        </button>                   
                    </div>
                </form>
            </div>

            <div id="edit_shipping_add_item_content" hidden>
            <form id="add_new_item_form" name="add_new_item_form">
                <div class="col-12 modal-body">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group">
                            <label class="col-12 form-control-label">
                                Stock Code (NEW ITEM)
                            </label>
                            <div class="col-12">
                                <input type="text" class="form-control" name="inventorycode" data-validation="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 col-form-label form-control-label">
                                Description:
                            </label>
                            <div class="col-12">
                                <input type="text" name="item_description" class="form-control" id="item_description" data-validation="required"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-12">
                                <select id="add_uom" name="uom">

                                </select>
                                <span style="color: red;"><small>UOM <i>(Unit of Measurement)</i></small></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="add_item_back" onclick="backToSelectItem()" class="btn btn-submit btn-danger btnNew">
                            Back
                        </button>
                        <button type="submit" class="btn btn-submit btn-primary btnNew">
                            Save
                        </button>
                        <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                            Close
                        </button>                   
                    </div>
                </div>
            </form>    
        </div>
    </div>
</div>
<!--item moda edit-->
<div class="modal fade" id="edit_item_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Edit Item
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div id="select_item_edit_content">
                <form id="edit_item_form" name="edit_item_form">
                    <div class="col-12 modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <!-- <input type="hidden" name="cat" id="cat" class="form-control" /> -->
                        <div class="form-group">
                            <label class="col-12 form-control-label">
                                Item Look Up
                            </label>
                            <div class="col-12">
                                <select id="edit_items" name="edit_items"  data-validation="required">

                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 col-form-label form-control-label">
                                Quantity:
                            </label>
                            <div class="row">
                            <label class="col-1 col-form-label form-control-label">
                
                            </label>
                            <div class="col-5">
                                <input type="text" name="quantity" id="quantity" class="form-control"/>
                            </div>
                            <div class="col-5">
                                <input type="text" name="uom" class="form-control" id="uom" />
                            </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 form-control-label">
                                Name:
                            </label>
                            <div class="col-12">
                                <textarea class="form-control" name="description" rows="3" id="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 form-control-label">
                                Purpose:
                            </label>
                            <div class="col-12">
                                <textarea class="form-control" name="purpose" rows="3" id="purpose"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-primary btnNew">
                            Save
                        </button>
                        <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                            Close
                        </button>      
                    </div>
                </form>
            </div>
            <div id="add_item_edit_content" hidden>
                <form id="add_new_item_form_edit" name="add_new_item_form_edit">
                    <div class="col-12 modal-body">
                        <input type="hidden" id="edit_csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group">
                                <label class="col-12 form-control-label">
                                    Stock Code (NEW ITEM)
                                </label>
                                <div class="col-12">
                                    <input type="text" class="form-control" name="inventorycode" data-validation="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-12 col-form-label form-control-label">
                                    Description:
                                </label>
                                <div class="col-12">
                                    <input type="text" name="item_description" class="form-control" id="item_description" data-validation="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-12">
                                    <select id="edit_uom" name="uom">

                                    </select>
                                    <span style="color: red;"><small>UOM <i>(Unit of Measurement)</i></small></span>
                                </div>
                            </div>
                        <div class="modal-footer">
                            <button type="button" id="edit_item_back" onclick="backToSelectItem_edit()" class="btn btn-submit btn-danger btnNew">
                                Back
                            </button>
                            <button type="submit" class="btn btn-submit btn-primary btnNew">
                                Save
                            </button>
                            <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                                Close
                            </button>                   
                        </div>
                    </div>    
                </form>
            </div>     
        </div>
    </div>
</div>
<!--assets moda-->
<div class="modal fade" id="add_asset_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
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
            <div class="col-12 modal-body">
                <form id="add_asset_form" name="add_asset_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            Asset Look Up
                        </label>
                        <div class="col-12">
                            <select id="assets" name="assets"  data-validation="required">

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 col-form-label form-control-label">
                            Quantity:
                        </label>
                        <div class="row">
                        <label class="col-1 col-form-label form-control-label">
            
                        </label>
                        <div class="col-5">
                            <input type="text" name="quantity2" class="form-control" id="quantity2" />
                        </div>
                        <div class="col-5">
                            <input type="text" name="uom2" class="form-control" id="uom2" />
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            Name:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="description2" rows="3" id="description2"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-12 form-control-label">
                            Purpose:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="purpose2" rows="3" id="purpose2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Save
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--clear-asset modal-->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Delete
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "delete_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Are you sure you want to remove this item?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                        No
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--clear-asset modal-->
<div class="modal fade" id="clear_asset_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Clear
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "clear_asset_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Are you sure you want to remove all added items?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                        No
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
