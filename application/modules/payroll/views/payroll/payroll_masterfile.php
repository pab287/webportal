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
                                Payroll
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
							<table class="table table-striped table-bordered row-border" id="table-payroll" width="100%">
								<thead>
									<tr>
										<th>Date from</th>
                                        <th>Date To</th>
                                        <th>Company</th>
                                        <th>Action</th>
									</tr>
								</thead>
								<tbody>	
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modal-payroll">
	<form id="frm-payroll">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="modal-dialog modal-full" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Payroll sheet</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll ">
						<table class="table table-striped table-bordered row-border" id="table-payroll-sheet" width="100%">
							<thead>
								<tr>
									<th>Employee</th>
									<th>Rate</th>
									<th>Days</th>
									<th>Legal Holidays</th>
									<th>Under Time Amount</th>
									<th>Amount</th>
									<th>Over Time Amount</th>
									<th>Over Time Hours</th>
									<th>N.Different'l Amount</th>
									<th>N.Different'l Hours</th>
									<th>Other/Adj2</th>
									<th>ECOLA</th>
									<th>Gross Pay</th>
									<th>SSS</th>
									<th>---</th>
									<th>Pagibig</th>
									<th>Tax</th>
									<th>Loans</th>
									<th>Net Due</th>
								</tr>
							</thead>
							<tbody>	
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</form>
</div>
