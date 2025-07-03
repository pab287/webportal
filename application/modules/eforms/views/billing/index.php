<script src="//www.amcharts.com/lib/3/amcharts.js" type="text/javascript"></script>
<script src="http://www.amcharts.com/lib/amcharts.js" type="text/javascript"></script>

<div class="m-content">
    <div class="row">
        <div class="col-xl-2 col-lg-2 col-md-2">
            <div class="m-portlet m-portlet--head-sm" id="waterUsage">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="row">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="la la-tint icon-xl"></i>
                                </span>
                                <h3 class="m-portlet__head-text title_subdivision">
                                    Water Usage
                                </h3>
                                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                    <a href="#" class="m-portlet__nav-link m-dropdown__toggle btnView dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">
                                        Filter
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 40.5547px;"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <template v-if="count > 0">
                                                            <li class="m-nav__item">
                                                                <a href="javascript:void(0);" class="m-nav__link btnView" @click="getUsagePerSubd('all')">
                                                                    <span class="m-nav__link-text">
                                                                        All
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <template v-for="(item,index) in collection">
                                                                <li class="m-nav__item">
                                                                    <a href="javascript:void(0);" class="m-nav__link btnView" @click="getUsagePerSubd(item.id)">
                                                                        <span class="m-nav__link-text">
                                                                            {{item.name}}
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                            </template>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body" style="width: 100%;height: 337px;padding: 0;">
                    <div style="height: 50%; width: 100%; padding: 12px;">
                        <h2 id="monthly_usage" style="text-align: right; color:#40C6BE; margin-top: 42px;">0</h2>
                        <p id="monthly_usage_msg" style="text-align: right;">Monthly usage (CBM)</p>
                    </div>
                    <hr style="margin: 0;">
                    <div style="height: 50%; width: 100%; padding: 12px;">
                        <h2 id="yearly_usage" style="text-align: right; color:#5BA5D9; margin-top: 42px;">0</h2>
                        <p id="yearly_usage_msg" style="text-align: right;">Year to date usage (CBM)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5 col-md-5">
            <div class="m-portlet m-portlet--head-sm">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="row">
                            <div class="m-portlet__head-title w-100">
                                <div class="row justify-content-between align-items-center mx-0">
                                    <div class="col-6 colmn-1">
                                        <span class="m-portlet__head-icon">
                                            <i class="la la-calendar"></i>
                                        </span>

                                        <h3 class="m-portlet__head-text title_subdivision title_total_usage">Total Usage</h3>
                                    </div>
                                    <div class="col-6 colmn-2 row mx-0 justify-content-end">
                                        <span href="#" id="datePicker_subdivision" class="btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">
                                            <span class="selected-year">Year</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="chartdiv_total_usage_previous_per_subdivision" style="width: 100%;height: 280px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5 col-md-5">
            <div class="m-portlet m-portlet--head-sm">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="row">
                            <div class="m-portlet__head-title w-100">
                                <div class="row justify-content-between align-items-center mx-0">
                                    <div class="col-10 colmn-1">
                                        <span class="m-portlet__head-icon">
                                            <i class="la la-calendar"></i>
                                        </span>

                                        <h3 class="m-portlet__head-text title_top_consumer">Top Consumer</h3>
                                    </div>
                                    <div class="col-2 colmn-2 row mx-0 justify-content-end">
                                        <span href="#" id="datePicker_top_consumer" class="btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">
                                            <span class="selected-year">Year</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="chartdiv_total_usage_top_consumer" style="width: 100%;height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-2 col-lg-2 col-md-2">
            <div class="m-portlet m-portlet--head-sm">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="row">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-line-graph"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    CU.M
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body" style="width: 100%;height: 337px;padding: 0;">
                    <div style="height: 50%; width: 100%; padding: 12px;">
                        <h2 id="customer_usage" style="text-align: right; color:#4478FF; margin-top: 42px;">0</h2>
                        <p style="text-align: right;">Customer Usage</p>
                    </div>
                    <hr style="margin: 0;">
                    <div style="height: 50%; width: 100%; padding: 12px;">
                        <h2 id="distribution_supply" style="text-align: right; color:#19ACBF; margin-top: 42px;">0</h2>
                        <p style="text-align: right;">Distributed Supply</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5 col-md-5">
            <div class="m-portlet m-portlet--head-sm" id="waterUsage_cum">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="row">
                            <div class="m-portlet__head-title w-100">
                                <input type="hidden" id="consumer_vs_supplier_date">

                                <div class="row justify-content-between align-items-center mx-0">
                                    <div class="col-6 colmn-1">
                                        <span class="m-portlet__head-icon">
                                            <i class="la la-calendar"></i>
                                        </span>

                                        <h3 class="m-portlet__head-text chartdiv_versus_title">Consumer vs. Supplier</h3>
                                    </div>

                                    <div class="col-6 colmn-2 row mx-0 justify-content-end">
                                        <div class="colmn-1 mr-3">
                                            <div style="margin-left: 12px;" class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                                <a href="#" class="m-portlet__nav-link m-dropdown__toggle btnView dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">Filter</a>

                                                <div class="m-dropdown__wrapper">
                                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 40.5547px;"></span>
                                                    <div class="m-dropdown__inner">
                                                        <div class="m-dropdown__body">
                                                            <div class="m-dropdown__content">
                                                                <ul class="m-nav">
                                                                    <template v-if="count > 0">
                                                                        <li class="m-nav__item">
                                                                            <a href="javascript:void(0);" class="m-nav__link btnView" @click="getUsagePerSubd('all')">
                                                                                <span class="m-nav__link-text">
                                                                                    All
                                                                                </span>
                                                                            </a>
                                                                        </li>
                                                                        <template v-for="(item,index) in collection">
                                                                            <li class="m-nav__item">
                                                                                <a href="javascript:void(0);" class="m-nav__link btnView" @click="getUsagePerSubd(item.id)">
                                                                                    <span class="m-nav__link-text">
                                                                                        {{item.name}}
                                                                                    </span>
                                                                                </a>
                                                                            </li>
                                                                        </template>
                                                                    </template>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="colmn-2">
                                            <span href="#" id="datePicker_consumer_vs_supplier" class="btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">
                                                <span class="selected-year">Year</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="chartdiv_versus" style="width: 100%;height: 280px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5 col-md-5">
            <div class="m-portlet m-portlet--head-sm">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="row">
                            <div class="m-portlet__head-title w-100">
                                <div class="row justify-content-between align-items-center mx-0">
                                    <div class="col-6 colmn-1">
                                        <span class="m-portlet__head-icon">
                                            <i class="la la-calendar"></i>
                                        </span>

                                        <h3 class="m-portlet__head-text title_total_payment">Total Payment</h3>
                                    </div>
                                    <div class="col-6 colmn-2 row mx-0 justify-content-end">
                                        <span href="#" id="datePicker_total_payment" class="btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">
                                            <span class="selected-year">Year</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="chartdiv_total_payment" style="width: 100%;height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-5 col-lg-5 col-md-5">
            <div class="m-portlet m-portlet--head-sm">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="row">
                            <div class="m-portlet__head-title w-100">
                                <div class="row justify-content-between align-items-center mx-0">
                                    <div class="col-6 colmn-1">
                                        <span class="m-portlet__head-icon">
                                            <i class="la la-calendar"></i>
                                        </span>

                                        <h3 class="m-portlet__head-text graph_title">Billing graph</h3>
                                    </div>
                                    <div class="col-6 colmn-2 row mx-0 justify-content-end">
                                        <span href="#" id="datePicker_billing_graph" class="btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">
                                            <span class="selected-year">Year</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="chartdiv" style="width: 100%;height: 280px;"></div>
                </div>
            </div>
        </div>
        <!-- <div class="col-xl-4 col-lg-4 col-md-4">
            <div class="m-portlet m-portlet--head-sm" style="min-height: 40em; padding-bottom: 30px;">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="la la-calendar-times-o"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Due Today
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                  <button class="m-btn btn btn-sm btn-danger" onclick="disconnectModal()"><span class="fa fa-window-close-o"></span> Disconnect Selected</button>
                  <button class="m-btn btn btn-sm btn-primary" onclick="disconnectAllModal()"><span class="fa fa-window-close-o"></span> Disconnect All</button>
                  <table class="table table-striped table-bordered table-sm table-condensed"
                          id="table-due-today"
                          width="100%">
                      <thead>
                      <tr>
                          <th>
                          <label class="m-checkbox m-checkbox--air m-checkbox--state-primary" style='padding-bottom: 5px;'>
                              <input type="checkbox" id="cb-select-all"><span></span>
                            </label>
                          </th>
                          <th>Account</th>
                          <th>Amount Due</th>
                          <th>Due Date</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                  </table>
                </div>
            </div>
        </div> -->
        <!-- <div class="col-xl-3 col-lg-3 col-md-3">
            <div class="m-portlet m-portlet--head-sm" style="min-height: 40em; padding-bottom: 30px;">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="la la-user-times"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Disconnected Accounts
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                  <table class="table table-striped table-bordered table-sm table-condensed" id="table-disconnected-accounts" width="100%">
                      <thead>
                      <tr>
                          <th>Account</th>
                          <th>DC Date</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                  </table>
                </div>
            </div>
        </div> -->
    </div>
