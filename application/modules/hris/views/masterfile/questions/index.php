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
									<th>Statement</th>
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

<div class="modal fade" id="modal-questions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form id="update_item">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Update Statement</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="statement">Question</label>
						<textarea class="form-control" id="new_question" name="question" rows="3" data-validation="required"></textarea>
					</div>
					<div class="form-group">
						<label for="statement">Statement</label>
						<textarea class="form-control" id="new_statement" name="statement" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary btnEdit">Update</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="new-modal-questions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form id="new_question_form">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">New Question</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="statement">Question</label>
						<textarea class="form-control" id="question" name="question" rows="3" data-validation="required"></textarea>
					</div>
					<div class="form-group">
						<label for="statement">Statement</label>
						<textarea class="form-control" id="statement" name="statement" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary btnSave">Save changes</button>
				</div>
			</div>
		</form>
	</div>
</div>