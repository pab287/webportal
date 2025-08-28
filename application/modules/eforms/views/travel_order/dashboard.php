<div class="m-content">
<div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
			<div class="m-portlet ">
				<div class="m-portlet__body  m-portlet__body--no-padding">
					<div class="row m-row--no-padding m-row--col-separator-xl">
						<div class="col-md-12 col-lg-6 col-xl-4">
							<div class="m-widget24">
								<div class="m-widget24__item">
									<h3 class="m-widget24__title">
										Most Traveled Personnel
									</h3>
									<br>
									<h5 id="shipp" class="m-widget24__desc">
										
									</h5>
									<span id="countsl" class="m-widget24__stats m--font-brand">
						
									</span>
									<div class="m--space-10"></div>
									<div class="progress m-progress--sm">
										<div id="p1" class="progress-bar m--bg-brand" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<span class="m-widget24__change">
										
									</span>
									<span id="countstp" class="m-widget24__number">
								
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 col-xl-4">
							<div class="m-widget24">
								<div class="m-widget24__item">
									<h3 class="m-widget24__title">
                                        Most Traveled Vehicle
									</h3>
									<br>
									<h5 id="shipv" class="m-widget24__desc">
							
									</h5>
									<span id="countst" class="m-widget24__stats m--font-danger">
									
									</span>
									<div class="m--space-10"></div>
									<div class="progress m-progress--sm">
										<div id="p2" class="progress-bar m--bg-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<span class="m-widget24__change">
									
									</span>
									<span class="m-widget24__number">
									
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 col-xl-4">
							<div class="m-widget24">
								<div class="m-widget24__item">
									<h3 class="m-widget24__title">
                                        Most Traveled Destination
									</h3>
									<br>
									<h5 id="shipd" class="m-widget24__desc">
										
									</h5>
									<span id="countsi" class="m-widget24__stats m--font-success">
										
									</span>
									<div class="m--space-10"></div>
									<div class="progress m-progress--sm">
										<div id="p3" class="progress-bar m--bg-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<span class="m-widget24__change">
									
									</span>
									<span id="countslp" class="m-widget24__number">
									
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-line-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Analytics
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="toAnalyticsPicker"><i class="fa fa-calendar"></i>
                        </a>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <span class="m--font-bolder" id="analyticsLabel">
                        ALL TIME
                    </span>
                    <div id="chartdiv" style="width: 100%;height: 500px;">
                        <div class="d-flex justify-content-center align-items-center w-100 h-100">
                            <div class="text-center">
                            <div class="mt-2 text-muted">Loading chart...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-calendar-2"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                CREATED TRAVEL ORDER &nbsp;
                            </h3>
                            <h3 class="m-portlet__head-text" id="createdToLabel">
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="createdTablePicker"><i class="fa fa-calendar"></i>
                        </a>
                    </div>
                </div>
                <div class="pb-3">
                    <div class="m-portlet__body table-responsive-sm pb-0">
                        <table class="table table-striped table-bordered"
                            id="table-travel-today"
                            width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Reference No</th>
                                <th>File Under</th>
                                <th>Driver & Vehicle</th>
                                <th>Personnel</th>
                                <th>Destination</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-calendar-2"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                DEPARTING TRAVEL ORDER &nbsp;
                            </h3>
                            <h3 class="m-portlet__head-text" id="departingToLabel">
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="departingTablePicker"><i class="fa fa-calendar"></i>
                        </a>
                    </div>
                </div>
                <div class="pb-3">
                    <div class="m-portlet__body table-responsive-sm pb-0">
                        <table class="table table-striped table-bordered" 
                            id="table-travel-weekly"
                            width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Reference No</th>
                                <th>File Under</th>
                                <th>Driver & Vehicle</th>
                                <th>Personnel</th>
                                <th>Destination</th>
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
    <div class="row" id="m-content">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-truck"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Total Approved Travel Orders
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="approveToPicker"><i class="fa fa-calendar"></i>
                        </a>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <span class="m--font-bolder" v-text="pieChartLabel">
                        ALL TIME
                    </span>
                    <div id="approvedPieChart" style="width: 100%;height: 500px;">
                        <div class="d-flex justify-content-center align-items-center w-100 h-100">
                            <div class="text-center">
                            <div class="mt-2 text-muted">Loading chart...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>