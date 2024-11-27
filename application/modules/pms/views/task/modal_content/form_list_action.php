<div class="modal-header">
    <h5 class="modal-title">{{row.description}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="frmSequenceFormData" method="post" action="<?php echo site_url("eforms/keeps/save_sequence_form_data"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" v-model="row.form_id"/>
    <input type="hidden" name="block_id" v-model="row.block_id"/>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <div class="m-portlet m-portlet--bordered m-portlet--unair m-portlet--sm m-portlet--metal m-portlet--head-solid-bg"
                     data-portlet="true">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Checklist</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);" data-portlet-tool="fullscreen"
                                       class="m-portlet__nav-link m-portlet__nav-link--icon" title=""
                                       data-original-title="Fullscreen">
                                        <i class="la la-expand"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body m-portlet__body--no_padding">
                        <div id="tree_sequence_form-action"></div>
                    </div>
                </div>
                <div class="m-portlet m-portlet--bordered m-portlet--sm m-portlet--metal m-portlet--head-solid-bg"
                     data-portlet="true">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Other Information</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0)" data-portlet-tool="toggle"
                                       class="m-portlet__nav-link m-portlet__nav-link--icon" title=""
                                       data-original-title="Collapse">
                                        <i class="la la-plus"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="form-group m-form__group m--margin-bottom-15">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control m-input" id="remarks" name="remarks" rows="5"
                                      v-model="row.remarks" @change="updateRowData"></textarea>
                        </div>
                        <div class="m-form__group form-group m--margin-bottom-15">
                            <label for="status">Status</label>
                            <div class="m-checkbox-inline">
                                <label class="m-checkbox">
                                    <input id="status" type="radio" name="status" value="1" v-model="row.status"
                                           @change="updateRowData"/>
                                    Ongoing
                                    <span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input id="status" type="radio" name="status" value="2" v-model="row.status"
                                           @change="updateRowData"/>
                                    Hold
                                    <span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input id="status" type="radio" name="status" value="3" v-model="row.status"
                                           @change="updateRowData"/>
                                    Complete
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <template v-if="node_count > 0">
                    <template v-for="(node, index) in nodes">
                        <div class="m-portlet m-portlet--bordered m-portlet--sm m-portlet--metal m-portlet--head-solid-bg">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">{{node.label}}</h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools"></div>
                            </div>
                            <div class="m-portlet__body">
                                <div class="form-group m-form__group form-group-sm row">
                                    <label :for="['switch-'+node.id]" class="col-4 col-form-label">
                                        Change Contractor
                                    </label>
                                    <div class="col-3">
                                <span class="m-switch m-switch--sm m-switch--icon">
                                    <label>
                                        <input :id="['switch-'+node.id]" type="checkbox" :name="['switch-'+node.id]"
                                               :value="node.id" @change="switchContractor(node.id, $event)"/>
                                        <span></span>
                                    </label>
                                </span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group form-group-sm row m--margin-bottom-15">
                                    <div class="col-lg-4">
                                        <label>
                                            Date of Task
                                        </label>
                                        <input :id="['date_'+node.id]" type="text"
                                               class="form-control m-input form-control-sm form-datepicker"
                                               :name="['date_' + node.id]" maxlength="10" size="10" autocomplete="off"
                                               v-model="row['date_'+node.id]" @change="updateRowData"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>
                                            Due Date
                                        </label>
                                        <input :id="['due_'+node.id]" type="text"
                                               class="form-control m-input form-control-sm form-datepicker"
                                               :name="['due_' + node.id]" maxlength="10" size="10" autocomplete="off"
                                               v-model="row['due_'+node.id]" @change="updateRowData"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group form-group-sm row">
                                    <div class="col-lg-8">
                                        <label class="">
                                            Contractor
                                        </label>
                                        <input :id="['contractor_'+node.id]" type="text"
                                               class="form-control m-input form-control-sm"
                                               :name="['contractor_'+node.id]" maxlength="75" size="75"
                                               autocomplete="off" v-model="row['contractor_'+node.id]"
                                               @change="updateRowData"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="">
                                            Contract #
                                        </label>
                                        <input :id="['contract_'+node.id]" type="text"
                                               class="form-control m-input form-control-sm"
                                               :name="['contract_'+node.id]" maxlength="22" size="22" autocomplete="off"
                                               v-model="row['contract_'+node.id]" @change="updateRowData"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show"
                         role="alert">
                        <div class="m-alert__icon">
                            <i class="flaticon-exclamation-1"></i>
                            <span></span>
                        </div>
                        <div class="m-alert__text">
                            No
                            <strong>
                                Checklist
                            </strong>
                            Data!
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave">Save</button>
        <button class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</form>