<div id="app_users" class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Mobile Application Users
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
							
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
								<div class="m-separator m-separator--dashed d-xl-none"></div>
							</div>
						</div>
						<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
							<div class="row align-items-center">
								<div class="col-xl-8 order-2 order-xl-1">
									
								</div>
							</div>
						</div>
					</div>
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="app_users_tbl" width="100%">
							<thead>
								<tr>
									<th>User</th>
									<th>Device Name</th>
									<th>Device ID</th>
									<th>Status</th>
									<th>Actions</th>
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

<div class="modal fade" id="m_archived" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
            <input type="hidden" name="id" id="archive_id">
			<div class="modal-header">
				<h5 class="modal-title" style="font-weight: bold; color: #5e5e5e;">
					Archive User
				</h5>
			</div>
			<div class="modal-body" id="archive_text">
				
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" style="font-weight: bold;" onclick="archiveUser()">
					Archive
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_signout" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
            <input type="hidden" name="id" id="id">
			<div class="modal-header">
				<h5 class="modal-title" style="font-weight: bold; color: #5e5e5e;">
					Sign Out User
				</h5>
			</div>
			<div class="modal-body" id="signout_text">
				
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-warning btnSave" style="color: white; font-weight: bold;" onclick="signOutUser()">
					Sign Out
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<div id="edit_app_user_modal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<form id="appuser_edit_form">
			<div class="form-group">
				<label for="exampleInputEmail1">Email address</label>
				<input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
				<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
			</div>
			<div class="form-group">
				<label for="exampleInputPassword1">Password</label>
				<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
			</div>
			<div class="form-check">
				<input type="checkbox" class="form-check-input" id="exampleCheck1">
				<label class="form-check-label" for="exampleCheck1">Check me out</label>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

	var search_val = "";
	var query_builder = "";
	var appuser_tbl = $("#app_users_tbl").DataTable({
		dom: '<"toolbar">rtlip',
		serverSide: true,
		processing: true,
		aaSorting: [],
		ajax: {
				url: baseUrl("gcctime/App_users/getUsersLogin"),
				type: "post",
				global: false,
				dataType: "json",
				data: function(d){
				d.csrf_token = _csrf_hash,
				d.search['value'] = search_val,
				d.query_builder = query_builder
			}
		},
		searching: true,
		columns: [
			{ data: "employee_name", width: "20%", render: function (data) {
					return "<strong style='color: #525252;'>"+data+"</strong>";
				}
			},
			{ data: "device_name", width: "20%"},
			{ data: "device_id", width: "25%"},
			{ data: "status", width: "8%", className: "text-center", render: function (data) {
					return renderStatus(data);
				}
			},
			{ data: null, width: "5%", className: "text-center"},
		],
		columnDefs: [
			{
				data: null,
				defaultContent: "",
				targets: -1,
				orderable: false,
				render: function ( data, type, row, meta ) { return itemDatatableActions(row); },
			}
		],
		select: {
			style:    'os',
			selector: 'td:first-child'
		},
	});

	function itemDatatableActions(row){
		if(row){
		var tempHtml = "---";
		var tempActions = [];
		var currentActions = ["sign_out", "delete"];
		$.each(currentActions, function(index, value){
			tempActions.push(value);
		});

		tempHtml = `<div class="dropdown">
				<a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
					<i class="la la-ellipsis-h"></i>
				</a>
				<div class="dropdown-menu dropdown-menu-right">`;
			$.each(tempActions, function(ii, vv){
				switch(vv){
					case "sign_out":
						if(row.status == 2){
							tempHtml += `<a class="dropdown-item" href="javascript:void(0);" onclick='modalSignout(`+ row.id +`,`+`\"` + row.employee_name + `\")'><i class="la la-sign-out"></i> Sign Out</a>`;
						}
					break;
					case "delete":
						tempHtml += `<a class="dropdown-item " style="color: #FF8383;" href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+`\"` + row.employee_name + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
					break;
				}
			});
		tempHtml += `</div></div>`;
		return tempHtml;
		}else{ return false; }
	}

	function renderStatus(data) {
		if(data == 2){
			return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>Signed In</strong></div>';
		} else {
			return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Signed out</strong></div>';
		}
	}

	$('#generalSearch').donetyping(function(callback) {
		search_val = $(this).val();
		appuser_tbl.ajax.reload();
	});

	function modalArchive(id, employee_name){
		const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${employee_name}</strong>?</p>`;
		$('#m_archived').modal('show');
		$('#archive_text').empty().html(temp);
		$("#m_archived input[name=id]").val(id);
	}

	function archiveUser(){
		var id = document.getElementById('archive_id').value;
		$.ajax({
			url: baseUrl("gcctime/App_users/deleteAppUser"),
			type: "POST",
			data: {
			csrf_token: _csrf_hash,
			app_user_id: id
			},
			success: function(response){
				if(response = 1){
              		$('#m_archived').modal('hide');
					appuser_tbl.ajax.reload();
				}
			},
			error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection Error");
			}
		});
	}

	function modalSignout(id, employee_name){
		const temp = `<p>Are you sure you wan't to sign out <strong class='m--font-boldest'>${employee_name}</strong>?</p>`;
		$('#m_signout').modal('show');
		$('#signout_text').empty().html(temp);
		$("#m_signout input[name=id]").val(id);
	}

	function signOutUser(){
		var id = document.getElementById('id').value;
		$.ajax({
			url: baseUrl("gcctime/App_users/signOutAppUser"),
			type: "POST",
			data: {
			csrf_token: _csrf_hash,
			app_user_id: id
			},
			success: function(response){
				if(response = 1){
              		$('#m_signout').modal('hide');
					appuser_tbl.ajax.reload();
				}
			},
			error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection Error");
			}
		});
	}

</script>