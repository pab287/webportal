<div class="m-portlet">
    <div class="m-portlet__body">
        <div id="printArea" class="printable-content contribution_deduction-content">
            <a id="printAction" href="javascript:void(0);" class="pull-right btnPrint" @click="printContent('printArea')"><i class="fa fa-print" style="font-size: 28px;"></i></a>
            <div id="header--company_title--center" class="row m--hide">
                <div class="col-12 col-md-12 col-sm-12 printable-width-12">
                    <h4 class="custom_header-title-top--center">{{filter.company_description}}</h4>
                    <p class="custom_header-address-top--center m-0">{{filter.company_address}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-sm-12 printable-width-12">
                    <h4 class="custom_header-title--center">PAYROLL SHEET - CONTRIBUTION/DEDUCTION REPORT</h4>
                </div>
            </div>
            <div id="font-size-header--column" class="row mb-4" v-if="count > 0">
                <div class="col-12 col-md-12 col-sm-12 printable-width-12">
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12 printable-width-2">
                            <p class="mb-0 m--font-bolder">PAY PERIOD:</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12 printable-width-2">
                            <p class="mb-0 m--font-bolder"><span>{{filter.pay_sequence}}</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12 printable-width-2">
                            <p class="mb-0 m--font-bolder">PAY DATE:</p>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12 printable-width-2">
                            <p class="mb-0 m--font-bolder"><span>{{filter.pay_date}}</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12 printable-width-2">
                            <p class="mb-0 m--font-bolder">PAY COVERAGE:</p>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 col-sm-12 printable-width-4">
                            <p class="mb-0 m--font-bolder"><span>{{filter.pay_coverage}}</span></p>
                        </div>
                    </div>
                    <div class="row" v-if="filter.payroll_group">
                        <div class="col-2 col-md-2 col-lg-2 col-sm-12 printable-width-2">
                            <p class="mb-0 m--font-bolder">PAYROLL GROUP:</p>
                        </div>
                        <div class="col-10 col-md-10 col-lg-10 col-sm-12 printable-width-10">
                            <p class="mb-0 m--font-bolder"><span>{{filter.payroll_group}}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-if="count > 0">
                <div class="col-md-12">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll" id="append--table_content-payroll_sheet">
                        <table width="100%" border='1' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;'>
                            <colgroup>
                                <col width="4%">
                                <col width="*">
                                <col width="5%">
                                <col width="5%">
                                <col width="5%">
                                <col width="5%">
                                <col width="6%">
                                <col width="6%" v-if="column_count > 0" v-for="headers in row_columns">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th class="text-center">SSS</th>
                                    <th class="text-center">SSS PROV</th>
                                    <th class="text-center">PHIC</th>
                                    <th class="text-center">HDMF</th>
                                    <th class="text-center">TAX</th>
                                    <th class="text-center" v-if="column_count > 0" v-for="header in row_columns">
                                        <template v-if="header == 'cal.'">SSS CAL</template>
                                        <template v-else-if="header == 'cal'">HDMF CAL</template>
                                        <template v-else>{{renderColumnLabel(header)}}</template>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="count > 0" v-for="(item, index) in rows">
                                    <td class="text-center">{{ctrCount(index)}}</td>
                                    <td>{{item.employee_name}}</td>
                                    <td class="text-right">{{item.sss}}</td>
                                    <td class="text-right">{{item.sss_prov}}</td>
                                    <td class="text-right">{{item.ph}}</td>
                                    <td class="text-right">{{rowFormatNumber(item.hdmf)}}</td>
                                    <td class="text-right">{{rowFormatNumber(item.tax)}}</td>
                                    <td class="text-right" 
                                        v-if="item.column_count > 0" 
                                        v-for="(cols, ii) in item.row_columns">
                                        {{rowFormatNumber(cols)}}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-right" colspan="2">GRAND TOTAL</th>
                                    <th class="text-right" v-for="(item, index) in grand_total_footer">{{rowFormatNumber(item)}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll" id="append--table_content-payroll_sheet_grand_total">
                        <table width="100%" border='1' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;'>
                            <colgroup>
                                <col width="12.5%">    
                                <col width="12.5%">    
                                <col width="12.5%">    
                                <col width="12.5%">    
                                <col width="12.5%">    
                                <col width="12.5%">    
                                <col width="12.5%">    
                                <col width="12.5%">    
                            </colgroup>
                            <thead>
                                <tr>
                                    <th colspan="8" class="text-center">GRAND TOTAL</th>
                                </tr>
                                <tr>
                                    <th class="text-center">OT</th>
                                    <th class="text-center">N.DIFF</th>
                                    <th class="text-center">HOLIDAY</th>
                                    <th class="text-center">BASIC</th>
                                    <th class="text-center">ALLOWANCES</th>
                                    <th class="text-center">ADJUSTMENTS</th>
                                    <th class="text-center">GROSS PAY</th>
                                    <th class="text-center">NET PAY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.ot_amount)}}</td>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.ot_ndiff_amount)}}</td>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.holiday_amount)}}</td>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.basic_rate)}}</td>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.allowances)}}</td>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.adjustments)}}</td>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.gross_pay)}}</td>
                                    <td class="text-right m--font-bolder">{{rowFormatNumber(grand_total.net_pay)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="footer-signature" class="printable-row_content m--hide">
                <template v-if="signature_count > 0">
                    <div class="row justify-content-center text-center">
                        <template v-for="(item, index) in signatures.meta_field">
                            <template v-if="item.is_active === true">
                                <div class="col-4 col-md-4 col-lg-4 col-sm-12 printable-width-4 printable-signatories">
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