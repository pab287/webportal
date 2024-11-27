<div class="m-content">

			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Accounts Mobile
							</h3>
						</div>
					</div>
				</div>
				<head>
				<meta name="viewport" content="initial-scale=1.0, width=device-width" />
				<link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.0/mapsjs-ui.css?dp-version=1533195059" />
				<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-core.js"></script>
				<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-service.js"></script>
				<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-ui.js"></script>
				<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>

				</head>
				
				<div class="m-portlet__body">
					
					<!--begin::Section-->
					<div class="m-section">
						<div class="m-section__content">
				
    <div class="table-responsive">
							<table class="table table-bordered table-hover" id="table">
								<thead>
									<tr>
										<th>
											BIOMETRIC ID
										</th>
										<th>
											NAME
										</th>
                    <th>
											DEVICE NAME
										</th>
                    <th>
											DEVICE ID
										</th>
										<th>
											USERS UNIQUE ID
										</th>
										<th>
											STATUS
										</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>

</div>
						</div>
					</div>
					<!--end::Section-->
				</div>
				<!--end::Form-->
			</div>
</div>

 <script  type="text/javascript"  charset="UTF-8">
 $("#table").DataTable({
    "ajax" : "<?php echo site_url("gcctime/Mobile_accounts/getaccount");?>"
  });
</script>
