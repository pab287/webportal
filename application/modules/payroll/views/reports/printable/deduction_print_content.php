<div class="m-portlet">
    <div class="m-portlet__body">
        <div id="printArea" class="printable-content contribution_deduction-content">
            <a id="printAction" href="javascript:void(0);" class="pull-right btnPrint" @click="printContent('printArea')"><i class="fa fa-print" style="font-size: 28px;"></i></a>
            <div id="header--company_title--center" class="row m--hide">
                <div class="col-12 col-md-12 col-sm-12">
                    <h4 class="custom_header-title-top--center">{{filter.company_description}}</h4>
                    <p class="custom_header-address-top--center m-0">{{filter.company_address}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-sm-12">
                    <h4 class="custom_header-title--center">CONTRIBUTION/DEDUCTION REPORT</h4>
                </div>
            </div>
            <div class="row mb-4" v-if="count > 0">
                <div class="col-12 col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="mb-0 m--font-bolder">PAY PERIOD:</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="mb-0 m--font-bolder"><span>{{filter.pay_sequence}}</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="mb-0 m--font-bolder">PAY DATE:</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="mb-0 m--font-bolder"><span>{{filter.pay_date}}</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="mb-0 m--font-bolder">PAY COVERAGE:</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="mb-0 m--font-bolder"><span>{{filter.pay_coverage}}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <template v-if="count > 0" v-for="(item, index) in rows">
                <div class="printable-row_content" id="by_pay_date">
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="m--font-boldest">{{item.idno}}</p>
                        </div>
                        <div class="col-10 col-md-10 col-lg-10 col-sm-12">
                            <p class="m--font-boldest">{{item.employee_name}}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="header-deduction text-right">GROSS PAY</p>
                            <p class="text-right mb-0">{{rowFormatNumber(item.gross_pay)}}</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="header-deduction text-right">SSS</p>
                            <p class="text-right mb-0">{{item.sss}}</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="header-deduction text-right">SSS PROV</p>
                            <p class="text-right mb-0">{{item.sss_prov}}</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="header-deduction text-right">PHIC</p>
                            <p class="text-right mb-0">{{item.ph}}</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="header-deduction text-right">HDMF</p>
                            <p class="text-right mb-0">{{rowFormatNumber(item.hdmf)}}</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="header-deduction text-right">TAX</p>
                            <p class="text-right mb-0">{{rowFormatNumber(item.tax)}}</p>
                        </div>
                        <template v-if="item.column_count > 0" v-for="(cols, ii) in item.row_columns">
                            <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                                <p class="header-deduction text-right">{{ii.toUpperCase()}}</p>
                                <p class="text-right mb-0">{{rowFormatNumber(cols)}}</p>
                            </div>
                        </template>
                    </div>
                    <div class="row">
                        <div class="col-10 col-md-10 col-lg-10 col-sm-12">
                            <p class="text-right m--font-boldest">AMOUNT DUE</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                            <p class="text-right m--font-bolder line--amount_due">{{rowFormatNumber(item.net_pay)}}</p>
                        </div>
                    </div>
                </div>
            </template>
            <div id="footer-signature" class="printable-row_content m--hide">
                <template v-if="signature_count > 0">
                    <div class="row justify-content-center text-center">
                        <template v-for="(item, index) in signatures.meta_field">
                            <template v-if="item.is_active === true">
                                <div class="col-4 col-md-4 col-lg-4 col-sm-12 printable-signatories">
                                    <p><small class="m--font-boldest">{{item.label}}:</small></p>
                                    <p class="mb-0 pt-2 custom_footer-signature" style="margin: 3.5rem 12.5% 0 !important;"><small class="m--font-boldest">{{item.value}}</small></p>
                                </div>
                            </template>
                        </template>
                    </div>            
                </template>
            </div>
        </div>
    </div>
</div>