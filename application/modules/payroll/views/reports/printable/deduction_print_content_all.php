<style>
    @media print {
        #print-counter {
            display: block !important;
        }
    }
    #print-counter {
        display: none;
    }
</style>

<div class="m-portlet">
    <div class="m-portlet__body">
    <div id="printArea_monthly" class="printable-content contribution_deduction-content">
        <a id="printAction" href="javascript:void(0);" class="pull-right btnPrint" @click="printDivMonthly('printArea_monthly')"><i class="fa fa-print" style="font-size: 28px;"></i></a>
        <div id="header--company_title--center" class="row m--hide">
            <div class="col-12 col-md-12 col-sm-12 text-center">
                <h4 class="custom_header-title-top--center">{{filter.company_description}}</h4>
                <p class="custom_header-address-top--center m-0">{{filter.company_address}}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-sm-12">
                <h4 class="custom_header-title--center text-center">CONTRIBUTION/DEDUCTION REPORT</h4>
            </div>
        </div>
        <div class="row mt-3 mb-4" v-if="count > 0">
            <div class="col-7 col-md-7 col-sm-12 printable-width-7">
                <div class="row">
                    <div class="col-4 col-md-4 col-lg-4 col-sm-12">
                        <p class="mb-0 m--font-bolder">FILTER BY:</p>
                    </div>
                    <div class="col-8 col-md-8 col-lg-8 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{filter.filter_by}}</span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 col-md-4 col-lg-4 col-sm-12">
                        <p class="mb-0 m--font-bolder">MONTH:</p>
                    </div>
                    <div class="col-8 col-md-8 col-lg-8 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{filter.month}}</span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 col-md-4 col-lg-4 col-sm-12">
                        <p class="mb-0 m--font-bolder">COVERAGE DATE:</p>
                    </div>
                    <div class="col-8 col-md-8 col-lg-8 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{filter.coverage_date}}</span></p>
                    </div>
                </div>
                <div class="row" v-if="filter.payout_sched">
                    <div class="col-4 col-md-4 col-lg-4 col-sm-12 printable-width-2">
                        <p class="mb-0 m--font-bolder">PAYOUT SCHED:</p>
                    </div>
                    <div class="col-8 col-md-8 col-lg-8 col-sm-12 printable-width-10">
                        <p class="mb-0 m--font-bolder"><span>{{filter.payout_sched}}</span></p>
                    </div>
                </div>
                <div class="row" v-if="filter.payout_mode">
                    <div class="col-4 col-md-4 col-lg-4 col-sm-12 printable-width-2">
                        <p class="mb-0 m--font-bolder">PAYOUT MODE:</p>
                    </div>
                    <div class="col-8 col-md-8 col-lg-8 col-sm-12 printable-width-10">
                        <p class="mb-0 m--font-bolder"><span>{{filter.payout_mode}}</span></p>
                    </div>
                </div>
                <div class="row" v-if="filter.station">
                    <div class="col-4 col-md-4 col-lg-4 col-sm-12 printable-width-2">
                        <p class="mb-0 m--font-bolder">STATION:</p>
                    </div>
                    <div class="col-8 col-md-8 col-lg-8 col-sm-12 printable-width-10">
                        <p class="mb-0 m--font-bolder"><span>{{filter.station}}</span></p>
                    </div>
                </div>
            </div>
            <div id="print-counter" v-if="print_counter.count > 0" class="col-12 col-md-5 col-sm-5 printable-width-5">
                <div class="row">
                    <div class="col-5 col-md-5 col-lg-5 col-sm-12">
                        <p class="mb-0 m--font-bolder">PRINT #: </p>
                    </div>
                    <div class="col-7 col-md-7 col-lg-7 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{ print_counter.count }}</span></p>
                    </div>
                </div>
                <div class="row" v-if="print_counter.last_printed">
                    <div class="col-5 col-md-5 col-lg-5 col-sm-12">
                        <p class="mb-0 m--font-bolder">LAST PRINTED BY: </p>
                    </div>
                    <div class="col-7 col-md-7 col-lg-7 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{ print_counter.last_printed }}</span></p>
                    </div>
                </div>
                <div class="row" v-if="print_counter.last_printed_at">
                    <div class="col-5 col-md-5 col-lg-5 col-sm-12">
                        <p class="mb-0 m--font-bolder">LAST PRINTED AT: </p>
                    </div>
                    <div class="col-7 col-md-7 col-lg-7 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{ print_counter.last_printed_at }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll" id="append--table_content"></div>
            </div>
        </div>
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