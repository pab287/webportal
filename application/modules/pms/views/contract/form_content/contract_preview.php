<form id="frmAssignContract" method="post" action="<?php echo site_url("pms/contract/do_post_event/set_new_contract"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="m-portlet fadeIn animated">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">TASK INFORMATION</h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item"></li>
            </ul>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="row">
            <!--div class="col-md-12 m--hide">
                <div class="m-portlet m-portlet--unair m--marginless">
                    <div class="m-portlet__body m-portlet__body--no-padding">
                        <div class="m_datatable m_datatable--no-padding m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                            <table class="table table-striped table--marginless" id="table-task_items" width="100%">
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
                            </table>
                        </div>
                    </div>
                </div>
            </div -->
            <div class="col-md-12" id="parentTask">
                <div class="m-widget11">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table">
                            <!--begin::Thead-->
                            <thead>
                                <tr>
                                    <td class="m-widget11__description">DESCRIPTION</td>
                                    <td class="m-widget11__qty m--align-center">QTY</td>
                                    <td class="m-widget11__unit m--align-center">UNIT</td>
                                    <td class="m-widget11__unit_cost m--align-right">UNIT COST</td>
                                    <td class="m-widget11__total_cost m--align-right">TOTAL COST</td>
                                </tr>
                            </thead>
                            <!--end::Thead-->
                            <!--begin::Tbody-->
                            <tbody>
                                <template v-if="count > 0">
                                    <template v-for="(vv, kk) in temp_items">
                                        <template v-if="vv.is_parent === true">
                                            <tr>
                                                <td colspan="6">
                                                    <p class="m-widget11__title m--marginless m-widget11__task">
                                                        <i class="fa fa-square fa--custom_sm"></i>
                                                        {{vv.label}}
                                                    </p>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr>
                                                <td class="m--custom-font-12">
                                                    <p class="m-widget11__text m--marginless m-widget11__subtask">
                                                        <i class="fa fa-angle-double-right"></i>
                                                            {{vv.label}}
                                                    </p>
                                                </td>
                                                <td class="m--align-center m--custom-font-12">{{vv.qty}}
                                                <input type="hidden" v-bind:name="'qty['+vv.id+']'" v-bind:value="vv.temp_qty" />
                                                <input type="hidden" v-bind:name="'lots['+vv.id+']'" v-bind:value="vv.temp_lots" />
                                                </td>
                                                <td class="m--align-center m--custom-font-12">
                                                    {{vv.unit}}
                                                </td>
                                                <td class="m--align-right m--custom-font-12 m--font-primary">
                                                    {{vv.tariff}}
                                                </td>
                                                <td class="m--align-right m--custom-font-12 m--font-primary m--font-boldest">
                                                    {{vv.total}}
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </template>
                            </tbody>
                            <!--end::Tbody-->
                            <tfoot>
                                <tr><td colspan="5">&nbsp;</td></tr>
                                <tr>
                                    <th colspan="4" class="text-right">GRAND TOTAL: </th>
                                    <th class="text-right m--font-primary">{{getGrandTotal}}</th>
                                </tr>
                            </tfoot>
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="contract-content_preview" class="m-portlet fadeIn animated">
    <input type="hidden" name="units" v-model="row.units" />
    <input type="hidden" name="item_id" v-model="row.parent_id" />
    <input type="hidden" name="checklist_id" v-model="row.checklist_id" />
    <input type="hidden" name="project_id" v-model="row.project_id" />
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">Contract Information</h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item">
                    <a id="generateCode" href="javascript:void(0);" class="m-portlet__nav-link btn btn-success m-btn m-btn--pill m-btn--air" @click="modalGenerateCode">GENERATE CODE</a>
                </li>
                <li class="m-portlet__nav-item">
                    <a id="saveChanges" href="javascript:void(0);" class="m-portlet__nav-link btn btn-brand m-btn m-btn--pill m-btn--air" @click="submitContractData('frmAssignContract')">SAVE CHANGES</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group m-form__group">
                            <label for="wo_code">Work Order # *</label> 
                            <input id="wo_code" type="text" name="wo_code" data-validation="required" autocomplete="off" class="form-control" readonly="readonly" v-model="wo_code" />
                            <div class="m-form m-form--fit">
                                <span class="m-form__help">Generate the work code #<span>
                                </span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group m-form__group">
                            <label for="issued_date">ISSUED DATE *</label> 
                            <input id="issued_date" type="text" name="issued_date" data-validation="required" autocomplete="off" class="form-control" readonly="readonly" v-model="issued_date" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group m-form__group">
                            <label for="due_date">DUE DATE *</label>
                            <input id="due_date" type="text" name="due_date" data-validation="required" autocomplete="off" class="form-control" readonly="readonly" v-model="due_date" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group m-form__group">
                            <label for="reference_code">Reference Code *</label>
                            <input id="reference_code" type="text" name="reference_code" data-validation="required" autocomplete="off" class="form-control" />
                            <div class="m-form m-form--fit">
                                <span class="m-form__help">Reference code as per contract<span>
                                </span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group m-form__group">
                            <label for="contractor_id">Contractor *</label>
                            <select id="contractor_id" name="contractor_id" data-validation="required" class="form-control select2"></select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group m-form__group">
                            <label for="task_incharge">Supervisor *</label>
                            <select id="task_incharge" name="task_incharge" data-validation="required" class="form-control select2"></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group m-form__group">
                            <label for="leadman">Leadman *</label>
                            <input id="leadman" name="leadman" data-validation="required" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group m-form__group">
                            <label for="foreman_id">Foreman *</label>
                            <select id="foreman_id" name="foreman_id" data-validation="required" class="form-control select2"></select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group m-form__group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control m-input" id="remarks" name="remarks" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet m-portlet--bordered m-portlet--unair">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Work Order Details</h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row m--margin-bottom-25">
                            <div class="col-md-12">
                                <div class="m-widget12">
                                    <div class="m-widget12__item">
                                        <span class="m-widget12__text1">
                                            DEVELOPMENT SITE
                                            <br>
                                            <span>{{wo_data.project_description}}</span>
                                        </span>
                                        <span class="m-widget12__text2">
                                            WORK ORDER TYPE
                                            <br>
                                            <span>{{wo_data.wo_type}}</span>
                                        </span>
                                    </div>
                                    <div class="m-widget12__item m--margin-bottom-10" v-if="wo_data.unit_count > 0">
                                        <span class="m-widget12__text1">UNIT / LOT</span>
                                    </div>
                                    <div class="m-widget12__item" v-if="wo_data.unit_count > 0">
                                        <div class="m-widget12__text1 row">
                                            <template v-for="(item, index) in wo_data.units">
                                                <span class="col-md-3">{{item.description}}</span>
                                            </template>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>