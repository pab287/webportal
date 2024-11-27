<style>
.m-form .m-form__group:first-child{ padding-top: 15px;}
</style>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Search Tripping
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
                </div>
                <form id="frmSearchTripping" action="<?php echo site_url('eforms/tripping/generate_report'); ?>" class="m-form m-form--fit m-form--label-align-right">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet__body">
                    <div class="row">
                        <div class="form-group m-form__group col-xs-12 col-md-4">
                            <label>
                                Select Date Range
                            </label>
                                <input type="text" class="form-control" id="m_daterangepicker_1" readonly="" name="date" >
                        </div>
                        <div class="form-group m-form__group col-xs-12 col-md-3">
                            <label>
                                Select Driver
                            </label>
                                <select class="form-control m-input m-input--square" name="driver_code" id="driverSelect" >
                                </select>
                        </div>
                        <div class="form-group m-form__group col-xs-12 col-md-3">
                            <label>
                                Select Project
                            </label>
                                <select class="form-control m-input m-input--square" name="project_code" id="projectSelect" >
                                </select>
                        </div>
                        <div class="form-group m-form__group col-xs-12 col-md-2">
                        <label>
                                &nbsp;
                            </label>
                            <button type="submit" class="btn btn-primary btnSave" >
                                Search
                            </button>
                        </div>
                    </div>
                </div>
				</form>
            </div>
        </div>

        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Search Results
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">

                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="table-report" width="100%">
							<thead>
								<tr>
                                    <th>Reference #</th>
                                    <th>Project</th>
                                    <th>Date</th>
                                    <th>Period</th>
                                    <th>Driver</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th>Rate</th>
                                </tr>
							</thead>
					    	<tbody>	
					        </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" style="text-align:right">Total:</th>
                                    <th></th>
                                </tr>
                            </tfoot>
						</table>
					</div>
                </div>
            </div>
        </div>

    </div>
</div>