</div>

<div class="modal fade" id="m_graph_disconnected" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_disconnected">
                    Disconnected
				</h5>
			</div>
			<div class="modal-body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered table-responsive" id="table-graph-disconnected" width="100%" style="display: inline-table;">
                    <thead>
                        <tr>
                            <th>Account No.</th>
                            <th>Account Name</th>
                            <th>Disconnected Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_graph_entries" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_total_entries">
                    Total Bill Entries
				</h5>
			</div>
			<div class="modal-body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered table-responsive" id="table-graph-entries" width="100%" style="display: inline-table;">
                    <thead>
                        <tr>
                            <th>Account No.</th>
                            <th>Account Name</th>
                            <th>Bill</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_graph_overdue" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_overdue">
                    Overdue
				</h5>
			</div>
			<div class="modal-body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered table-responsive" id="table-graph-overdue" width="100%" style="display: inline-table;">
                    <thead>
                        <tr>
                            <th>Account No.</th>
                            <th>Account Name</th>
                            <th>Bill</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_graph_usage" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_usage">
                    Total Usage
				</h5>
			</div>
			<div class="modal-body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered table-responsive" id="table-graph-usage" width="100%" style="display: inline-table;">
                    <thead>
                        <tr>
                            <th>Account No.</th>
                            <th>Account Name</th>
                            <th>Bill</th>
                            <th>Usage</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_graph_upcomingdue" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_upcoming_due">
                    Upcoming Due
				</h5>
			</div>
			<div class="modal-body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered table-responsive" id="table-graph-upcomingdue" width="100%" style="display: inline-table;">
                    <thead>
                        <tr>
                            <th>Account No.</th>
                            <th>Account Name</th>
                            <th>Bill</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_disconnect" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_upcoming_due">
          Disconnect Accounts
				</h5>
			</div>
			<div class="modal-body">
        <div>Are you sure you want to disconnect selected accounts? </div>
			</div>
			<div class="modal-footer">
        <button type="button" class="btn btn-success btnSave" onclick="disconnectSelect()">
					Confirm
				</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_disconnect_all" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title m_graph_title_upcoming_due">
          Disconnect All Overdue Accounts
				</h5>
			</div>
			<div class="modal-body">
        <div>Are you sure you want to disconnect <span class='m--font-boldest'><i>all overdue accounts?</i> </span></div>
			</div>
			<div class="modal-footer">
        <button type="button" class="btn btn-success btnSave" onclick="disconnectAllOverdue()">
					Confirm
				</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>