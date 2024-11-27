<div class="modal fade" id="edit-employee-benefits-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-edit-benefits" action="<?php echo site_url("payroll/employee/update_employee_allowance"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Benefits</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="allowance" class="form-control-label">
                            Benefit :
                        </label>
                        <select name="benefit_id" id="update_benefit_id" class="form-control"> 
                        </select>
                    </div>
                    <div class="form-group">
						<div class="row">
							<label class="form-control-label col-6">
								Rate*
							</label>
						</div>
                      
						<div class="row">
							<div class="col-12">
								<input type="text" name="rate" class="form-control"/>
							</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnUpdate">Save Changes</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>