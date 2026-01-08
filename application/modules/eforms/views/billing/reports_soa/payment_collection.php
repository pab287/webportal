<style>
    #pc_page .h-35_13 {
        height: 35.13px;
    }

    #pc_page .form-group .m-input {
        padding: 8.45px 16.25px;
    }

    /* Chrome, Safari, Edge, Opera */
    #pc_page input::-webkit-outer-spin-button,
    #pc_page input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    #pc_page input[type=number] {
        -moz-appearance: textfield;
    }

    #tbl-payment_collection .m-checkbox > span:after{
        margin-left: -3px;
        margin-top: -8px;
    }
    #tbl-payment_collection .v-middle {
        vertical-align: middle!important;
    }

    #pc_page #pc_inputs_wrapper .col-6 {
        max-width: 49.5%;
    }

    #pc_page #pc_inputs_wrapper .col-6 .row .col:last-child {
        max-width: 12%;
        flex: 0 0 12%;
    }

    #pc_page #pc_inputs_wrapper .col-6 .row .col:not(:last-child) {
        max-width: 28%;
        flex: 0 0 28%;
    }

    #pc_page #pc_inputs_wrapper .col-6 .row.alert {
        padding: 15px;
    }

    #pc_page #pc_inputs_wrapper label {
        text-transform: uppercase;
        font-weight: 500;
    }

    #pc_page .help-block.form-error {
        display: none;
    }


    .group-header {
        background: #eef3ff;
        cursor: pointer;
    }

    .payment-row {
        display: none; /* hidden until opened */
    }

    .cashier-header {
        padding: 8px;
        font-size: 15px;
    }

    #collection_cashier_list .accord_item-icon {
        position: absolute;
        top: 0;
        bottom: 0;
        margin: auto;
        height: max-content;
    }
    #collection_cashier_list .accord_item-icon i {
        transition: .3s ease-in-out;
    }
    #collection_cashier_list .accord_item-head.collapsed .accord_item-icon i {
        rotate: -90deg;
    }
    #collection_cashier_list .accord_item-head .accord_item-icon i {
        rotate: 0deg;
    }

    #payment-collected-list .accord_item:not(:last-child) {
        margin: 0 0 10px 0;
    }

    #collection_cashier_list .accord_item-head.collapsed {
        background: #f4f5f8;
        border-radius: 5px;
    }

    #collection_cashier_list .accord_item-head {
        background: #4895ef;
        position: relative;
        transition: .3s ease-in-out;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .accord_item-title {
        text-transform: uppercase;
        font-weight: 600;
        color: #737373;
    }

    .accord_item-body {
        background: #fff;
        border: 1px solid #4895ef;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        overflow: hidden;
    }

    .accord_item-content {
        max-height: 400px;
        overflow: auto;
    }

    #collection_cashier_list .accord_item-title {
        font-weight: 600;
    }
    #collection_cashier_list .accord_item-title,
    #collection_cashier_list .accord_item-icon  {
        color: #fff;
        transition: .3s ease-in-out;
    }

    #collection_cashier_list .accord_item-head.collapsed span {
        color: #737373;
    }

    #payment-collected-list .accord_item {
        cursor: pointer;
    }

    #payment-collected-list .accord_item:hover .accord_item-head.collapsed {
        background: #e0e0e1;
    }

    #total-collection .col-4 {
        max-width: 30%;
        border-radius: 5px;
    }
</style>

