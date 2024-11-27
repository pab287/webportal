<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Shipping advice
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<?php 
						$ses_id = $this->core_layout->getCurrentEmployeeId();	
						$role = $this->core_layout->getEmployeeData($ses_id);
						if($role['group_id'] == 1){ ?>
						<span class="btn m-btn" id="telegram_config" data-toggle="modal" data-target="#modal_telegram_config_sa"><i class="la la-ellipsis-h m--font-brand"></i></span>
						<?php } ?>		
					</div>
				</div>
				<div class="m-portlet__body">
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
				<!---->	
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-6">
									<a href="<?php echo site_url("eforms/shipping/new_shipping");?>">
										<button id="add_new" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew mb-1" stype="button">
											<span> 
												<i class="la la-plus"></i>
												<span>
													New
												</span>
											</span>
										</button>
									</a>
									<button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search mb-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
										<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
											Query Builder
										</a>
									</div>
									<div class="btn-group">
										<button type="button" id="mass_actions" class="btn btn-brand m-btn m--hide m-btn--icon m-btn--pill dropdown-toggle btnNew" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Actions
										</button>
										<div class="dropdown-menu">
											<a class="dropdown-item" href="#" onclick="approve_all_shipping('Approved')">
												<i class="fa fa-check" style="color: #34bfa3;"></i> Approve
											</a>
											<a class="dropdown-item" href="#" onclick="approve_all_shipping('Disapproved')">
												<i class="fa fa-times" style="color: red;"></i> Disapprove
											</a>
											<a class="dropdown-item" href="#" onclick="approve_all_shipping('Cancelled')">
												<i class="fa fa-trash" style="color: red;"></i>
												Cancel
											</a>
										</div>
									</div>
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
							<div class="m-btn-group btn-group" role="group">
								<button id="tbl-btn-share" title="Export" type="button"
										class="btn btnExport btn-success m-btn dropdown-toggle"
										data-toggle="dropdown" aria-haspopup="true"
										aria-expanded="false">
									<i class="la la-external-link"></i>
								</button>
								<div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
										x-placement="bottom-start"
										style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
									<a href="" class="dropdown-item datatable-csv" id="ExportCSV">
										<i class="m-nav__link-icon la la-file-o"></i>
										<span class="m-nav__link-text">
											CSV
										</span>
									</a>
									<a href="" class="dropdown-item datatable-pdf" id="ExportPDF">
										<i class="m-nav__link-icon la la-file-pdf-o"></i>
										<span class="m-nav__link-text">
											PDF
										</span>
									</a>
									<a href="" class="dropdown-item datatable-excel" id="ExportExcel">
										<i class="m-nav__link-icon la la-file-excel-o"></i>
										<span class="m-nav__link-text">
											EXCEL
										</span>
									</a>   
								</div>
							</div>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
					<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								
							</div>
						</div>
					</div>
                </div>
				<input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
						<table class="table table-striped table-bordered" id="table-shipping" width="100%">
							<thead>
								<tr>
									<th class="notExport" style="padding-right:10px!important; padding-left: 10px!important;" valign="middle">
										<div class="text-center">
											<label class="mb-0">
												<input type="checkbox" class="flat-red" id="selectall">
											</label>
										</div>
									</th>
									<th>Status</th>
									<th>Priority</th>
									<th>Advice #</th>
									<th>File Under</th>
									<th>Ship To</th>
									<th>Item</th>
									<th>Shipping Date</th>
									<th class="notExport">Action</th>
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-query-builder">
	<form id="frm-query-builder">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Query Builder</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="query-builder"></div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="clear_query_builder()" class="btn btn-danger btnAdvance_search mr-auto">Clear</button>
					<button type="button" id="query-builder-btn" class="btn btn-primary btnAdvance_search">Generate</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_telegram_config_sa">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-telegram mr-1"></i>Telegram Notification</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="save_telegram_config">
				<div class="modal-body">
					<div class="form-group m-form__group row">
						<input type="hidden" id="config_id">
						<input type="hidden" id="module" value="shipping_advice">
						<label class="col-4 col-form-label">
							Chat Id
						</label>
						<div class="col-8">
							<input type="text" class="form-control" id="chat_id" name="chat_id">
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label class="col-4 col-form-label">
							Bot Token
						</label>
						<div class="col-8">
							<textarea class="form-control" row="4" id="telegram_bot_token" name="telegram_bot_token"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="save_telegram_config()" class="btn btn-primary btnAdvance_search"><i class="la la-save mr-2"></i>Save</button>
				</div>
			</form>
		</div>
	</div>
</div>