<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
              <span class="m-portlet__head-icon">
                  <a type="button" href="view_transmittal?id=<?php echo $_GET['id'];?>" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                      <i class="la la-arrow-left"></i>
                  </a>
              </span>
							<h3 class="m-portlet__head-text">
								Edit Transmittal
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
				<div class="m-portlet__body">
          <form action="#" id="form_transmittal" class="form-horizontal">
          <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					  <div class="row">
              <div class="col-md-5 col-sm-12">
                <div class="form-group m-form__group row">
                  <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12 col-form-label m--font-bold">
                    Type
                  </label>
                  <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                    <select class="form-control" id="type" name="type" onchange="type_change()" disabled>
                            
                    </select>
                  </div>
                </div>
                <div class="form-group m-form__group row">
                  <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12 col-form-label required m--font-bold">
                    File Under
                  </label>
                  <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                    <select id="select2_file" name="company_id" data-validation="required" >

                    </select>
                  </div>
                </div>
                <div class="form-group m-form__group row">
                  <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12 col-form-label required m--font-bold">
                    Department
                  </label>
                  <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                    <select id="select2_dep" name="dep_id" data-validation="required" >

                    </select>
                  </div>
                </div>
                <br>
                <div class="form-group m-form__group row">
                  <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12 col-form-label required m--font-bold">
                    Requested By
                  </label>
                  <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                    <select id="select2_requested" name="requested_by" data-validation="required" >

                    </select>
                  </div>
                </div>
                <div class="form-group m-form__group row">
                  <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12 col-form-label m--font-bold">
                    Other information
                  </label>
                  <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                    <textarea name="info" rows="3" cols="50"  v-text="vm_tab1.purpose" class="form-control" > </textarea> 
                  </div>
                </div>
                <div class="form-group m-form__group row">
                  <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12 col-form-label m--font-bold">
                    Priority
                  </label>
                  <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                    <select class="form-control" id="priority" name="priority">
                                
                    </select>
                  </div>
                </div>
               </div>
              <div class="col-md-7 col-sm-12">
                <div class="form-group m-form__group row">
                  <div class="col-12 text-right m--margin-bottom-10">
                    <a  class="btn btn-primary  btnNew text-white m-btn--sm  " onclick="add_content()"> 
                      <span>
                        <i class="la la-plus"></i>                                       
                           Content    
                      </span>      
                    </a>
                    <button type="button"  class="btn btn-danger m-btn--sm  btnNew " onclick="clear_content()">
                      <i class="la la-trash"></i>
                          Clear
                    </button>
                  </div>            
                  <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12">
                    <table class="table table-striped table-bordered" id="table-content" width="100%">
                      <thead>
                        <tr>
                          <th>Content</th>
                          <th width="15%">Action</th>
                        </tr>
                      </thead>

                      <tbody> 
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12" id="table_v"></div>
                </div>
              </div>
            </div>
            <br>
            <div class="m-separator m-separator--dashed d-xl-12"></div>  
            <br>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="form-group m-form__group row" id="delivery_to_in">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label required m--font-bold">
                    Deliver To
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <select  id="select2_deliver" name="deliver_to" onchange="emp_details()" data-validation="required" >

                    </select>
                  </div>
                </div>
                <div class="form-group m-form__group row" id="delivery_to_ex">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bold">
                    Deliver To
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <input class="form-control m-input" type="text" name="delivery_to_ex" v-model="vm_tab1.ship_to" />
                  </div>
                </div>
                <div class="form-group m-form__group row" id="company_to_in">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bold">
                    Company
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <textarea id="deliver_company_in" name="deliver_company" rows="3" cols="50" disabled class="form-control" v-text="vm_tab1.company_to_desc"> </textarea> 
                  </div>
                </div>
                <div class="form-group m-form__group row" id="company_to_ex">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bold">
                    Company
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <textarea id="deliver_company_ex" name="deliver_company" rows="3" cols="50" class="form-control" v-text="vm_tab1.company_to"> </textarea> 
                  </div>
                </div>
                <div class="form-group m-form__group row" id="row_department">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bold">
                    Department
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <input class="form-control m-input" type="text" name="department" v-model="vm_tab1.department_to"/>
                  </div>
                </div>
                <div class="form-group m-form__group row">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label required m--font-bold">
                    Address
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <textarea name="deliver_address" rows="3" cols="50" class="form-control" data-validation="required" v-text="vm_tab1.ship_to_address"> </textarea> 
                  </div>
                </div>
                <div class="form-group m-form__group row">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label required m--font-bold">
                    Delivery Date
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">   
                    <div class='input-group date' id="delivery_date">               
                      <input class="form-control m-input" type="text" id="delivery_dt" name="delivery_date" v-model="vm_tab1.ship_date" data-validation="required"  />
                      <span class="input-group-addon">
                        <i class="la la-calendar glyphicon-th"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group m-form__group row">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">            
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <div class="m-radio-inline">
                      <label class="m-radio"><input id="service" type="radio" name="is_service" value="1"  onclick="change_service()">Service<span></span></label>
                      <label class="m-radio"><input id="other" type="radio" name="is_other" value="1" onclick="change_other()">Others<span></span></label>
                    </div>
                  </div>
                </div>
                <div class="form-group m-form__group row" id="service_veh">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label required m--font-bold required">
                    Service Vehicle
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <select id="select2_vehicle" name="vehicle"  onchange="veh_details()" data-validation="required" >
                      
                    </select>
                  </div>
                </div>
                <div class="form-group m-form__group row" id="service_driver">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label required m--font-bold required">
                    Driver
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <input class="form-control m-input" type="text" name="driver" data-validation="required"  v-model="vm_tab1.driver"/>
                  </div>
                </div>
                <div class="form-group m-form__group row" id="other_remark">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label required m--font-bold required">
                    Remarks
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <textarea name="remark"  class="form-control" data-validation="required" v-model="vm_tab1.others_remarks"> </textarea> 
                  </div>
                </div>
                <div class="form-group m-form__group row">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bold required">
                    Transporter
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <input class="form-control m-input" type="text" name="transporter" v-model="vm_tab1.transporter" data-validation="required" />
                  </div>
                </div>
                <div class="form-group m-form__group row" id="row_courier">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bold">
                    Courier & Waybill #
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <input class="form-control m-input" type="text" name="courier"  v-model="vm_tab1.waybill"/>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="modal-footer">
                  <button type="submit" id="btnSaveTransmittal" onclick="update_transmittal()" class="btn btn-brand"><i class="la la-floppy-o"></i> Submit</button>
                  <a href="<?php echo site_url("eforms/transmittal/masterfile");?>" >
                    <button type="button" style="color: #FFFFFF;" class="btn m-btn--custom btn-metal m-btn text-white btnNew" >Cancel</button>
                  </a>
                </div>
              </div>
            </div> 
          </form>
        </div>
			</div>
			<!--end::Portlet-->
      <div class="modal fade" id="modal_form_delete" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title"></h3>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  
                </div>
                <div class="modal-body form">
                  <form action="#" id="form_delete" class="form-horizontal">
                    <input type="hidden" value="" name="delete_id"/> 
                   <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                    
                    <div class="col-md-12">
                     <b>Are you sure you want to Delete this content?</b> 
                    </div>
                    </div>
                    
                    </div>
                   <div class="modal-footer">
				   <button type="button" onclick="delete_content()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew" >Yes</button>
                      <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">No</button>
                    </div>
                    </form>
                  </div><!-- /.modal-content -->
                </div>
    </div>
  </div>
         <div class="modal fade" id="modal_form_content" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title"></h3>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  
                </div>
                <div class="modal-body form">
                  <form action="#" id="form_content" class="form-horizontal">
                    <input type="hidden" value="" name="id"/> 
                   <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                    <label class="control-label col-md-2 required">Description</label>
                    <div class="col-md-12">
                     <textarea name="description"  class="form-control" data-validation="required"> </textarea> 
                    </div>
                    </div>
                    
                    </div>
                   <div class="modal-footer">
                      
                     <button type="submit" id="btnSave" onclick="save_content()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew">Save</button>
                      <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">Cancel</button>
                    </div>
                    </form>
                  </div><!-- /.modal-content -->
                </div>
		</div>
	</div>
</div>

<div class="modal fade" id="clear_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
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
              <form id = "clear_form">
                  <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                  <div class="col-12 modal-body">
                      <div class="form-group m-form__group row">
                          <label class="col-12 col-form-label form-control-label">
                              Are you sure you want to remove all added contents?
                          </label>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="submit" class="btn btn-submit btn-primary btnNew">
                          Yes
                      </button>
                      <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                          No
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <style>
    .select2-selection__rendered {
        font-weight: 500;
        color: #232323;
    }
</style>