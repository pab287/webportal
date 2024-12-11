<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="masterfile" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                                New Travel Order
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">

                    </div>
				</div>
                <form action="#" id="form_travel_order" class="form-horizontal">
				<div class="m-portlet__body">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">  
                            <div class="form-group m-form__group row">
                                <label class="col-md-2 col-sm-2 col-xs-12 col-lg-2 col-sm-2 col-xs-12 col-form-label m--font-bolder">
                                    File Under
                                </label>
                                <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                    <select id="select2_file" name="company_id" data-validation="required" >

                                    </select>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-md-2 col-sm-2 col-xs-12 col-lg-2 col-sm-2 col-xs-12 col-form-label m--font-bolder">
                                    Department
                                </label>
                                <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                    <select id="select2_dep" name="dep_id" data-validation="required" >

                                    </select>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-md-2 col-sm-2 col-xs-12 col-lg-2 col-sm-2 col-xs-12 col-form-label m--font-bolder">
                                    Type
                                </label>
                                <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                    <div class="m-radio-inline" id="radio_type">
                                        <label class="m-radio" data-container="body" data-toggle="m-tooltip" data-placement="top" title="" data-original-title="Within the company">
                                            <input type="radio" name="type" value="internal" id="rd_internal">
                                                Internal
                                            <span></span>
                                        </label>
                                        <label class="m-radio" data-container="body" data-toggle="m-tooltip" data-placement="top" title="" data-original-title="Not within the company">
                                            <input type="radio" name="type" value="external" id="rd_external">
                                                External
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="m--hide" id="require_msg_type">
                                        <p style="color: #ff0000; font-size: 10px; font-weight: bold; margin-top: 3px;">This is a required field</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">  
                            <div class="form-group m-form__group row">
                                <label class="col-md-3 col-sm-3 col-xs-12 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bolder">
                                    Official Station
                                </label>
                                <div class="col-md-9 col-lg-9 col-sm-9 col-sm-12">
                                    <textarea name="station" rows="3" cols="50"  class="form-control" data-validation="required"></textarea> 
                                </div>
                            </div>        
                            <!-- <div class="form-group m-form__group row">
                                <label class="col-md-3 col-sm-3 col-xs-12 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                    Origin
                                </label>
                                <div class="col-md-9 col-lg-9 col-sm-9 col-sm-12">
                                    <textarea name="origin" rows="3" cols="50" class="form-control" data-validation="required"> </textarea> 
                                </div>
                            </div> -->
                            <div class="form-group m-form__group row">
                                <label class="col-md-3 col-sm-3 col-xs-12 col-lg-3 col-sm-3 col-xs-12 col-form-label m--font-bolder">
                                    Travel Type
                                </label>
                                <div class="col-md-9 col-lg-9 col-sm-9 col-sm-12">
                                    <div class="m-radio-inline">
                                        <label class="m-radio"><input id="service" type="radio" name="is_service" value="1" onclick="change_service()">Service<span></span></label>
                                        <label class="m-radio"><input id="hitch" type="radio" name="is_hitch" value="1" onclick="change_hitch()">Hitch<span></span></label>
                                        <label class="m-radio"><input id="commute" type="radio" name="is_commute" value="1" onclick="change_commute()">Commute<span></span></label>
                                        <label class="m-radio"><input id="personal" type="radio" name="is_personal" value="1" onclick="change_personal()">Personal Vehicle<span></span></label>
                                        <label class="m-radio"><input id="other" type="radio" name="is_other" value="1" onclick="change_other()">Others<span></span></label>
                                    </div>
                                </div>
                            </div>
                            <?php /*** service vehicle option for logistics and administrator role ***/ ?>
                            <div id="content_option">
                                <template v-if="allow_service_vehicle === true">
                                <div class="form-group m-form__group row" id="service_veh">
                                    <label class="col-md-3 col-sm-3 col-xs-12 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Service Vehicle
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-sm-12">
                                        <select id="select2_vehicle" name="vehicle" data-validation="required" ></select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="service_driver">
                                    <label class="col-md-3 col-sm-3 col-xs-12 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Driver
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-sm-12">
                                        <select id="driver" name="driver" data-validation="required"></select>
                                    </div>
                                </div>
                                </template>
                            </div>
                            <?php /*** service vehicle option for logistics and administrator role ***/ ?>
                            <div class="form-group m-form__group row m--font-bolder" id="other_remark" style="display: none;">
                                <label class="col-md-3 col-sm-3 col-xs-12 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                    Remarks
                                </label>
                                <div class="col-md-9 col-lg-9 col-sm-9 col-sm-12">
                                    <textarea name="remark"  class="form-control" data-validation="required"></textarea> 
                                </div>
                            </div>   
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed d-xl-12"></div> 
                    <br>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group m-form__group row">
                                <div class="col-12 text-right m--margin-bottom-10">
                                    <a  class="btn btn-primary m-btn--sm btnNew text-white " onclick="add_personnel()"> 
                                        <span>
                                            <i class="la la-users"></i>            
                                        <span>                                                    
                                        Add Personnel      
                                    </a>
                                    <button type="button" class="btn btn-danger m-btn--sm btnClear" onclick="clear_personnel()">
                                        <i class="la la-trash"></i>
                                        Clear
                                    </button>
                                </div>             
                                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12">
                                    <table class="table table-striped table-bordered" id="table-personnel" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Position/Designation</th>
                                                <th width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12 m--hide" id="table_v">
                                    <p style="color: #ff0000; font-size: 10px; font-weight: bold;">Required. Add atleast 1 Content</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group m-form__group row">
                                <div class="col-12 text-right m--margin-bottom-10">
                                    <a class="btn btn-primary m-btn--sm btnNew text-white" onclick="add_destination()"> 
                                        <span>
                                            <i class="la la-map-marker"></i>            
                                            <span>                                                            
                                                Add Destination  
                                            </span>
                                        </span>      
                                    </a>
                                    <button type="button"  class="btn btn-danger m-btn--sm btnClear" onclick="clear_destination()">
                                        <i class="la la-trash"></i>
                                        Clear
                                    </button>
                                </div>                   
                                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12">
                                    <table class="table table-striped table-bordered" id="table-destination" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Destinations</th>
                                                <th>Date & Time</th>
                                                <th width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12 m--hide" id="table2_v">
                                    <p style="color: #ff0000; font-size: 10px; font-weight: bold;">Required. Add atleast 1 Content</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot text-right">
                    <button type="submit" id="btnSaveTravel" onclick="add_travel_order()" class="btn btn-accent btnSave"><i class="la la-floppy-o"></i> Submit</button>
                    <a href="<?php echo site_url("eforms/travel_order/masterfile"); ?>"  class="btn btn-metal m-btn--custom text-white btnCancel" >
                        <span>CANCEL</span>
                    </a>
                </div>
                </form>
            </div>
        </div> 
    </div>
