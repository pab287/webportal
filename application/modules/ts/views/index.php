<div class="m-content">
    <div class="m-portlet ">
		<div class="m-portlet__body  m-portlet__body--no-padding">
			<div class="row m-row--no-padding m-row--col-separator-xl">
				<div class="col-md-12 col-lg-6 col-xl-3">
					<a href="<?php echo base_url('ts/ticketing/tickets?id=pending');?>">
                    <button id="pending_button" class="btn m-btn--square btn-default btn-block">
					<div class="m-widget24">
						<div class="m-widget24__item text-left">
							<h3 class="m-widget24__title">
								Pending Tickets
							</h3>
							<br>
							<span id="pending" class="m-widget24__stats">
								
							</span>
							<div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
								<div id="progress_pending" class="progress-bar m--bg-warning" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<span class="m-widget24__change">
								Percent
							</span>
							<span class="m-widget24__number" id="percent_pending">
								
							</span>
						</div> 
					</div>	
                    </button>
					</a>
				</div>
                <div class="col-md-12 col-lg-6 col-xl-3">
					<a href="<?php echo base_url('ts/ticketing/tickets?id=overdue');?>">
                    <button class="btn m-btn--square  btn-default btn-block">
					<div class="m-widget24 text-left">
						<div class="m-widget24__item">
							<h3 class="m-widget24__title">
							   Overdue Tickets
							</h3>
							<br>
							<span id="overdue" class="m-widget24__stats">
								
							</span>
							<div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
								<div id="progress_overdue" class="progress-bar m--bg-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<span class="m-widget24__change">
								Percent
							</span>
							<span id="percent_overdue" class="m-widget24__number">
						
							</span>
						</div>
					</div>
                    </button>
					</a>
				</div>
				<div class="col-md-12 col-lg-6 col-xl-3">
					<a href="<?php echo base_url('ts/ticketing/tickets?id=confirm');?>">
                    <button class="btn m-btn--square  btn-default btn-block">
					<div class="m-widget24 text-left">
						<div class="m-widget24__item">
							<h3 class="m-widget24__title">
								Tickets Awaiting Confirmation
							</h3>
						    <br>
							<span id="inprogress" class="m-widget24__stats">
						
							</span>
							<div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
								<div id="progress_confirm" class="progress-bar m--bg-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<span class="m-widget24__change">
								Percent
							</span>
						    <span id="percent_confirm" class="m-widget24__number">
					
							</span>
						</div>
					</div>	
                    </button>
					</a>						
				</div>
				<div class="col-md-12 col-lg-6 col-xl-3">
					<a href="<?php echo base_url('ts/ticketing/tickets?id=all');?>">
                    <button class="btn m-btn--square  btn-default btn-block">
					<div class="m-widget24 text-left">
						<div class="m-widget24__item">
							<h3 class="m-widget24__title">
								All Tickets
							</h3>
							<br>
							<span id="all" class="m-widget24__stats">
							
							</span>
							<div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
								<div id="progress_all" class="progress-bar m--bg-info" role="progressbar" style="width:100%;"  aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<span class="m-widget24__change">
								Percent
							</span>
							<span id="percent_all" class="m-widget24__number">
								100%
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
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__body  m-portlet__body--no-padding">
                <div style="width: 100%; height: 800px;" id="chartdiv">
                </div>
            </div>
        </div>
    </div>

	<div class="m-portlet ">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						Recent Tickets
					</h3>
				</div>
			</div>
		</div>
		<div class="m-portlet__body  m-portlet__body--no-margin">
			<!--begin: Datatable -->
			<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
				<table class="table table-striped table-bordered" id="table-tickets" width="100%">
					<thead>
						<tr>
							<th>Type</th>
							<th>Department</th>
							<th>Issue</th>
							<th>Status</th>
							<th>Date Needed</th>
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