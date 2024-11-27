<div class="m-content">
	<div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Payroll Group
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul id="group_notification" class="m-portlet__nav">
							<li v-if="count > 0 && notification_clicked === false" class="m-portlet__nav-item"><small>Duplicate Payroll Group</small></li>
							<li v-if="count > 0" class="m-portlet__nav-item">
								<a href="javascript:void(0);" 
									class="m-portlet__nav-link m-portlet__nav-link--icon" 
									:class="notification_clicked === false ? 'm-animate-shake':''" 
									data-toggle="modal" 
									data-target="#modalGroupNotification"
									@click="toggleClicked()">
									<i class="flaticon-music-1 m--font-danger"></i>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								<div class="form-group m-form__group row align-items-center">
									<div class="col-md-4">
                                        <button id="btnNewEmployeeGroup" type="button"
                                        class="btn btn-success m-btn m-btn--icon btnNew"
                                        data-modal="<?php echo site_url("payroll/employee/get_employee_group_modal/new"); ?>">
                                        <span>
                                            <i class="fa fa-plus"></i>
                                            <span>NEW EMPLOYEE GROUP</span>
                                        </span>
                                        </button>
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
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll ">
							<table class="table table-striped table-bordered row-border" id="table-payroll_group" width="100%"></table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="documentModal" class="modal fade document-modal-container"
    data-keyboard="false" data-backdrop="static"
    modal-exempt-custom tabindex="-1"
    role="dialog"></div>
</div>
<div id="modalGroupNotification" class="modal fade document-modal-container"
    data-keyboard="false" data-backdrop="static"
    modal-exempt-custom tabindex="-1"
    role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Duplicate Employee Payroll Group</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="modal-duplicate-entries" class="mb-3 m-form">
					<template v-if="count > 0">
						<ol class="p-0 pl-3">
							<template v-for="(item, index) in data">
								<li>
									<div class="row">
										<div class="col-md-6">
											<p>{{item.employee_name}}</p>
										</div>
										<div class="col-md-6 text-right">
											<template v-for="(itemx, indexx) in item.payroll_group">
												<span class="m-badge m-badge--danger m-badge--wide m-badge--rounded mr-1 mb-1 text-white">{{itemx.description}}</span>
											</template>
										</div>
									</div>
									<div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x mb-2"></div>
								</li>
							</template>
						</ol>
					</template>
				</div>
			</div>
		</div>
	</div>
</div>