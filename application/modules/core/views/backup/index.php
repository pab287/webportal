<?php $actions = $this->core_layout->getCurrentActions(); ?>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">Backup Database</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
								<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
									<i class="la la-ellipsis-h m--font-brand"></i>
								</a>
								<div class="m-dropdown__wrapper">
									<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 22.5px;"></span>
									<div class="m-dropdown__inner">
										<div class="m-dropdown__body">
											<div class="m-dropdown__content">
												<ul class="m-nav">
													<li class="m-nav__item">
														<a href="javascript:void(0)" class="m-nav__link" onclick="updateRemoteClient()">
															<i class="m-nav__link-icon flaticon-refresh"></i>
															<span class="m-nav__link-text">Update Remote Client</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-backup_database" width="100%">
							<thead>
								<tr>
									<th>Filename</th>
									<th>Created By</th>
									<th>Created Date</th>
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
<div class="modal fade" id="modal-backup_database" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Backup Database</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<i class="la la-warning"></i>  Are you sure you want to backup database? 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btnSave btn-backup_database">Yes</button>
				<button class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var _currentActions = <?php echo json_encode($actions); ?>;
var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";
var _modalBackup = $("#modal-backup_database");

var _dtTable = $("#table-backup_database").DataTable({
	dom: '<"toolbar">frtlip',
	serverSide: true,
	processing: true,
	ajax: {
		url: "<?php echo site_url("core/backup/get_collection"); ?>",
		type: "post",
		dataType: "json",
		data: {  _csrf_token : _csrf_hash }
	},
	columns: [
		{ data: "filename", width: "55%" },
		{ data: "created_by", width: "25%" },
		{ data: "created_date", width: "15%" },
		{ data: null, width: "5%", className: "text-center" },
	],
	columnDefs: [{
			data: null,
			defaultContent: "",
			targets: -1,
			orderable: false,
			render: function ( data, type, row, meta ) { return aclDatatableActions(row.id); },
		}, { 
			targets: "_all", 
			defaultContent: "", 
		}], initComplete: function(settings, json){}
});

var _htmlContent = '<button id="backup_database-new" type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-backup_database"><i class="fa fa-plus"></i> New </button>';
$("div.toolbar").html(_htmlContent);

function aclDatatableActions($id){
	if($id){
		var _actionButton ="";
		if(typeof _currentActions !== "undefined" && jQuery.inArray("download", _currentActions) !== -1){
			_actionButton += "<button type='button' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnDownload btnDownloadAction' data-id='"+$id+"'><i class='la la-download'></i></button>";				
		}
		
		if(!_actionButton){ _actionButton = "---"; }
		return _actionButton;
	}else{ return false; }
}

function updateRemoteClient(){
	$.ajax({
		url: "<?php echo site_url("core/backup/create_backup_stocks"); ?>",
		dataType: "json",
		beforeSend: function(){	mapBlockUI(); },
		success: function(json){
			if(json.response){
				_modalBackup.modal("hide");
				toastr.success(json.toastr_msg);
				_dtTable.ajax.reload();
			}else{
				toastr.error(json.toastr_msg);
			}
			mapUnblockUI();
		}
	});
}

$(document).on("click", ".btnDownloadAction", function(){
	var id = $(this).data("id");
	location.href = "<?php echo site_url("core/backup/download_backup"); ?>"+"/"+id;
});

$(document).on("click", ".btn-backup_database", function(){
	$.ajax({
		url: "<?php echo site_url("core/backup/create_backup"); ?>",
		dataType: "json",
		beforeSend: function(){ mapBlockUI() },
		success: function(json){
			if(json.response){
				_modalBackup.modal("hide");
				toastr.success(json.toastr_msg);
				_dtTable.ajax.reload();
			}else{
				toastr.error(json.toastr_msg);
			}
			mapUnblockUI();
		}
	});
});
</script>