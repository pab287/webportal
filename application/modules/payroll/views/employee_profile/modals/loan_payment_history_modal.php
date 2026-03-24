<div class="modal fade" id="loan-payment-history-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="m--font-boldest2">LOAN PAYMENT HISTORY</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-portlet m-portlet--bordered m-portlet--unair">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-file-1"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    Remarks
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <span id="_for_remarks"></span>
                    </div>
                </div>
                <div id="employee--loan_payment_history">
                    <ul class="nav nav-tabs nav-fill mb-0">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab_payment" data-toggle="tab" href="#tab_payments">
                                Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab_interest_charge" data-toggle="tab" href="#tab_interest_charges">
                                Interest Charges
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" style="border: 1px solid #dddddd; border-top: 0; padding: 15px">
                        <div class="tab-pane active show" id="tab_payments">
                            <div class="row">
                                <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                    <table class="table table-bordered" style="width: 100%">
                                        <thead>
                                        <tr>
                                            <th scope="col">PAYROLL CUT OFF</th>
                                            <th scope="col">POSTED BY</th>
                                            <th scope="col">AMOUNT</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                        <tr>
                                            <th scope="col" colspan="2" class="text-right">
                                                <span class="m--font-boldest">TOTAL</span>
                                            </th>
                                            <th scope="col"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_interest_charges">
                        <div class="row">
                                <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                    <table class="table table-bordered" style="width: 100%">
                                        <thead>
                                        <tr>
                                            <th scope="col">PAYROLL CUT OFF</th>
                                            <th scope="col">POSTED BY</th>
                                            <th scope="col">AMOUNT DUE</th>
                                            <th scope="col">AMOUNT PAID</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                        <tr>
                                            <th scope="col" colspan="3" class="text-right">
                                                <span class="m--font-boldest">TOTAL</span>
                                            </th>
                                            <th scope="col"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>