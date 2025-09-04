<div class="m-content" id="m-content" style="text-transform: uppercase;">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-2 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Open Tickets
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="openTicketPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="d-flex justify-content-center">
							<span class="d-flex flex-column align-items-center">
								<div class="m-loader m-loader--primary" v-if="!widget.open">
								</div>
								<p v-else>
									<span class="m--font-bolder" v-text="openTicketRange"></span>
									<h2 v-text="widget.open"></h2>
								</p>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Total Tickets
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="totalTicketPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="d-flex justify-content-center">
							<span class="d-flex flex-column align-items-center">
								<div class="m-loader m-loader--primary" v-if="!widget.total">
								</div>
								<p v-else>
									<span class="m--font-bolder" v-text="totalTicketRange"></span>
									<h2 v-text="widget.total"></h2>
								</p>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Urgent Tickets
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="urgentTicketPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="d-flex justify-content-center">
							<span class="d-flex flex-column align-items-center">
								<div class="m-loader m-loader--primary" v-if="!widget.urgent">
								</div>
								<p v-else>
									<span class="m--font-bolder" v-text="urgentTicketRange"></span>
									<h2 v-text="widget.urgent"></h2>
								</p>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Average Time Completed
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="aveResolveTicketPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="d-flex justify-content-center">
							<span class="d-flex flex-column align-items-center">
								<div class="m-loader m-loader--primary" v-if="!widget.aveResolve"></div>
								<template v-else>
									<div class="d-flex flex-column align-items-center">
										<span class="m--font-bolder mb-3" v-text="aveResolveRange"></span>
										<div class="d-flex justify-content-center align-items-start flex-wrap" 
											v-if="!Object.values(widget.aveResolve).every(v => v === 0)">
											<template v-for="(value, label, index) in widget.aveResolve" :key="label">
												<div class="text-center mx-2" v-if="value !== 0">
													<div class="h2 mb-1">{{ value }}</div>
													<small class="text-muted">{{ label }}</small>
												</div>
												<div class="h3 text-muted mx-1" 
													v-if="value !== 0 && index < Object.keys(widget.aveResolve).length - 1">:</div>
											</template>
										</div>
										<div v-else class="text-muted h5">DATA INVALID</div>
									</div>
								</template>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Average Response Time
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="aveResponseTicketPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="d-flex justify-content-center">
							<span class="d-flex flex-column align-items-center">
								<div class="m-loader m-loader--primary" v-if="!widget.aveResponse"></div>
								<template v-else>
									<div class="d-flex flex-column align-items-center">
										<span class="m--font-bolder mb-3" v-text="aveResponseRange"></span>
										<div class="d-flex justify-content-center align-items-start flex-wrap" 
											v-if="!Object.values(widget.aveResponse).every(v => v === 0)">
											<template v-for="(value, label, index) in widget.aveResponse" :key="label">
												<div class="text-center mx-2" v-if="value !== 0">
													<div class="h2 mb-1">{{ value }}</div>
													<small class="text-muted">{{ label }}</small>
												</div>
												<div class="h3 text-muted mx-1" 
													v-if="value !== 0 && index < Object.keys(widget.aveResponse).length - 1">:</div>
											</template>
										</div>
										<div v-else class="text-muted h5">DATA INVALID</div>
									</div>
								</template>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Total Tickets By Status
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="ticketStatusPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<span class="m--font-bolder" v-text="totalTicketByStatusRange">
						</span>
						<div class="container-fluid d-flex justify-content-center align-items-center" id="active_graph" style="height: 500px;">
							<span class="m-loader m-loader--primary m-loader--lg"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Total Tickets By Category
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="ticketCategoryPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<span class="m--font-bolder" v-text="totalTicketByCategoryRange">
						</span>
						<div class="container-fluid d-flex justify-content-center align-items-center" id="type_graph" style="height: 500px;">
							<span class="m-loader m-loader--primary m-loader--lg"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Total Tickets By Priority
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="ticketPriorityPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<span class="m--font-bolder" v-text="totalTicketByPriorityRange">
						</span>
						<div class="container-fluid d-flex justify-content-center align-items-center" id="priority_chart" style="height: 500px;">
							<span class="m-loader m-loader--primary m-loader--lg"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Ticket Completion percentage
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView" id="ticketCompletionPicker"><i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<span class="m--font-bolder" v-text="totalTicketCompletionRange">
						</span>
						<div class="container-fluid d-flex justify-content-center align-items-center" id="completion_chart" style="height: 500px;">
							<span class="m-loader m-loader--primary m-loader--lg"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title w-100" style="display: flex; justify-content: space-between;">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div class="colmn-1 col-9 p-0">
                                    <span class="m-portlet__head-icon"><i class="flaticon-graph"></i></span>
                                    <h3 class="m-portlet__head-text">TICKET REQUEST BY ASSIGNEE</h3>
                                </div>

                                <div class="colmn-2 col-3 p-0">
                                    <div class="m-select2">
                                        <form>
                                            <select id="assigneeSelect" name="select2_company" data-validation="false" class="form-control">
												<option value="all">All</option>
											</select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
						</div>
						<div class="m-portlet__head-tools">
							<a href="javascript:void(0);" id="ticketAssigneePicker" class="btn btn-brand btn-sm m-btn m-btn--icon btn-lg btnView">
								<i class="fa fa-calendar"></i>
							</a>
						</div>
					</div>
					<div class="m-portlet__body">
						<span class="m--font-bolder" v-text="totalTicketByAsigneeRange">
						</span>
						<div class="container-fluid d-flex justify-content-center align-items-center" id="assignee_chart" style="min-height: 300px; height: auto;">
							<span class="m-loader m-loader--primary m-loader--lg"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
