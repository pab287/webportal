<style>
@media (min-width: 768px) {
  .modal-full {
    width: 100%;
   	max-width:90%;
  }
}

</style>
<div class="m-content">
	<div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Fixed Taxable Deduction
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
				
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air btnNew" href="#" data-toggle="modal" data-target="#modal-add-taxable-deduction">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
							<div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
								<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
									<input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
									<span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
								</div>
							</div>
						</div>
						<br>
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll ">
							<table class="table table-striped table-bordered row-border" id="table-fixed_taxable_deduction" style="width: 100%;">
								<thead>
									<tr>
										<th>Employee Name</th>
                                        <th>Basic Rate</th>
                                        <th>Fixed Taxable Deduction</th>
                                        <th>Created By</th>
                                        <th>Action</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modal-add-taxable-deduction">
	<form id="frm-add--taxable-deduction">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">New Fixed Taxable Deduction</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
                        <label for="" class="form-control-label required">Employee Name</label>
                        <div>
							<select id="employee_id" class="form-control" name="employee_id" data-validation="required"></select>
                        </div>
                    </div>
					<div class="form-group">
                        <label for="" class="form-control-label required">Fixed Amount</label>
                        <div class="row">
							<div class="col-md-6">
								<input type="number" name="fixed_amount" class="form-control" data-validation="required" autocomplete="off" />
							</div>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" onclick="Save()" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</form>
</div>
