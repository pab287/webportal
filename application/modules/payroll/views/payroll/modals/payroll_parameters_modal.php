<style>
    #pay-rate-setting-container table tbody input:disabled {
        background-color: #ffffff;
        border-color: transparent;
        font-weight: 500;
    }

    #pay-rate-setting-container table tbody input {
        border: 1px solid #c1c1c1;
        outline: none;
        border-radius: 3px;
        padding: 2px 6px;
        font-weight: bold;
    }
</style>

<div class="modal fade" tabindex="-1" role="dialog"
     id="payroll-parameters-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Parameters</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="payroll_parameter-container" class="modal-body">
                <form action="<?= base_url('payroll/update_remittance_parameters') ?>"
                      id="frm-update-remittance-parameters">
                    <p class="m--regular-font-size-lg1 m--font-bolder mb-2 text-muted">GOV'T REMITTANCES SETTING</p>
                    <div id="remittances-container" class="m-widget4"></div>
                    <div class="text-right mt-4">
                        <button class="btn btn-primary m-btn m-btn--sm m--font-boldest btnUpdate" type="submit">
                            Save Changes
                        </button>
                    </div>
                </form>

                <template v-if="admin_access === true">
                    <hr class="mt-5 mb-4">
                    <form action="<?= base_url('payroll/update_sss_contribution_basis') ?>"
                            id="frm-update-sss_contribution_basis">
                        <p class="m--regular-font-size-lg1 m--font-bolder mb-2 text-muted">SSS CONTRIBUTION SETTING</p>
                        <div class="m-widget4">
                            <div class="form-group row">
                                <label class="col col-form-label">
                                    <span class="m--regular-font-size-lg1 m--font-boldest">SSS Contribution Basis</span>
                                </label>
                                <div class="col-4 text-right">
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox m--font-boldest">
                                            <input type="radio" 
                                            name="sss_contribution_basis" 
                                            value="basic_rate" 
                                            v-model="sss_contribution_basis" /> Basic Pay <span></span>
                                        </label>
                                        <label class="m-checkbox m--font-boldest">
                                            <input type="radio" 
                                            name="sss_contribution_basis" 
                                            value="gross_pay" 
                                            v-model="sss_contribution_basis" /> Gross Pay <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <button class="btn btn-primary m-btn m-btn--sm m--font-boldest btnUpdate" type="submit">
                                Save Changes
                            </button>
                        </div>
                    </form>
                    <hr class="mt-5 mb-4">
                    <form action="<?= base_url('payroll/update_zeronetpay_parameters') ?>" id="frm-update-zero_netpay">
                        <p class="m--regular-font-size-lg1 m--font-bolder mb-2 text-muted">ZERO NETPAY SETTING</p>
                        <div id="zero_netpay-container" class="m-widget4">
                            <div class="form-group row">
                                <label class="col col-form-label">
                                    <span class="m--regular-font-size-lg1 m--font-boldest">
                                        Display Zero Netpay Employee/s
                                    </span>
                                </label>
                                <div class="col-3 text-right">
                                    <span class="m-switch m-switch--outline m-switch--icon m-switch--success">
                                        <label class="mb-0">
                                            <input type="checkbox" name="enable_zero_netpay" value="1" :checked="enable_zero_netpay == 1" />
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <button class="btn btn-primary m-btn m-btn--sm m--font-boldest btnUpdate" type="submit">
                                Save Changes
                            </button>
                        </div>
                    </form>
                    <template v-if="active_tax_status === true">
                    <hr class="mt-5 mb-4">
                    <form action="<?= base_url('payroll/update_payroll_settings') ?>" id="frm-payroll-settings">
                        <p class="m--regular-font-size-lg1 m--font-bolder mb-2 text-muted">TAX SETTING</p>
                        <div id="payroll-setting-container" class="m-widget4">
                            <div class="form-group row">
                                <label class="col col-form-label">
                                    <span class="m--regular-font-size-lg1 m--font-boldest">
                                        Fixed tax monthly income switch
                                    </span>
                                </label>
                                <div class="col-3 text-right">
                                    <span class="m-switch m-switch--outline m-switch--icon m-switch--success">
                                        <label class="mb-0">
                                            <input type="hidden" name="ftmi_prop_switch" v-model="ftmi_prop_switch" />
                                            <input type="checkbox"
                                                name="fixed_tax_monthly_income_switch"
                                                value="1"
                                                v-model="ftmi_prop_switch"
                                                @click="propSwitch(event)" />
                                            <span></span>
                                        </label>
                                    </span>
                                </div>

                            </div>
                            <div class="form-group row">
                                <label class="col col-form-label">
                                    <span class="m--regular-font-size-lg1 m--font-boldest">
                                        Fixed tax monthly income
                                    </span>
                                </label>
                                <div class="col-3 text-right">
                                    <input type="text"
                                    class="form-control text-right"
                                    name="fixed_tax_monthly_income"
                                    v-model="fixed_tax_monthly_income"
                                    :disabled="ftmi_prop_switch === false? true: false" />
                                </div>
                            </div>
                            <div class="text-right mt-4">
                                <button class="btn btn-primary m-btn m-btn--sm m--font-boldest btnUpdate" type="submit">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                    </template>
                </template>
                <hr class="mt-5 mb-4">

                <form action="" id="frm-pay-rate-settings">
                    <p class="m--regular-font-size-lg1 m--font-bolder mb-2 text-muted">PAY RATE SETTING</p>
                    <div id="pay-rate-setting-container" class="m-widget4">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="vertical-align: top;">PARTICULAR</th>
                                <th width="15%" class="text-right" style="vertical-align: top;">
                                    REGULAR DAY
                                </th>
                                <th width="15%" class="text-right" style="vertical-align: top;">
                                    NIGHT DIFF.
                                </th>
                                <th width="15%" class="text-right" style="vertical-align: top;">
                                    OVERTIME
                                </th>
                                <th width="15%" class="text-right" style="vertical-align: top;">
                                    OVERTIME NIGHT DIFF.
                                </th>
                                <th width="8%" class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>