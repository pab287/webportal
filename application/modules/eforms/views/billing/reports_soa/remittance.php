<style>
    .h-35_13 {
        height: 35.13px;
    }
    
    #filter_inputs .col-5 {
        max-width: 40%;
    }
    #filter_inputs label {
        text-transform: uppercase;
        font-weight: 500;
    }
    .v-middle {
		vertical-align: middle!important;
	}

    .created_by_group span {
        font-size: 12px;
    }

    #remarks_text span {
        max-width: 1300px;
        display: block;
        font-style: italic;
        font-weight: 400;
    }

    .remit_inputs_wrapper label {
        text-transform: uppercase;
        font-weight: 500;
        color: #8E8E93;
    }

    .remit_inputs_wrapper {
        border-radius: 10px;
        border: 1px solid #e2e2e3!important;
    }

    /* Remittance View Design */
    .remittance_details .d-label {
        font-size: 10px;
        color: #8E8E93;
        margin: 0 0 5px;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 1px;
    }

    .remittance_details .d-val {
        font-size: 14px;
        font-weight: 700;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #7f7f83;
    }

    .remittance_details .col-3 {
        max-width: 24%;
    }

    .remittance_details .info_block {
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #e2e2e3;
    }

    /* New remit modal datatable */
    #tbl-payment_collection_wrapper .dataTables_scrollHeadInner, #tbl-payment_collection_wrapper .dataTables_scrollFootInner {
        width: unset !important;
    }

    #tbl-payment_collection_wrapper table.dataTable {
        width: 100% !important;
    }

    .r-widget .r-widget_legend-bullet {
        width: 15px;
        height: 15px;
        display: inline-block;
        border-radius: 1.1rem;
        margin: 0 1rem 0.1rem 0;
    }

    tr.short-dep td,
    tr.is_archived td,
    tr.has-variance td .btn.m-btn--hover-accent:not(.btn-secondary):not(.btn-outline-light) i {
        color: #fff;
    }

    .r-widget .r-widget_legend-text {
        font-weight: 600;
        color: #5b5d67;
        font-size: 11px;
    }

    /* Daily cash report css */
    .dcr-wrap:not(:last-child) {
        margin-bottom: 10px;
    }
    .dcr-wrap {
        padding: 25px 20px 20px;
        border-radius: 10px;
    }

    .cashier-name, .cashier-collected {
        font-weight: 500;
        color: #7f7f83;
    }

    .cashier-wrap div:not(:last-child) {
        margin: 0 0 10px 0;
    }

    .total-per-cashier-wrap .dcr-wrap:not(:last-child) {
        margin: 0 0 10px 0px;
    }

    .dcr-scroller-wrap, .tpc-scroller-wrap {
        padding: 0 30px;
        max-height: 657px;
        overflow-y: auto;
        scrollbar-color: #7f7f83 transparent;
        scrollbar-width: thin;
    }

    .dcr-payment h5, .tcp-header h5 {
        margin: 0;
        color: #7f7f83;
        font-weight: 800;
    }

    .tcp-header h5 {
        font-size: 12px;
    }

    .total-per-cashier-wrap .dcr-wrap {
        background: #efeff0;
        padding: 20px;
    }

    .remit_inputs_scroll_wrap {
        max-height: 657px;
        overflow-y: auto;
        scrollbar-color: #7f7f83 transparent;
        scrollbar-width: thin;
    }

    /* View modal remittance */
    #remittance_details .remit_inputs_scroll_wrap .info_block:not(:last-child) {
        margin: 0 0 10px 0;
    }

    #modal_view_remittance .dcr-scroller-wrap, 
    #modal_view_remittance .tpc-scroller-wrap,
    #modal_view_remittance .remit_inputs_scroll_wrap {
        max-height: 575px;
    }

    /* Skeleton Loader start */
    .skeleton-box {
        padding: 16px;
        margin-bottom: 12px;
        border-radius: 6px;
    }

    .skeleton {
        position: relative;
        overflow: hidden;
        background-color: #e2e5e7;
        border-radius: 4px;
    }

    /* shimmer effect */
    .skeleton::after {
        content: "";
        position: absolute;
        top: 0;
        left: -150px;
        height: 100%;
        width: 150px;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.5),
            transparent
        );
        animation: skeleton-loading 1.2s infinite;
    }

    @keyframes skeleton-loading {
        0% {
            left: -150px;
        }
        100% {
            left: 100%;
        }
    }

    /* Sizes */
    .skeleton-title {
        width: 35%;
        height: 18px;
    }

    .skeleton-amount {
        width: 25%;
        height: 18px;
    }

    .skeleton-text {
        width: 45%;
        height: 14px;
        margin: 6px 0;
    }

    .skeleton-text.small {
        width: 25%;
    }
    /* Skeleton Loader End */