</div>

<div class="modal fade" id="modal_form_delete" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="form_delete" class="form-horizontal">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body form">
                    <input type="hidden" value="" name="delete_id"/> 
                    <div class="form-group">
                        <div class="col-md-12">
                            <b>Are you sure you want to remove this personnel?</b> 
                        </div>
                    </div>  
                </div>
                <div class="modal-footer">
                    <button type="button"  onclick="delete_personnel()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnDelete" >Yes</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_delete2" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="form_delete2" class="form-horizontal">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body form">
                    <input type="hidden" value="" name="delete_id2"/> 
                    <div class="form-group">
                        <div class="col-md-12">
                            <b>Are you sure you want to delete this content?</b> 
                        </div>
                    </div>           
                </div>
                <div class="modal-footer">				  
                    <button type="button"  onclick="delete_destination()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnDelete" >Yes</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_personnel" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="form_personnel" class="form-horizontal">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body form">
                    <input type="hidden" value="" name="id_personnel"/> 
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12">Employee</label>
                        <div class="col-md-12">
                            <select id="select2_emp" name="employee_id" ></select> 
                        </div>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" onclick="save_personnel()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_destination" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close closeNewTravel" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row m-0" style="width: 100%">
                <div class="col-lg-12 p-0 text-center">
                <!-- <input id="mapInputTO" class="controls" type="text" placeholder="Search Places Here.." /> -->
                <div id="tempMap" class="travelOrderMap" style="width: 100%;height: 500px;border: 1px solid #cccccc;" class="p-0"></div>
                </div>
                <div class="col-lg-12 p-0">
                    <form action="#" id="form_destination" class="form-horizontal">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="modal-body form">
                            <input type="hidden" value="" name="id_destination"/>
                            <input type="hidden" value="" id="formNewTravelFrom" name="formTravelFrom"/>
                            <input type="hidden" value="" id="formNewTravelTo" name="formTravelTo"/>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12 m--font-bolder">From</label>
                                        <div class="col-md-12 travel-order-control">
                                            <div class="col-md-12 p-0 input-control">
                                            <input type="text" id="travelFrom" class="form-control" placeholder="" name="from" data-validation="required">
                                            </div>
                                            <div id="travelOrderFromIcon" class="icon-control">
                                                <i class="la la-angle-down icon"></i>
                                                <div id="travelOrderOptionFrom">
                                                    <ul id="travel_option_from" v-if="checker === true">
                                                        <li v-for="sites in vm_tab3" v-on:click="selectedSite(sites.id)">{{ sites.site_name }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12 m--font-bolder">To</label>
                                        <div class="col-md-12 travel-order-control">
                                            <div class="col-md-12 p-0 input-control">
                                                <input type="text" id="travelTo" class="form-control" placeholder="" name="to" data-validation="required">
                                            </div>
                                            <div id="travelOrderToIcon" class="icon-control">
                                                <i class="la la-angle-down icon"></i>
                                                <div id="travelOrderOptionTo">
                                                    <ul id="travel_option_to" v-if="checker === true">
                                                        <li v-for="sites in vm_tab2" v-on:click="selectedSite(sites.id)">{{ sites.site_name }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12 m--font-bolder">Requested By</label>
                                <div class="col-md-12" id="requested-by">
                                    <select id="select2_req" name="requested_by" data-validation="required" ></select> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12 m--font-bolder">Purpose</label>
                                <div class="col-md-12">
                                    <textarea name="purpose" row="2" class="form-control" data-validation="required"> </textarea> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12 m--font-bolder">Date From</label>
                                        <div class="col-12 input-group date" id="date_from">
                                            <input readonly class="form-control m-input" type="text" name="date_from" id="issue_dt" data-validation="required" maxlength="22" />
                                            <span class="input-group-addon">
                                                    <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12 m--font-bolder">Date To</label>
                                        <div class="col-12 input-group date" id="date_to">
                                            <input readonly class="form-control m-input" type="text" name="date_to" id="issue_dt" data-validation="required" maxlength="22"/>
                                            <span class="input-group-addon">
                                                    <i class="la la-calendar glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-8 col-sm-8 col-xs-12 m--font-bolder">Special Instruction</label>
                                <div class="col-md-12">
                                    <textarea name="special"  class="form-control"></textarea> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12 m--font-bolder">Remarks</label>
                                <div class="col-md-12">
                                    <textarea name="remarks"  class="form-control" data-validation="required"> </textarea> 
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave2" onclick="save_destination()" class="btn btn-primary m-btn m-btn--custom m-btn--icon closeNewTravel btnSave">Save</button>
                            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon btnClose closeNewTravel" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
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
                    <button type="submit" class="btn btn-submit btn-primary btnDelete">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>