<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Edit Qty &amp; Unit Cost</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmEditQtyUnitCost" method="post" action="<?php echo site_url("pms/contract/do_post_event/update_contract_temp_qty_unit_cost"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
        <table class="table table-bordered" id="table-edit_qty_unitcost" width="100%">
            <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Cost</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btn-submit btnSave">Save Changes</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
</form>