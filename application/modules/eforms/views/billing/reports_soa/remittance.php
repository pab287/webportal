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

    .remit_inputs_wrapper .col-6 .row.alert {
        padding: 15px;
    }

    .remit_inputs_wrapper label {
        text-transform: uppercase;
        font-weight: 500;
    }

    .emp-filter .col-5 {
        max-width: 40%;
    }

    .remit-inputs .col-3 {
        max-width: 24%;
    }

    /* Remittance View Design */
    .remittance_details {
        padding: 10px 0px;
    }

    .remittance_details .d-label {
        font-size: 12px;
        color: #484848;
        margin: 0 0 5px;
        text-transform: capitalize;
    }

    .remittance_details .d-val {
        font-size: 13px;
        font-weight: 600;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .remittance_details .col-3 {
        max-width: 24%;
    }

    .remittance_details .info_block {
        background: #F4F5F9;
        padding: 15px;
        border-radius: 10px;
    }

    /* New remit modal datatable */
    #tbl-payment_collection_wrapper .dataTables_scrollHeadInner, #tbl-payment_collection_wrapper .dataTables_scrollFootInner {
        width: unset !important;
    }

    #tbl-payment_collection_wrapper table.dataTable {
        width: 100% !important;
    }

    #remit_daily_collection_wrapper .dataTables_scrollHead table {
        min-width: max-content!important;
    }

    .r-widget .r-widget_legend-bullet {
        width: 15px;
        height: 15px;
        display: inline-block;
        border-radius: 1.1rem;
        margin: 0 1rem 0.1rem 0;
    }

    tr.short-dep td,
    tr.has-variance td .btn.m-btn--hover-accent:not(.btn-secondary):not(.btn-outline-light) i {
        color: #fff;
    }

    .r-widget .r-widget_legend-text {
        font-weight: 600;
        color: #5b5d67;
        font-size: 11px;
    }
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
                    <button class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill btnNew text-white mr-1" id="new_remit_modal" data-toggle="modal" data-target="#modal_new_remittance">
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
                        <div class="r-widget_legend d-flex align-items-center">
                            <span class="r-widget_legend-bullet m--bg-danger"></span>
                            <span class="r-widget_legend-text">SHORT DEPOSIT</span>
                        </div>
                    </div>

                    <div class="alert alert-warning alert-dismissible fade show m-alert m-alert--air m-alert--outline m-alert--outline-2x mb-0">
                        <strong>Total Collection - Deposit = <span class="m-badge m-badge--warning m-badge--wide text-white">VARIANCE</span></strong>
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

            <div class="modal-body">
                <div class="row justify-content-between align-items-end mx-0 mb-4">
                    <div id="remit_filter" class="remit_inputs_wrapper row flex-wrap justify-content-between align-items-end mb-0 mx-0 w-100">
                        <div class="col-8 px-0 emp-filter">
                            <div class="row justify-content-between align-items-end alert m-alert m-alert--default mx-0 mb-0">
                                <div class="col-5 p-0">
                                    <label for="employee" class="mb-2">Employee: <span class="text-danger">*</span></label>
                                    <select v-model="selectedEmployee" id="employee" class="form-control"></select>
                                </div>

                                <div class="col-5 p-0">
                                    <label for="date-range" class="mb-2">Date: <span class="text-danger">*</span></label>
                                    <div class="input-group" id="date-picker">
                                        <input 
                                            type="text" 
                                            class="form-control m-input" 
                                            placeholder="MMM DD, YYYY - MMM DD, YYYY" 
                                            v-model="date_range_picked"
                                            autocomplete='off' 
                                            style="height: 35.13px;"
                                        >
                                        <span class="input-group-addon bg-white"><i class="la la-calendar-check-o"></i></span>
                                    </div>
                                </div>

                                <div class="col-2 p-0">
                                    <button class="btn btn-info w-100" @click="generateReport()">
                                        <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="payment_table_wrapper" class="mb-4">
                    <table class="table" id="tbl-payment_collection" width="100%" style="font-family: roboto;">
                        <thead>
                            <tr>
                                <th class="text-center" width="25%">Date Collected</th>
                                <th class="text-center" width="25%">Payment Collected</th>
                                <th class="text-center" width="25%">Total Balance Covered</th>
                                <th class="text-center" width="25%">Collected By</th>
                            </tr>
                        </thead>

                        <tbody></tbody>

                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div id="remit_inputs" class="remit_inputs_wrapper row flex-wrap justify-content-between align-items-end mb-0 mx-0 w-100">   
                    <div class="col-12 px-0 remit-inputs">
                        <form id="remittance_form" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                            <div class="row justify-content-between align-items-end alert m-alert m-alert--default mx-0 mb-0">
                                <div class="col-3 col p-0 form-group m-form__group mb-0">
                                    <label for="deposit" class="mb-2">Deposit: <span class="text-danger">*</span></label>
                                    <input type="text" v-model="deposit_amount" id="deposit" class="form-control h-35_13 deposit bg-white text-right" placeholder="0.00">
                                </div>

                                <div class="col-3 col p-0 form-group m-form__group mb-0">
                                    <label for="payment_collected" class="mb-2">Payment Collected: </label>
                                    <input type="text" v-model="payment_collected" id="payment_collected" class="payment_collected form-control h-35_13 bg-white text-right" placeholder="0.00" readonly>
                                </div>

                                <div class="col-3 col p-0 form-group m-form__group mb-0">
                                    <label for="variance" class="mb-2">Variance: </label>
                                    <input type="text" v-model="variance" id="variance" class="form-control h-35_13 variance bg-white text-right" placeholder="0.00" readonly>
                                </div>

                                <div class="col-3 col p-0 form-group m-form__group mb-0">
                                    <label for="deposit_date" class="mb-2">Deposit Date: <span class="text-danger">*</span></label>
                                    <input type="text" v-model="deposit_date" id="deposit_date" placeholder="MMM DD, YYYY" class="form-control h-35_13 deposit_date bg-white">
                                </div>
                            </div>
                        </form>
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

            <div id="remittance_details" class="modal-body">
                <div class="remittance_details">
                    <div class="row justify-content-between mb-3 mx-0">
                        <div class="col-3 info_block">
                            <p class="d-label">Reference No.</p>
                            <p class="d-val r_ref_no" :title="ref_no">{{ ref_no }}</p>
                        </div>
                        <div class="col-3 info_block">
                            <p class="d-label">Cashier</p>
                            <p class="d-val r_cashier" :title="cashier">{{ cashier }}</p>
                        </div>
                        <div class="col-3 info_block">
                            <p class="d-label">Depositor</p>
                            <p class="d-val r_depositor" :title="depositor">{{ depositor }}</p>
                        </div>
                        <div class="col-3 info_block">
                            <p class="d-label">Date Deposit</p>
                            <p class="d-val r_date_deposit" :title="date_deposit">{{ date_deposit }}</p>
                        </div>
                    </div>
                    <div class="row justify-content-between mb-3 mx-0">
                        <div class="col-3 info_block">
                            <p class="d-label">Total Collection</p>
                            <p class="d-val r_total_collection" :title="total_collection">{{ total_collection }}</p>
                        </div>
                        <div class="col-3 info_block">
                            <p class="d-label">Deposit</p>
                            <p class="d-val r_deposit" :title="deposit">{{ deposit }}</p>
                        </div>
                        <div class="col-3 info_block" :style="{backgroundColor: variance_color}">
                            <p class="d-label" :style="{color: variance_label_text_color}">Variance</p>
                            <p class="d-val r_variance" :style="{color: variance_value_text_color}" :title="variance">{{ variance }}</p>
                        </div>
                        <div class="col-3 info_block">
                            <p class="d-label">Date Range</p>
                            <p class="d-val r_date_range" :title="date_range">{{ date_range }}</p>
                        </div>
                    </div>

                    <template v-if="remarks_text != ''">
                        <div id="remarks_wrap" class="row mx-0">
                            <div class="col-12 m-alert m-alert--icon m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="m-alert__icon">
                                    <i class="la la-warning"></i>
                                </div>
                                <div class="m-alert__text r_remarks_text" :title="remarks_text">{{ remarks_text }}</div>	  			  	
                            </div>
                        </div>
                    </template>
                </div>

                <table class="table" id="remit_daily_collection" style="font-family: roboto;">
                    <thead>
                        <tr>
                            <th class="text-center" width="25%">Date Collected</th>
                            <th class="text-center" width="25%">Payment Collected</th>
                            <th class="text-center" width="25%">Total Balance Covered</th>
                            <th class="text-center" width="25%">Collected By</th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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
				<h5 class="modal-title">Archive Remittance</h5>
			</div>

			<div class="modal-body" id="archive_text"></div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" onclick="archiveBill()">Archive</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>