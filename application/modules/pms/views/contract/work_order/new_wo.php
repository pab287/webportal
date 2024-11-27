<div class="m-content">
    <div class="row">
        <div id="contract-content" class="col-lg-4 col-md-4">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Work Order</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <form id="frmGenerateTask" method="post" action="<?php echo site_url("pms/contract/do_post_event/generate_task_contract"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet__body">
                    <template v-if="is_editable === true">
                        <input type="hidden" name="is_editable" v-model="is_editable" />
                        <input type="hidden" name="wo_version" v-model="row.wo_version" />
                        <input type="hidden" name="project_id" v-model="row.project_id" />
                        <input type="hidden" name="checklist_id" v-model="row.checklist_id" />
                        <div id="template_preview">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="m-portlet m-portlet--unair m-portlet--mobile m-portlet--bordered">
                                        <div class="m-portlet__body">
                                            <div id="checklist_preview" class="form-group m-form__group">NO CHECKLIST PREVIEW!</div>
                                            <div id="selectedItems" class="form-group m-form__group"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="block_multiple">Block</label>
                                        <select class="form-control select2" id="block_multiple" name="block_multiple[]" data-validation="required" multiple="multiple"></select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn m-btn btn-sm btn-primary btnNew" onclick="generateLots()">Generate Lot</button>
                                </div>
                            </div>
                            <div id="block_lot-content" class="m--margin-top-15">
                                <template v-if="count > 0">
                                    <template v-for="(vv, ii) in blocks" v-if="block_count[vv] > 0">
                                    <div class="m-portlet m-portlet--unair m-portlet--mobile m-portlet--bordered m--margin-bottom-10">
                                        <div class="m-portlet__body">
                                            <div class="row">
                                            <h6 class="col-12">BLOCK {{vv}}</h6>
                                                <template v-for="(item, index) in rows" v-if="item.block == vv">
                                                <div class="col-2">LOT {{item.lot}} <input type="hidden" name="other_units[]" :value="item.id" /></div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="form-group m-form__group">
                            <label for="wo_version">Work Order Version</label>
                            <select class="form-control select2" id="wo_version" name="wo_version" data-validation="required"></select>
                        </div>
                        <div id="template_preview"></div>
                    </template>
                </div>
                <div class="m-portlet__foot">
                    <div class="row align-items-center">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-success m-btn m-btn--pill m-btn--air btn-submit">Add To Task List</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div id="contract-item_preview" class="col-lg-8 col-md-8">
            <form id="frmAssignContract" method="post" action="<?php echo site_url("pms/contract/do_post_event/set_new_contract"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="row">
                <div class="col-12">
                    <div id="contract-content_preview" class="m-portlet fadeIn animated">
                        <input v-if="is_editable === true" type="hidden" name="id" v-model="row.id" />
                        <input v-if="is_editable === true" type="hidden" name="wo_task_id" v-model="row.wo_task_id" />
                        <input v-if="is_editable === false" type="hidden" name="units" v-model="row.units" />
                        <input v-if="is_editable === false" type="hidden" name="item_id" v-model="row.parent_id" />
                        <input v-if="is_editable === false" type="hidden" name="checklist_id" v-model="row.checklist_id" />
                        <input v-if="is_editable === false" type="hidden" name="project_id" v-model="row.project_id" />
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">Contract Information</h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <template v-if="is_editable === false">
                                        <li class="m-portlet__nav-item">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-brand m-btn m-btn--pill m-btn--air btnSave">
                                                    Actions
                                                </button>
                                                <button type="button" class="btn btn-brand m-btn m-btn--pill m-btn--air dropdown-toggle dropdown-toggle-split btn-submit btnSave" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a id="generateCode" class="dropdown-item" href="javascript:void(0);" @click="modalGenerateCode">
                                                        <i class="flaticon-cogwheel-1"></i> Generate Code
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="javascript:void(0);" @click="submitContractData('frmAssignContract', true)">
                                                        <i class="flaticon-interface"></i> Save
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:void(0);" @click="submitContractData('frmAssignContract', false)">
                                                        <i class="flaticon-interface-4"></i> Save and Continue
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                    <template v-else>
                                        <li class="m-portlet__nav-item">
                                            <a id="saveChanges" href="javascript:void(0);" class="m-portlet__nav-link btn btn-brand m-btn m-btn--pill m-btn--air btn-submit btnSave" @click="submitContractData('frmAssignContract')">SAVE CHANGES</a>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group m-form__group">
                                                <template v-if="is_editable === true">
                                                    <label for="wo_code">Work Order #</label>
                                                    <p class="form-control m--marginless" v-text="row.wo_code">&nbsp;</p>
                                                </template>
                                                <template v-else>
                                                    <label for="wo_code">Work Order # *</label>
                                                    <input id="wo_code" type="text" name="wo_code" data-validation="required" autocomplete="off" class="form-control" readonly="readonly" v-model="wo_code" />
                                                    <div class="m-form m-form--fit">
                                                        <span class="m-form__help">Generate the work code #<span>
                                                        </span></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group m-form__group">
                                                <template v-if="is_editable === true">
                                                   <label for="issued_date">ISSUED DATE</label>
                                                    <template v-if="co_type === 2">
                                                    <input id="issued_date" type="text" name="issued_date" data-validation="required" autocomplete="off" class="form-control" v-model="row.issued_date" />
                                                    </template>
                                                    <template v-else>
                                                    <p class="form-control m--marginless" v-text="row.issued_date">&nbsp;</p>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                   <label for="issued_date">ISSUED DATE *</label>
                                                <input id="issued_date" type="text" name="issued_date" data-validation="required" autocomplete="off" class="form-control" readonly="readonly" v-model="issued_date" />
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group m-form__group">
                                                <template v-if="is_editable === true">
                                                    <label for="due_date">DUE DATE</label>
                                                    <template v-if="co_type === 2">
                                                    <input id="due_date" type="text" name="due_date" data-validation="required" autocomplete="off" class="form-control" v-model="row.due_date" />
                                                    </template>
                                                    <template v-else>
                                                    <p class="form-control m--marginless" v-text="row.due_date">&nbsp;</p>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <label for="due_date">DUE DATE *</label>
                                                    <input id="due_date" type="text" name="due_date" data-validation="required" autocomplete="off" class="form-control" readonly="readonly" v-model="due_date" />
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group m-form__group">
                                                <label for="reference_code">Reference Code *</label>
                                                <input id="reference_code" type="text" name="reference_code" data-validation="required" autocomplete="off" class="form-control" v-model="row.reference_code" />
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
                                        <div class="col-md-4">
                                            <div class="form-group m-form__group">
                                                <label for="task_incharge">Supervisor *</label>
                                                <select id="task_incharge" name="task_incharge" data-validation="required" class="form-control select2"></select>
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
                                        <div class="col-md-8">
                                            <div class="form-group m-form__group">
                                                <label for="leadman">Leadman *</label>
                                                <input id="leadman" name="leadman" data-validation="required" class="form-control" autocomplete="off" v-model="row.leadman" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group m-form__group">
                                                <label for="remarks">Remarks</label>
                                                <textarea class="form-control m-input" id="remarks" name="remarks" rows="5" v-model="row.remarks"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="m-portlet m-portlet--mobile">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">Work Order Information</h3>
                                </div>
                            </div>
                            <div id="woInformationAction" class="m-portlet__head-tools">
                                <ul class="m-portlet__nav" v-if="show_action === true">
                                    <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover">
                                        <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon m-dropdown__toggle">
                                            <i class="la la-ellipsis-h"></i>
                                        </a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__body">
                                                    <div class="m-dropdown__content">
                                                        <ul class="m-nav">
                                                            <li class="m-nav__section m-nav__section--first">
                                                                <span class="m-nav__section-text">
                                                                    Quick Actions
                                                                </span>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="javascript:void(0);" class="btnEdit m-nav__link" onclick="renderEditQtyUnitCost()">
                                                                    <i class="m-nav__link-icon flaticon-edit-1"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Edit Qty &amp; Unit Cost
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="javascript:void(0);" class="btnEdit m-nav__link" onclick="getCurrentTaskItems(true)">
                                                                    <i class="m-nav__link-icon flaticon-cancel"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Remove Task
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <!--begin: Datatable -->
                            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                                <table class="table table-bordered" id="table-task_items" width="100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th>Lots</th>
                                        <th>Unit</th>
                                        <th>Unit Cost</th>
                                        <th>Total Cost</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-right">GRAND TOTAL: </th>
                                            <th id="grandTotal" class="text-right">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!--end: Datatable -->
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<style>
input.form-control.m--input.text-right.form-control-table_field.maskQty {
    width: auto;
    display: inline-block;
    padding: 0 10px;
    position: relative;
}
</style>