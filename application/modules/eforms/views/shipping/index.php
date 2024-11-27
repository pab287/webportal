<div class="m-content">
	<div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
			<div class="m-portlet ">
				<div class="m-portlet__body  m-portlet__body--no-padding">
					<div class="row m-row--no-padding m-row--col-separator-xl">
						<div class="col-md-12 col-lg-6 col-xl-4 col-sm-12 col-xs-12">
							<div class="m-widget24">
								<div class="m-widget24__item">
									<h3 class="m-widget24__title">
										Most Shipped to Reciever
									</h3>
									<br>
									<h5 id="shipto" class="m-widget24__desc">
										
									</h5>
									<span id="countst" class="m-widget24__stats m--font-brand">
						
									</span>
									<div class="m--space-10"></div>
									<div class="progress m-progress--sm">
										<div  id="p3" class="progress-bar m--bg-brand" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<span class="m-widget24__change">
										
									</span>
									<span id="countstp" class="m-widget24__number">
								
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 col-xl-4 col-sm-12 col-xs-12">
							<div class="m-widget24">
								<div class="m-widget24__item">
									<h3 class="m-widget24__title">
										Most Shipped Item
									</h3>
									<br>
									<span id="shipitid" class="m-widget24__desc">
							
									</span>
									<span id="shipitdes" style="margin-left:0px;" class="m-widget24__desc">
							
									</span>
                                    <br>
									<span id="countsi" class="m-widget24__stats m--font-danger">
									
									</span>
									<div class="m--space-10"></div>
									<div class="progress m-progress--sm">
										<div  id="p2" class="progress-bar m--bg-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<span class="m-widget24__change">
									
									</span>
									<span class="m-widget24__number">
									
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 col-xl-4 col-sm-12 col-xs-12">
							<div class="m-widget24">
								<div class="m-widget24__item">
									<h3 class="m-widget24__title">
										Most Shipped To Location
									</h3>
									<br>
									<h5 id="shiptoloc" class="m-widget24__desc">
										
									</h5>
									<span id="countsl" class="m-widget24__stats m--font-success">
										
									</span>
									<div class="m--space-10"></div>
									<div class="progress m-progress--sm">
										<div id="p1" class="progress-bar m--bg-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
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
                </div>
                <div class="m-portlet__body">
                    <div id="chartdiv" style="width: 100%;height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar-2"></i>
                    </span>
                            <h3 class="m-portlet__head-text">
                                Today
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body table-responsive table-responsive-sm">
                    <table class="table table-striped table-bordered"
                           id="table-shipping-today"
                           width="100%">
                        <thead>
                        <tr>
                            <th>Shipping Advice #</th>
                            <th>File Under</th>
                            <th>Ship To</th>
                            <th>Item</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar-2"></i>
                    </span>
                            <h3 class="m-portlet__head-text">
                                THIS WEEK
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body table-responsive table-responsive-sm">
                    <table class="table table-striped table-bordered"
                           id="table-shipping-weekly"
                           width="100%">
                        <thead>
                        <tr>
                            <th>Shipping Advice #</th>
                            <th>File Under</th>
                            <th>Ship To</th>
                            <th>Item</th>
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