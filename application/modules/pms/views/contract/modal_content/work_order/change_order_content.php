<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Change Order</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmChangeOrder" method="post" action="<?php echo site_url("pms/contract/do_post_event/update_contract_change_order"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="id" />
<div class="modal-body">
    <div class="m-portlet m-portlet--bordered m-portlet--unair">
        <div class="m-portlet__body m-portlet__body--no-padding">
            <div class="row m-row--no-padding m-row--col-separator-xl">
                <div class="col-md-6 col-lg-6 col-xl-6">
                    <div class="m-portlet m-portlet__head-sm m-portlet--unair m--marginless">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        BLOCK AND LOTS
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav" v-if="count > 1">
                                    <li class="m-portlet__nav-item">
                                        <span class="m-switch m-switch--sm m-switch--icon">
                                            <label class="m--marginless"><input id="checkAll" type="checkbox" @change="checkAllEvent($event)" checked="checked" /><span></span></label>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="m-form__group form-group">
                                        <div class="m-checkbox-inline">
                                            <label class="m-checkbox col-md-6 m--margin-bottom-10" style="margin: 0;" v-for="(item, index) in rows">
                                                <input type="checkbox" class="tempCheckbox" name="unit_id[]" checked="checked" :value="item.id" @change="updateBlockLotChecked" data-validation="checkbox_group" data-validation-qty="min1" />{{item.description}}
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="exampleSelect1">
                                            Include Other Block and Lots
                                        </label>
                                        <select class="form-control m-input select2" id="other_block_lot"></select>
                                    </div>
                                </div -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-6">
                    <div class="m-portlet m-portlet__head-sm m-portlet--unair m--marginless">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        UNIT COST <small>UPDATES</small>
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav" v-if="cost_count > 1">
                                    <li class="m-portlet__nav-item">
                                        <span class="m-switch m-switch--sm m-switch--icon">
                                            <label class="m--marginless"><input id="checkAllUnitCost" type="checkbox" @change="checkAllCostEvent($event)" /><span></span></label>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="row">
                                <div class="col-md-12">
                                    <template v-if="cost_count > 0">
                                        <div class="m-form__group form-group">
                                            <div class="m-checkbox-inline">
                                                <label class="m-checkbox col-6 m--margin-bottom-10" style="margin: 0;" v-for="(item, index) in unit_cost">
                                                    <input type="checkbox" class="tempCostCheckbox" :name="'cost['+item.id+']'" :data-id="item.id" :value="item.rc_tariff" @change="updatedCostChecked" />
                                                    <p class="m--marginless">{{item.label}}</p>
                                                    <p class="m--marginless">{{item.temp_format0}} -> <strong>{{item.temp_format1}}</strong></p>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="m-alert m-alert--outline m-alert--square alert alert-success fade show" role="alert">
                                            <strong>No Available Updates, </strong>Rate card is up to date!
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm m--margin-bottom-25">
        <table class="table table-striped table-bordered" id="table-task_contract_items" width="100%">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Qty</th>
                    <th>Lots</th>
                    <th>Unit</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th colspan="5"><p class="text-right m--marginless">Grand Total</p></th>
                    <th>0.00</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="row" v-if="triggerUpdate === true">
        <div class="col-12">
            <div class="form-group m-form__group">
                <label for="remarks">Change Order Remarks *</label>
                <textarea class="form-control m-input" id="remarks" name="remarks" rows="5" data-validation="required"></textarea>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary fadeIn animated" v-if="triggerUpdate === true">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
</form>