<div class="m-content">
	<div class="row">
		<div class="col-lg-3">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Import
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
                </div>
                <form id="frmUpload" class="m-form m-form--fit m-form--label-align-right">
                <div class="m-portlet__body">
					<div class="form-group m-form__group">
						<div class="alert m-alert m-alert--default" role="alert">
							.csv file is only allowed to attach as file data. <a href="#" data-toggle="modal" data-target="#m_modal_downloadtemplate">Download Template</a>
						</div>
					</div>
                    <div class="form-group m-form__group">
                        <label for="exampleInputEmail1">
                            File Browser
                        </label>
                            <input type="file" id="temp_fileupload" name="files">
                        </label>
                    </div>
                </div>
				</form>
            </div>
        </div>

        <div class="col-lg-9">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Import data
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
                </div>
                <div class="m-portlet__body">
                <div id="progress_approve"
                    class="progress progress-striped active"
                    role="progressbar"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="display:none;"
                    >
                    <div
                        class="progress-bar progress-bar-success"
                        style="width: 0%;"
                    ></div>
                </div>
                    <!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-import" width="100%">
								<thead>
									<tr>
										<th>Reference #</th>
										<th>Project</th>
										<th>Date</th>
										<th>Time</th>
										<th>Driver</th>
										<th>Origin</th>
										<th>Destination</th>
									</tr>
								</thead>
								<tbody>	
								</tbody>
							</table>
						</div>
					<!--end: Datatable -->

                </div>
            </div>
        </div>

    </div>
</div>


<div class="modal fade" id="m_modal_downloadtemplate" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					Download Template
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmDownloadTemplate" action="<?php echo site_url('eforms/tripping/download_template');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-body">
				<div class="form-group m-form__group">
					<label>
						Project
					</label>
					<select class="form-control m-input m-input--square" name="project" id="projectSelect" >
					</select>
				</div>
				<div class="form-group m-form__group">
					<label>
						Driver
					</label>
					<select class="form-control m-input m-input--square" name="driver" id="driverSelect" >
					</select>
				</div>
				<div class="form-group m-form__group">
					<label>
						Origin
					</label>
					<select class="form-control m-input m-input--square" name="origin" id="originSelect" >
					</select>
				</div>
				<div class="form-group m-form__group">
					<label>
						Destination
					</label>
					<select class="form-control m-input m-input--square" name="destination" id="destinationSelect" >
					</select>
				</div>
			</div>
			<div class="modal-footer">	
				<button type="button" class="btn btn-danger btnClose" data-dismiss="modal">
					Cancel
				</button>
				<button type="submit" class="btn btn-primary btnNew" >
					Download
				</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="m_modal_downloadtemplateLink" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					Download Template Link
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<div class="modal-body">
				<a  class="btn btn-primary btnNew link" >
					Download link
				</a>
			</div>
		</div>
	</div>
</div>