<div id="temp_attendance" class="m-content">
	<div class="m-portlet">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						Attendance Mobile
					</h3>
				</div>
			</div>
		</div>
		<div class="m-portlet__body">
			<!--begin::Section-->
			<div class="m-section">
				<div class="m-section__content">	
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
						<table class="table table-striped table-bordered" id="table" width="100%">
							<col width="10%">
							<col width="10%">
							<col width="10%">
							<col width="5%">
							<col width="30%">
							<col width="6%">
							<col width="6%">
							<col width="5%">
							<col width="3%">
							<thead>
								<tr>
									<th>NAME</th>
									<th>POSITION</th>
									<th>DEPARTMENT</th>
									<th>BIOMETRIC</th>
									<th>LOCATION</th>
									<th>TIME</th>
									<th>DATE</th>
									<th>IMAGE</th>
									<th>ACTION</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			<!--end::Section-->
		</div>
		<!--end::Form-->
	</div>
</div>
<div id="image_view" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" onclick="clear()" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-2" id="img_view">
      </div>
    </div>
  </div>
</div>

<div id="view-map" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h5 class="modal-title" id="view-title">View Coordinate</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
			<div class="modal-body" id="view_map">
				<p><b>Location:</b> <span id="loc-name"></span></p>
				<iframe id="view-coordinate" width="100%" height="500" frameborder="0"></iframe>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var search_val = "";
	var table = $("#table").DataTable({
		dom: '<"toolbar">rtlip',
		serverSide: true,
		processing: true,
		aaSorting: [],
		ajax: {
				url: baseUrl("gcctime/temp_attendance/getattendance"),
				type: "post",
				global: false,
				dataType: "json",
				data: function(d){
					d.csrf_token = _csrf_hash,
					d.search['value'] = search_val
			}
		},
		searching: true,
		columns: [
			{ data: "name", width: "25%", render: function (data) {
					return "<strong style='color: #525252;'>"+data+"</strong>";
				}
			},
			{ data: "biometric_id", width: "20%"},
			{ data: "position", width: '10%', },
			{ data: "department", width: '10%', },
			{ data: "address", width: "25%",
				render: function(data, type, row, meta){
					var html = ``;

					html += '<div>';
						if(data != 'No Location'){
							html += `<p class="m-0">${data}</p>`;
							html += `<p class="m-0"><small><b>Coordinates: </b> ${row.latitude} - ${row.longitude}</small></p>`;
						}else{
							html += '<p class="m-0">No Location Found.</p>';
						}

						html += `<p class="m-0"><small><b>Station(s): </b> ${row.station} </small></p>`;
					html += '</div>';

					return html;
				}
			},
			{ data: "time", width: "25%", className: "text-center"},
			{ data: "date", width: "25%", className: "text-center"},
			{ data: "image", width: "10%", className: "text-center"},
			{ data: null, className: "text-center", width: "10%",
				render: function(data, type, row, meta){
					return `<button class="m-btn m-btn--hover-brand btn btn-secondary btnView" onclick="getPinnedCoordinate('${row.latitude}', '${row.longitude}', '${row.location}')" 	 				data-toggle="m-tooltip" data-original-title="View Coordinate" data-skin="dark" data-delay='{"show": 300}' style="border-radius: 50%; padding: 10px;">
							<i class="la la-map-marker"></i>
						</button>`;
				}
			}
		],
		select: {
			style:    'os',
			selector: 'td:first-child'
		},
	});

	$('#generalSearch').donetyping(function(callback) {
		search_val = $(this).val();
		table.ajax.reload();
	});

	function imgView(id){
		$.ajax({
			url: baseUrl("gcctime/temp_attendance/getImage"),
			type: "POST",
			data: {
			csrf_token: _csrf_hash,
			img_id: id
			},
			success: function(response){
				$("#image_view #img_view").html(response);
				$("#image_view").modal("show");
			}
		});
	}

	function clear(){
		$("#image_view #img_view").html();
	}

	function getPinnedCoordinate(lng, long, location){
		$("#loc-name").text(location);
		$("#view-coordinate").attr('src', `https://maps.googleapis.com/maps/embed/v1/place?q=${lng},${long}&zoom=20&maptype=satellite&key=AIzaSyCm_pTwQzhaAKspErhW9ptpubv_ATLrpgE`);

		$("#view-map").modal();
	}

</script>
 