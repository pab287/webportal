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

    /* #remittance_details .col-3 {
        max-width: 24.5%;
    } */

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
        background: #F4F5F9;
        padding: 30px;
        border-radius: 10px;
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
    }

    /* New remit modal datatable */
    #tbl-payment_collection_wrapper .dataTables_scrollHeadInner, #tbl-payment_collection_wrapper .dataTables_scrollFootInner {
        width: unset !important;
    }

    #tbl-payment_collection_wrapper table.dataTable {
        width: 100% !important;
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
            <div class="row justify-content-between mb-4">
                <div class="col-6">
                    <button class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill btnNew text-white" id="new_remit_modal" data-toggle="modal" data-target="#modal_new_remittance">
                      <span><i class="la la-plus"></i><span>New</span></span>
                    </button>
                </div>
            </div>

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

<div class="modal fade" id="modal_new_remittance" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="min-width: 75%">
		<div class="modal-content">
			<div class="modal-header">
                <div class="row align-items-center justify-content-between w-100">
                    <div class="col-6">
                        <h5 class="modal-title">New Remit</h5>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                </div>
			</div>

            <div class="modal-body">
                <div class="row justify-content-between align-items-end mx-0 mb-4">
                    <div id="remit_filter" class="remit_inputs_wrapper row flex-wrap justify-content-between align-items-end mb-0 mx-0 w-100">
                        <div class="col-8 px-0 emp-filter mb-4">
                            <div class="row justify-content-between align-items-end alert m-alert m-alert--default mx-0 mb-0">
                                <div class="col-5 p-0">
                                    <label for="employee" class="mb-2">Employee: <span class="text-danger">*</span></label>
                                    <select v-model="selectedEmployee" id="employee" class="form-control" data-validation="required"></select>
                                </div>

                                <div class="col-5 p-0">
                                    <label for="date-range" class="mb-2">Date: <span class="text-danger">*</span></label>
                                    <div class="input-group" id="date-picker">
                                        <input 
                                            type="text" 
                                            class="form-control m-input" 
                                            placeholder="MMM DD, YYYY - MMM DD, YYYY" 
                                            v-model="date_range_picked"
                                            data-validation="required" 
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

                        <div class="col-12 px-0 remit-inputs">
                            <form id="remittance_form" method="POST" onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                                <div class="row justify-content-between align-items-end alert m-alert m-alert--default mx-0 mb-0">
                                    <div class="col-3 col p-0 form-group m-form__group mb-0">
                                        <label for="deposit" class="mb-2">Deposit: <span class="text-danger">*</span></label>
                                        <input type="text" name="deposit" id="deposit" class="form-control h-35_13 deposit bg-white">
                                    </div>

                                    <div class="col-3 col p-0 form-group m-form__group mb-0">
                                        <label for="payment_collected" class="mb-2">Payment Collected: </label>
                                        <input type="text" name="payment_collected" id="payment_collected" class="payment_collected form-control h-35_13 bg-white" readonly>
                                    </div>

                                    <div class="col-3 col p-0 form-group m-form__group mb-0">
                                        <label for="variance" class="mb-2">Variance: </label>
                                        <input type="text" name="variance" id="variance" class="form-control h-35_13 variance bg-white" readonly>
                                    </div>

                                    <div class="col-3 col p-0 form-group m-form__group mb-0">
                                        <label for="deposit_date" class="mb-2">Deposit Date: <span class="text-danger">*</span></label>
                                        <input type="text" name="deposit_date" id="deposit_date" placeholder="MMM DD, YYYY" class="form-control h-35_13 deposit_date bg-white">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="payment_table_wrapper">
                    <table class="table table-striped table-bordered" id="tbl-payment_collection" width="100%" style="font-family: roboto;">
                        <thead>
                            <tr>
                                <th class="text-center" width="20%">Account</th>
                                <th class="text-center" width="10%">Bill #</th>
                                <th class="text-center">AR #</th>
                                <th class="text-center">Payment #</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Collected Date</th>
                                <th class="text-center">Collected By</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <div class="row align-items-center justify-content-end w-100 mx-0">
                    <button id="remit_form_btn" type="submit" class="btnSave btn btn-info">
                        <span><i class="fa fa-save pr-2"></i>Save</span>
                    </button>
                </div>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_view_remittance" tabindex="-1">
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

            <div class="modal-body">
                <div class="remittance_details mb-4">
                    <div class="row mb-3">
                        <div class="col-3">
                            <p class="d-label">Reference No.</p>
                            <p class="d-val r_ref_no"></p>
                        </div>
                        <div class="col-3">
                            <p class="d-label">Cashier</p>
                            <p class="d-val r_cashier"></p>
                        </div>
                        <div class="col-3">
                            <p class="d-label">Depositor</p>
                            <p class="d-val r_depositor"></p>
                        </div>
                        <div class="col-3">
                            <p class="d-label">Date Deposit</p>
                            <p class="d-val r_date_deposit"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-3">
                            <p class="d-label">Total Collection</p>
                            <p class="d-val r_total_collection"></p>
                        </div>
                        <div class="col-3">
                            <p class="d-label">Deposit</p>
                            <p class="d-val r_deposit"></p>
                        </div>
                        <div class="col-3">
                            <p class="d-label">Variance</p>
                            <p class="d-val r_variance"></p>
                        </div>
                        <div class="col-3">
                            <p class="d-label">Date Range</p>
                            <p class="d-val r_date_range"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p class="d-label">Remarks</p>
                            <p class="d-val r_remarks_text mb-0"></p>
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-bordered" id="remit_payment_collection" width="100%" style="font-family: roboto;">
                    <thead>
                        <tr>
                            <th class="text-center" width="20%">Account</th>
                            <th class="text-center" width="10%">Bill #</th>
                            <th class="text-center">AR #</th>
                            <th class="text-center">Payment #</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Collected Date</th>
                            <th class="text-center">Collected By</th>
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