<div id="pc_page" class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">Payment Collection</h3>
                </div>
            </div>

            <div id="exportButtons" class="m-portlet__head-tools">
                <button @click="exportExcel('xlsx')" class="btn btn-success m-btn btnExportexcel" type="button" id="ExportExcel" data-toggle="m-tooltip" title="" data-original-title="EXPORT EXCEL">
                    <span><i class="fa fa-file-excel-o"></i></span>
                </button>

                <button @click="exportPDF" class="btn btn-danger btnExportpdf" type="button" id="ExportPDF" data-toggle="m-tooltip" title="" data-original-title="EXPORT PDF">
                    <span><i class="fa fa-file-pdf-o"></i></span>
                </button>

                <button @click="exportExcel('csv')" class="btn btn-warning btnExportexcel" type="button" id="ExportCSV" data-toggle="m-tooltip" title="" data-original-title="EXPORT CSV">
                    <span><i class="fa fa-file-excel-o"></i></span>
                </button>
            </div>
        </div>

        <div class="m-portlet__body">
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                <div id="pc_inputs_wrapper" class="row justify-content-between align-items-center mx-0 mb-4">
                    <div class="col-8 align-items-end mx-0 mb-0 alert m-alert m-alert--default">
                        <div class="row mx-0 mb-0">
                            <div class="col-6 p-0"><label class="mb-2">Payment Collector: </label></div>
                            <div class="col-4 p-0"><label class="mb-2">Date: </label></div>
                            <div class="col-2"></div>
                        </div>
                        <div id="payment-collection-app" class="row justify-content-between align-items-start mx-0 mb-0">
                            <div class="col-6 p-0">
                                <select id="employee" class="form-control" multiple data-validation="required"></select>
                            </div>

                            <div class="col-4 p-0">
                                <div class="input-group" id="date-picker">
                                    <input type="text" class="form-control m-input" placeholder="MMM DD, YYYY - MMM DD, YYYY" id="date-range" v-model="date_range" data-validation="required" autocomplete='off' style="height: 35.13px;">
                                    <span class="input-group-addon bg-white"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>

                            <div class="col-2 p-0">
                                <button class="btn btn-info w-100" @click="generateReport" :disabled="loading">
                                    <span v-if="loading">Loading...</span>
                                    <span v-else>
                                        <i class="fa fa-gears pr-2"></i>
                                        GENERATE
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="total-collection" class="col-4">
                        <div class="row justify-content-evenly align-items-center" style="justify-content: space-evenly;">
                            <div class="col-4 alert m-alert m-alert--default mb-0 py-4">
                                <h5 class="text-center m--font-boldest m--font-danger">{{ numberWithCommas(totalCancelled) }}</h5>
                                <p class="text-center m--font-bolder mb-0" style="color: #737373;">Total Cancelled</p>
                            </div>

                            <div class="col-4 alert m-alert m-alert--default mb-0 py-4">
                                <h5 class="text-center m--font-boldest m--font-brand">{{ numberWithCommas(totalPaymentCount) }}</h5>
                                <p class="text-center m--font-bolder mb-0" style="color: #737373;">Total Payment(s)</p>
                            </div>

                            <div class="col-4 alert m-alert m-alert--default mb-0 py-4">
                                <h5 class="text-center m--font-boldest m--font-success">{{ numberWithCommas(totalCollection) }}</h5>
                                <p class="text-center m--font-bolder mb-0" style="color: #737373;">Total Collection</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="payment-collected-list">
                    <div v-if="collection.length > 0" id="collection_cashier_list" class="m-accordion m-accordion--default m-accordion--solid m-accordion--section m-accordion--toggle-arrow" role="tablist">
                        <div v-for="(item, index) in collection" :key="`acc-${index}`" class="accord_item">
                            <div :id="`collection_head_${index}`" :href="`#collection_body_${index}`" class="accord_item-head py-3 px-4 collapsed" role="tab" data-toggle="collapse" aria-expanded="false">
                                <span class="accord_item-icon"><i class="la la-angle-down"></i></span>
                                <span class="accord_item-title ml-5 d-flex align-items-center">
                                    {{ item.cashier }} 
                                    <div class="mx-3">|</div> 
                                    <span class="text-white m-badge m-badge--danger m-badge--wide">Total Cancelled: {{ item.archived_count }} </span> 
                                    <div class="mx-3">|</div> 
                                    <span class="text-white m-badge m-badge--brand m-badge--wide"> Total Payment(s): {{ item.payments.length - item.archived_count }} </span>
                                    <div class="mx-3">|</div> 
                                    <span class="text-white m-badge m-badge--success m-badge--wide"> Total Collection: ₱ {{ numberWithCommas(item.total_cash) }} </span>
                                </span>
                            </div>

                            <div :id="`collection_body_${index}`" class="accord_item-body collapse" role="tabpanel" data-parent="#collection_cashier_list">
                                <div class="accord_item-content">
                                    <template v-if="item.payments.length > 0">
                                        <table class="table table-striped table-bordered mb-0" width="100%" style="font-family: roboto;">
                                            <thead>
                                                <tr class="group-header">
                                                    <th class="text-center">AR #</th>
                                                    <th class="text-center" title="Applied Payment Date">AP Date</th>
                                                    <th class="text-center">Account</th>
                                                    <th class="text-center">Bill #</th>
                                                    <th class="text-center">Payment #</th>
                                                    <th class="text-center">Type</th>
                                                    <th class="text-center" title="Receive Payment">Amount</th>
                                                    <th class="text-center">Bal. Covered</th>
                                                    <th class="text-center" title="Payment Date">Payment Date</th>
                                                    <th class="text-center">Collected By</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr v-for="(payment, p_index) in item.payments" :key="`payment-${p_index}`" :class="payment.is_archived == 1 ? 'bg-danger text-white' : ''">
                                                    <td class="text-center">{{ payment.acknowledgement_receipt }}</td>
                                                    <td class="text-center">{{ date_format(payment.applied_payment_date) }}</td>
                                                    <td class="text-center">{{ payment.account }}</td>
                                                    <td class="text-center">{{ payment.bill_ref }}</td>
                                                    <td class="text-center">{{ payment.payment_ref }}</td>
                                                    <td class="text-center">{{ payment.type }}</td>
                                                    <td class="text-right">{{ payment.received_amount }}</td>
                                                    <td class="text-right">{{ payment.balance_covered }}</td>
                                                    <td class="text-center">{{ date_format(payment.payment_date) }}</td>
                                                    <td class="text-center">{{ item.cashier }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
<!-- commte -->
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="alert m-alert--default mb-0" role="alert">
                            <p class="text-center text-muted mb-0" style="font-weight: 600;">No Collection data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>