</style>

<div id="kodc_page" class="m-content">
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">Remittance Collection</h3>
                </div>
            </div>
        </div>

        <div class="m-portlet__body">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-8 d-flex align-items-center">
                    <button type="button" class="btnNew btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill text-white mr-1" id="new_remit_modal" data-toggle="modal" data-target="#modal_new_remittance">
                        <span><i class="la la-plus"></i><span>New</span></span>
                    </button>

                    <button class="btn btn-brand m-btn m-btn--icon m-btn--pill mr-4" id="billing-date-picker">
                        <span>
                            <em class="fa fa-calendar"></em>
                            <span class="selected-filter pl-3 pr-2 text-uppercase">Date Filter</span>
                        </span>
                    </button>

                    <div class="r-widget mr-4">
                        <div class="r-widget_legend d-flex align-items-center mb-1">
                            <span class="r-widget_legend-bullet m--bg-accent"></span>
                            <span class="r-widget_legend-text">EXCESS DEPOSIT</span>
                        </div>
                        <div class="r-widget_legend d-flex align-items-center mb-1">
                            <span class="r-widget_legend-bullet" style="background: #ffcd4a;"></span>
                            <span class="r-widget_legend-text">SHORT DEPOSIT</span>
                        </div>
                        <div class="r-widget_legend d-flex align-items-center">
                            <span class="r-widget_legend-bullet m--bg-danger"></span>
                            <span class="r-widget_legend-text">CANCEL DEPOSIT</span>
                        </div>
                    </div>

                    <div class="alert alert-brand alert-dismissible fade show m-alert m-alert--air m-alert--outline m-alert--outline-2x mb-0">
                        <strong>Total Collection - Deposit = <span class="m-badge m-badge--brand m-badge--wide text-white">VARIANCE</span></strong>
                    </div>
                </div>

                <div class="col-4">
                    <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                        <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                        <span class="m-input-icon__icon m-input-icon__icon--left">
                            <span>
                                <i class="la la-search"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div id="remittance_table_wrapper">
                <table class="table table-bordered table-sm" id="tbl-remittance" width="100%" style="font-family: roboto;">
                    <thead>
                        <tr>
                            <th class="text-center py-3 px-2">Ref #</th>
                            <th class="text-center py-3 px-2">Date Range</th>
                            <th class="text-center py-3 px-2">Total Collection</th>
                            <th class="text-center py-3 px-2">Deposit</th>
                            <th class="text-center py-3 px-2">Variance</th>
                            <th class="text-center py-3 px-2">Cashier</th>
                            <th class="text-center py-3 px-2">Depositor</th>
                            <th class="text-center py-3 px-2">Date Deposit</th>
                            <th class="text-center py-3 px-2">Date Log</th>
                            <th class="text-center py-3 px-2">Action</th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modal_new_remittance">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 75%">
		<div class="modal-content">
			<div class="modal-header">
                <div class="row align-items-center justify-content-between w-100">
                    <div class="col-6">
                        <h5 class="modal-title">New Remit</h5>
                    </div>
                    <div class="col-6 pr-0">
                        <button type="button" class="close pr-0" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                </div>
			</div>

            <div class="modal-body p-0">
                <div class="row justify-content-between mx-0">
                    <div class="col-4 py-5 px-0">
                        <h5 class="text-center mb-4" style="font-weight: 700; color: #7f7f83;">Remit Details</h5>

                        <div class="remit_inputs_scroll_wrap">
                            <div class="px-5 pb-1">
                                <div id="remit_filter" class="remit_inputs_wrapper m-alert m-alert--outline alert alert-metal py-4 mb-4">
                                    <div class="form-group m-form__group w-100 mb-4">
                                        <label for="employee" class="mb-2">Employee: <span class="text-danger">*</span></label>
                                        <select id="employee" class="form-control w-100" multiple></select>
                                    </div>

                                    <div class="form-group m-form__group w-100 mb-4">
                                        <label for="date-range" class="mb-2">Date: <span class="text-danger">*</span></label>
                                        <div class="input-group" id="date-picker">
                                            <input type="text" class="form-control m-input" placeholder="MMM DD, YYYY - MMM DD, YYYY" v-model="date_range_picked" autocomplete='off' style="height: 35.13px;">
                                            <span class="input-group-addon bg-white"><i class="la la-calendar-check-o"></i></span>
                                        </div>
                                    </div>

                                    <button class="btn btn-info d-block ml-auto" @click="generateReport()">
                                        <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                                    </button>
                                </div>

                                <div id="remit_inputs" class="remit_inputs_wrapper m-alert m-alert--outline alert alert-metal py-4 mb-0">   
                                    <form id="remittance_form" method="POST" class="w-100">
                                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                                        <div class="form-group m-form__group w-100 mb-4">
                                            <label for="deposit" class="mb-2">Deposit: <span class="text-danger">*</span></label>
                                            <input type="text" v-model="deposit_amount" id="deposit" class="form-control h-35_13 deposit text-right w-100" placeholder="0.00">
                                        </div>

                                        <div class="form-group m-form__group w-100 mb-4">
                                            <label for="payment_collected" class="mb-2">Payment Collected: </label>
                                            <input type="text" v-model="payment_collected" id="payment_collected" class="payment_collected form-control h-35_13 text-right w-100" placeholder="0.00" readonly disabled>
                                        </div>

                                        <div class="form-group m-form__group w-100 mb-4">
                                            <label for="variance" class="mb-2">Variance: </label>
                                            <input type="text" v-model="variance" id="variance" class="form-control h-35_13 variance text-right w-100" placeholder="0.00" readonly disabled>
                                        </div>

                                        <div class="form-group m-form__group w-100">
                                            <label for="deposit_date" class="mb-2">Deposit Date: <span class="text-danger">*</span></label>
                                            <input type="text" v-model="deposit_date" id="deposit_date" placeholder="MMM DD, YYYY" class="form-control h-35_13 deposit_date w-100">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="daily_cash_report_app" class="col-8 p-0 d-flex">
                        <div class="col-6 py-5 px-0 daily-cash-wrap" style="background: #efeff0;">
                            <h5 class="text-center mb-4" style="font-weight: 700; color: #7f7f83;">Daily Cash</h5>

                            <div class="dcr-scroller-wrap">
                                <!-- Display Data -->
                                <template v-if="daily_cash_report && daily_cash_report.length > 0">
                                    <div v-for="(item, index) in daily_cash_report" :key="index" class="dcr-wrap bg-white">
                                        <div class="dcr-payment d-flex justify-content-between align-items-center">
                                            <h5>{{ item.payment_date }}</h5> 
                                            <h5>₱ {{ numberWithCommas(item.total_payments_per_day.toFixed(2)) }}</h5>
                                        </div>

                                        <hr>

                                        <div class="cashier-wrap">
                                            <div v-for="(cashier, c_index) in item.cashier" :key="c_index" class="d-flex justify-content-between align-items-center bg-white">
                                                <span class="cashier-name">{{ cashier.cashier }}</span>
                                                <span class="cashier-collected">₱ {{ numberWithCommas(cashier.total_payments.toFixed(2)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- If no records at all -->
                                <template v-else-if="daily_cash_report && daily_cash_report.length === 0">
                                    <div class="bg-white p-4 mb-0" style="border-radius: 5px;">
                                        <p class="text-center text-muted mb-0" style="font-weight: 600;">
                                            No Records
                                        </p>
                                    </div>
                                </template>

                                <!-- SKELETON (default & while waiting) -->
                                <template v-else>
                                    <div v-for="n in 3" :key="n" class="dcr-wrap bg-white skeleton-box">
                                        <div class="dcr-payment d-flex justify-content-between align-items-center">
                                            <div class="skeleton skeleton-title"></div>
                                            <div class="skeleton skeleton-amount"></div>
                                        </div>

                                        <hr>

                                        <div class="cashier-wrap">
                                            <div v-for="i in 3" :key="i" class="d-flex justify-content-between align-items-center">
                                                <div class="skeleton skeleton-text"></div>
                                                <div class="skeleton skeleton-text small"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="col-6 py-5 px-0 total-per-cashier-wrap">
                            <h5 class="text-center mb-4" style="font-weight: 700; color: #7f7f83;">Total per cashier</h5>
                            
                            <div class="tpc-scroller-wrap">
                                <template v-if="total_per_cashier && total_per_cashier.length > 0">
                                    <div v-for="(totalPerCashier, index) in total_per_cashier" :key="index" class="dcr-wrap">
                                        <div class="tcp-header d-flex justify-content-between align-items-center">
                                            <h5>{{ totalPerCashier.cashier }}</h5> 
                                            <h5>₱ {{ numberWithCommas(totalPerCashier.total_cash) }}</h5>
                                        </div>
                                    </div>
                                </template>

                                   <!-- If no records at all -->
                                <template v-else-if="total_per_cashier && total_per_cashier.length === 0">
                                    <div class="alert m-alert--default p-4 mb-0"
                                        style="border-radius: 5px; background: #efeff0;">
                                        <p class="text-center text-muted mb-0" style="font-weight: 600;">
                                            No Records
                                        </p>
                                    </div>
                                </template>

                                <!-- SKELETON (default & while waiting) -->
                                <template v-else>
                                    <div v-for="n in 4" :key="n" class="dcr-wrap skeleton-box">
                                        <div class="tcp-header d-flex justify-content-between align-items-center">
                                            <div class="skeleton skeleton-title"></div>
                                            <div class="skeleton skeleton-amount"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div id="remit_form_btn" class="row align-items-center justify-content-end w-100 mx-0">
                    <button @click="save_remittance()" type="submit" class="btnSave btn btn-info">
                        <span><i class="fa fa-save pr-2"></i>Save</span>
                    </button>
                </div>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_view_remittance">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 70%">
		<div class="modal-content">
			<div class="modal-header">
                <div class="row align-items-center justify-content-between w-100">
                    <div class="col-6">
                        <h5 class="modal-title" id="exampleModalLabel">View Remittance</h5>
                    </div>
                    <div class="col-6 p-0">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                </div>
			</div>

            <div id="remittance_details" class="modal-body p-0">
                <div class="remittance_details">
                    <div class="row mx-0">
                        <div class="col-4 px-0 py-5">
                            <h5 class="text-center mb-4" style="font-weight: 700; color: #7f7f83;">Remit Details</h5>

                            <div class="remit_inputs_scroll_wrap px-5">
                                <div class="info_block">
                                    <p class="d-label">Reference No.</p>
                                    <p class="d-val r_ref_no" :title="ref_no">{{ ref_no }}</p>
                                </div>

                                <div class="info_block">
                                    <p class="d-label">Depositor</p>
                                    <p class="d-val r_depositor" :title="depositor">{{ depositor }}</p>
                                </div>

                                <div class="info_block">
                                    <p class="d-label">Date Deposit</p>
                                    <p class="d-val r_date_deposit" :title="date_deposit">{{ date_deposit }}</p>
                                </div>

                                <div class="info_block">
                                    <p class="d-label">Total Collection</p>
                                    <p class="d-val r_total_collection" :title="total_collection">{{ total_collection }}</p>
                                </div>

                                <div class="info_block">
                                    <p class="d-label">Deposit</p>
                                    <p class="d-val r_deposit" :title="deposit">{{ deposit }}</p>
                                </div>

                                <div class="info_block" :style="{backgroundColor: variance_color}">
                                    <p class="d-label" :style="{color: variance_label_text_color}">Variance</p>
                                    <p class="d-val r_variance" :style="{color: variance_value_text_color}" :title="variance">{{ variance }}</p>
                                </div>

                                <div class="info_block">
                                    <p class="d-label">Date Range</p>
                                    <p class="d-val r_date_range" :title="date_range">{{ date_range }}</p>
                                </div>

                                <template v-if="remarks_text != ''">
                                    <div id="remarks_wrap" class="row mx-0">
                                        <div class="col-12 mb-0 m-alert m-alert--icon m-alert--outline alert alert-warning alert-dismissible fade show" role="alert">
                                            <div class="m-alert__text r_remarks_text" :title="remarks_text">
                                                <p class="d-label text-warning">Remarks</p>    
                                                <p class="mb-0" style="font-weight: 600;">{{ remarks_text }}</p>
                                            </div>	  			  	
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="col-4 py-5 px-0 daily-cash-wrap" style="background: #efeff0;">
                            <h5 class="text-center mb-4" style="font-weight: 700; color: #7f7f83;">Daily Cash</h5>

                            <div class="dcr-scroller-wrap">
                                <!-- Display Data -->
                                <template v-if="daily_cash_report && daily_cash_report.length > 0">
                                    <div v-for="(item, index) in daily_cash_report" :key="index" class="dcr-wrap bg-white">
                                        <div class="dcr-payment d-flex justify-content-between align-items-center">
                                            <h5>{{ item.payment_date }}</h5> 
                                            <h5>₱ {{ numberWithCommas(item.total_payments_per_day.toFixed(2)) }}</h5>
                                        </div>

                                        <hr>

                                        <div class="cashier-wrap">
                                            <div v-for="(cashier, c_index) in item.cashier" :key="c_index" class="d-flex justify-content-between align-items-center bg-white">
                                                <span class="cashier-name">{{ cashier.cashier }}</span>
                                                <span class="cashier-collected">₱ {{ numberWithCommas(cashier.total_payments.toFixed(2)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- SKELETON (default & while waiting) -->
                                <template v-else-if="daily_cash_report && daily_cash_report.length === 0">
                                    <div v-for="n in 3" :key="n" class="dcr-wrap bg-white skeleton-box">
                                        <div class="dcr-payment d-flex justify-content-between align-items-center">
                                            <div class="skeleton skeleton-title"></div>
                                            <div class="skeleton skeleton-amount"></div>
                                        </div>

                                        <hr>

                                        <div class="cashier-wrap">
                                            <div v-for="i in 3" :key="i" class="d-flex justify-content-between align-items-center">
                                                <div class="skeleton skeleton-text"></div>
                                                <div class="skeleton skeleton-text small"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="col-4 py-5 px-0 total-per-cashier-wrap">
                            <h5 class="text-center mb-4" style="font-weight: 700; color: #7f7f83;">Total per cashier</h5>
                            
                            <div class="tpc-scroller-wrap">
                                <template v-if="grand_total_per_cashier && grand_total_per_cashier.length > 0">
                                    <div v-for="(totalPerCashier, index) in grand_total_per_cashier" :key="index" class="dcr-wrap">
                                        <div class="tcp-header d-flex justify-content-between align-items-center">
                                            <h5>{{ totalPerCashier.cashier }}</h5> 
                                            <h5>₱ {{ numberWithCommas(totalPerCashier.total_cash) }}</h5>
                                        </div>
                                    </div>
                                </template>

                                <!-- SKELETON (default & while waiting) -->
                                <template v-else-if="grand_total_per_cashier && grand_total_per_cashier.length === 0">
                                    <div v-for="n in 4" :key="n" class="dcr-wrap skeleton-box">
                                        <div class="tcp-header d-flex justify-content-between align-items-center">
                                            <div class="skeleton skeleton-title"></div>
                                            <div class="skeleton skeleton-amount"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="row align-items-center justify-content-end w-100 mx-0">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <span>Close</span>
                    </button>
                </div>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_remarks" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Remarks</h5>
			</div>

            <div class="modal-body">
                <small class="text-danger font-italic">* Deposit does not match the total payment collected. Please provide a remarks.</small><br>
                <small id="remarks-count" class="text-muted d-block text-right">0 / 70 characters</small>

                <textarea class="form form-control mt-2 mb-4" name="remarks" id="remarks" rows="6"></textarea>

                <div class="row align-items-center justify-content-end w-100 mx-0">
                    <input type="button" id="remarks-modal-save" class="btn btn-primary" value="SAVE">
                </div>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_archived">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<input type="hidden" name="id" id="archive_id">
			<input type="hidden" name="archive_ref_no" id="archive_ref_no">

			<div class="modal-header">
				<h5 class="modal-title">Cancel Remittance</h5>
			</div>

			<div class="modal-body" id="archive_text"></div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" onclick="cancel_remit()">Yes</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>