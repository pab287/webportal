


<!-- <script  src="//code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
<script  src="//code.jquery.com/jquery-migrate-3.0.0.min.js"></script> -->
<div class="m-content">

    <div class="m-portlet ">
		<div class="m-portlet__body  m-portlet__body--no-padding">
		
			<div class="row m-row--no-padding m-row--col-separator-xl">
				<div class="col-md-12 col-lg-6 col-xl-6">

                    <button id="active_button" class="btn m-btn--square btn-default btn-block">
					<div class="m-widget24">
						<div class="m-widget24__item text-left">
							<h3 class="m-widget24__title">
								Total Documents
							</h3>
							<br>
							<span id="document" class="m-widget24__stats">
								
							</span>
							<div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
                            <div id="progress_document" class="progress-bar m--bg-primary" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<span class="m-widget24__change">
								Percent
							</span>
							<span id="percent_document" class="m-widget24__number">
								
							</span>
						</div> 
					</div>	
                    </button>
					</a>
				</div>
               
                <div class="col-md-12 col-lg-6 col-xl-6">

                    <button class="btn m-btn--square  btn-default btn-block">
					<div class="m-widget24 text-left">
						<div class="m-widget24__item">
							<h3 class="m-widget24__title">
								Recently Uploaded Documents
							</h3>
							<br>
							<span id="archive" class="m-widget24__stats">
							
							</span>
							<div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
                            <div id="progress_archive" class="progress-bar m--bg-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<span class="m-widget24__change">
								Percent
							</span>
							<span id="percent_archive" class="m-widget24__number">
							
							</span>
						</div>
					</div>
                    </button>
					</a>
				</div>
			</div>
		</div>
	</div>	
	
	
	<div class="m-portlet ">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
                   Breakdown of Documents by Classification
					</h3>
				</div>
			</div>
		</div>
		<div class="m-portlet__body  m-portlet__body--no-margin">
			<!--begin: Datatable -->
			<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
            <table class="table table-striped table-bordered" id="table-class" width="100%">
								<thead>
									<tr>
										<th>Classification</th>
                                        <th>Number of Files</th>
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

