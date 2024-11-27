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
        <div class="row mb-4" v-if="count > 0">
            <div class="col-12 col-md-12 col-sm-12">
                <div class="row">
                    <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                        <p class="mb-0 m--font-bolder">FILTER BY:</p>
                    </div>
                    <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{filter.filter_by}}</span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                        <p class="mb-0 m--font-bolder">MONTH:</p>
                    </div>
                    <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{filter.month}}</span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                        <p class="mb-0 m--font-bolder">COVERAGE DATE:</p>
                    </div>
                    <div class="col-3 col-md-3 col-lg-3 col-sm-12">
                        <p class="mb-0 m--font-bolder"><span>{{filter.coverage_date}}</span></p>
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