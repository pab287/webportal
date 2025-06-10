<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<span class="m-portlet__head-icon">
                                <i class="flaticon-notes"></i>
                            </span>
							<h3 class="m-portlet__head-text" id="itemHead">
								Employment Questions Masterfile
							</h3>
						</div>
					</div>
                    <div class="m-portlet__head-tools">
                        <a class="m-nav__link btnArchive" onclick="getArchivedItems()" href="#" id="archived_items">
                            <i class="m-nav__link-icon flaticon-open-box"></i>
                            <span class="m-nav__link-text">
                                Show Archived Questions
                            </span>
                        </a>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
						<table class="table table-striped table-bordered" id="employement_questions">
							<thead>
								<tr>
                                    <th></th>
									<th>Question</th>
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