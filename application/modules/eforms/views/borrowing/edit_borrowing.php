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
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
              <span class="m-portlet__head-icon">
                  <a type="button" href="view_borrowing?id=<?php echo $_GET['id'];?>" title="Back to Viewing" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                      <i class="la la-arrow-left"></i>
                  </a>
              </span>
							<h3 class="m-portlet__head-text">
								Edit Borrowing
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
				<div class="m-portlet__body">
          <form action="#" id="form_borrowing" class="form-horizontal">
          <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <input type="hidden" value="" name="description"/>
                <div class="form-group m-form__group row">
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                    Borrower
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <select id="select2_borrower" name="borrower" data-validation="required" onchange="emp_details()">

                    </select>
                  </div>
                </div>
                <div class="form-group m-form__group row" >
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                    Transaction Date
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <div class='input-group date' id="trans_date">               
                      <input class="form-control m-input" type="text" name="trans_date"  data-validation="required" v-model="vm_tab1.date_trans"/>
                      <span class="input-group-addon">
                        <i class="la la-calendar glyphicon-th"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="form-group m-form__group row" >
                  <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                    Date Needed
                  </label>
                  <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                    <div class='input-group date' id="need_dt">               
                    <input class="form-control m-input" type="text" name="need_dt" data-validation="required" v-model="vm_tab1.date_needed" />
                      <span class="input-group-addon">
                        <i class="la la-calendar glyphicon-th"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group m-form__group row">
                  <label class="col-md-2 col-lg-2 col-sm-12 col-form-label">
                    Purpose
                  </label>
                  <div class="col-md-10 col-lg-10 col-sm-12">
                    <textarea name="purpose" rows="5" cols="50" class="form-control" data-validation="required" v-text="vm_tab1.purpose"> </textarea> 
                  </div>
                </div>
              </div>
            </div>
            <div class="m-separator m-separator--dashed d-xl-12"></div> 
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group m-form__group row">
                  <div id="fix-mobile" class="col-12 text-right m--margin-bottom-10">
                    <a  class="btn btn-warning m-btn--sm btnNew text-white " onclick="add_asset()"> 
                      <span>
                        <i class="la la-plus"></i>                                            
                          assets & components
                      </span>      
                    </a>
                    <a  class="btn btn-success m-btn--sm btnNew text-white " onclick="add_item()"> 
                      <span>
                        <i class="la la-plus"></i>                                                                    
                          Sample Item
                      </span>      
                    </a>
                    <a class="btn btn-info m-btn--sm btnNew text-white " onclick="add_vehicle()"> 
                      <span>
                        <i class="la la-plus"></i>                                   
                          V&E Component
                      </span>      
                    </a>
                    <button type="button"  class="btn btn-danger m-btn--sm btnClear " onclick="clear_content()">
                      <i class="la la-trash"></i>
                          Clear
                    </button>
                  </div>
                  <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12 table-responsive-sm">
                    <table class="table table-striped table-bordered" id="table-content" width="100%">
                      <thead>
                        <tr>
                          <th>Item Borrowed</th>
                          <th>Qty</th>
                          <th>Date Borrowed</th>
                          <th>Date Due</th>
                          <th>Date Returned</th>
                          <th width="15%">Action</th>
                        </tr>
                      </thead>

                      <tbody> 
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12" id="table_v"></div>
                </div>
                <div class="modal-footer">
                  <button type="submit" id="btnSaveBorrowing" onclick="update_borrowing()" class="btn btn-accent btnSave"><i class="la la-floppy-o"></i> Save</button>
                  <a href="<?php echo site_url("eforms/borrowing/masterfile");?>"  class="btn btn-metal text-white btnCancel" >
                    <span>
                      CANCEL
                    </span>
                  </a>
                </div>
              </div>
            </div>
          </form>
			  </div>
      </div>
    </div>
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
            <b>Are you sure you want to Delete data</b> 
          </div>
          </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="delete_content()" class="btn btn-primary m-btn m-btn--icon  btnDelete">Yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--icon  btnCancel" data-dismiss="modal">No</button>
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
      <form action="#" id="form_content" class="form-horizontal">
        <div class="modal-body form">
          <input type="hidden" value="" name="id"/> 
          <input type="hidden" value="" name="type"/> 
          <input type="hidden" value="" name="code"/>
          <input name="price" class="form-control" type="hidden" value="0.00">
          <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="form-group" id="asset">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Asset Code *</label>
            <div class="col-md-12">
              <select id="select2_asset" name="asset" data-validation="required" onchange="asset_details()"></select> 
            </div>
          </div>
          <!--
            <div class="form-group" id="sample">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Sample Code</label>
            <div class="col-md-12">
              <input class="form-control m-input" type="text" name="sample"   />
            </div>
            </div>
          -->
          <div class="form-group" id="vehicle">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Asset Code *</label>
            <div class="col-md-12">
              <select id="select2_vehicle" name="vehicle" data-validation="required" onchange="vehicle_details()"></select> 
            </div>
          </div>
          <div class="form-group" id="sample">
            <label class="control-label col-md-4 col-sm-4 col-xs-12" >Name *</label>
            <div class="col-md-12">
              <textarea name="sample" class="form-control" data-validation="required" autocomplete="off"></textarea> 
            </div>
          </div>
          <div class="form-group" id="asset_name">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Asset Name</label>
            <div class="col-md-12">
              <textarea name="name"  class="form-control" data-validation="required" readonly autocomplete="off"></textarea> 
            </div>
          </div>
          <div class="form-group" class="vehicle">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Description *</label>
            <div class="col-md-12">
              <textarea name="desc"  class="form-control" data-validation="required" autocomplete="off"></textarea> 
            </div>
          </div>
          <div class="form-group" id="sample_qty">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Quantity *</label>
            <div class="col-md-12">
              <input type="number" min="0" name="quantity" class="form-control" data-validation="required" autocomplete="off" /> 
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-12">Date Borrowed</label>
            <div class="col-md-8">
              <div class='input-group date' id="borrowed_dt">               
                <input class="form-control m-input" type="text" name="borrowed_dt" autocomplete="off" readonly />
                <span class="input-group-addon"><i class="la la-calendar glyphicon-th"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Date Due</label>
            <div class="col-md-8">
              <div class='input-group date' id="due_dt">               
                <input class="form-control m-input" type="text" name="due_dt" autocomplete="off" readonly />
                <span class="input-group-addon"><i class="la la-calendar glyphicon-th"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-4 col-sm-4 col-xs-12">Remarks</label>
            <div class="col-md-12">
              <textarea name="remarks" class="form-control" ></textarea> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="btnSave" onclick="save_content()" class="btn btn-primary m-btn m-btn--icon  btnSave">Save</button>
          <button type="button" class="btn btn-metal text-white m-btn m-btn--icon btnCancel" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div>
</div>

<div class="modal fade" id="clear_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Clear Form
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
                            Do you want to clear all data?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnClear">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button> 
                </div>
            </form>
        </div>
    </div>
</div>