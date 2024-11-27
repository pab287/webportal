<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">New Contract</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<div class="m-portlet m-portlet--unair m-portlet--head-sm m-portlet--danger m-portlet--head-solid-bg m--margin-bottom-0">
<form id="frmGenerateCode" method="post" action="<?php echo site_url("pms/contract/generate_contract_code"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="contract_count" value="<?php echo $count; ?>">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">Work Order</h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item">
                    <a id="generateCode" href="javascript:void(0);" class="m-portlet__nav-link btn btn-light m-btn m-btn--pill m-btn--air">
                        Generate Code
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="row">
           <div class="col-lg-6">
                <div class="form-group m-form__group">
                    <label for="wo_version">Work Order Version</label>
                    <select id="wo_version" class="form-control select2" name="wo_version" data-count="<?php echo $count; ?>" data-validation="required">
                        <option value="">&nbsp;</option>
                        <option value="block_lot">Specific Block and Lot</option>
                        <!-- option value="different_blocks">Different Blocks</option>
                        <option value="perimeter_fence">Perimeter Fence/Riprap</option -->
                        <option value="road_lots">Road Lots</option>
                        <option value="other_works">Other Works</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group m-form__group">
                    <label for="issued_date">Date Issued</label>
                    <input id="issued_date" type="text" class="form-control form-datepicker" maxlength="10" size="10" name="issued_date" data-validation="required" autocomplete="off" />
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group m-form__group">
                    <label for="due_date">Date Due</label>
                    <input id="due_date" type="text" class="form-control form-datepicker" maxlength="10" size="10" name="due_date" data-validation="required" autocomplete="off" />
                </div>
            </div>
        </div>
        <div id="version_template"></div>
    </div>
</form>
</div>
<div class="m-form m-form--fit">
    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x m--marginless"></div>
</div>
<form id="frmAddNewContract" method="post" action="<?php echo site_url("pms/contract/set_modal_contract"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="parent_id" value="<?php echo $id; ?>">
    <input type="hidden" id="wo_issued_date" name="issued_date" />
    <input type="hidden" id="wo_due_date" name="due_date" />
    <input type="hidden" id="task_items" name="task_items" />
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group m-form__group">
                    <label for="wo_code">Work Order #</label>
                    <input id="wo_code" type="text" 
                    class="form-control" 
                    name="wo_code" 
                    autocomplete="off"
                    readonly="readonly" 
                    data-validation="required" />
                    <div class="m-form m-form--fit">
                        <span class="m-form__help">Generate the work code # above.<span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group m-form__group">
                    <label for="contractor_id">Contractor</label>
                    <select id="contractor_id" class="form-control select2" name="contractor_id" data-validation="required"></select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group m-form__group">
                    <label for="task_incharge">Task In Charge</label>
                    <select id="task_incharge" class="form-control select2" name="task_incharge" data-validation="required"></select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="remarks">Remarks</label>
            <textarea id="remarks" class="form-control" name="remarks" rows="5" style="min-height: 100px;"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave">Save</button>
        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
    </div>
</form>