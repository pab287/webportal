<style>
    li.select2-selection__choice {
        white-space: pre-line;
        max-width: 90%;
        line-height: 20px;
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-late_absentee_report">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <a href="javascript:void(0);" id="toggleCollapse" style="text-decoration: none">
                            <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                                <h3 class="m-portlet__head-text">No Earners Report <small>Filter Options</small></h3>
                            </div>
                        </a>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon" title="Collapse" data-original-title="Collapse">
                                    <i class="la la-angle-down"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-bottom-30">
                    <div class="align-items-center">
                        <form class="m-form has-validation-callback" id="frm-filter">
                            <div class="row">
                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 pr-0">
                                    <div class="form-group m-form__group">
                                        <label for="" class="required mb-1" style="font-weight: 600;">Company</label>
                                        <select name="company" id="company" class="form-control" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 pr-0">
                                    <div class="form-group m-form__group">
                                        <label for="" class="required mb-1" style="font-weight: 600;">
                                            Payout Classification
                                        </label>
                                        <select class="form-control" id="payout_schedule" data-validation="required" name="payout_schedule">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 mt-2 pr-0">
                                    <div class="form-group m-form__group">
                                        <label for="" class="required mb-1" style="font-weight: 600;">
                                            Sequence
                                        </label>
                                        <select class="form-control" id="payroll_sequence"
                                                data-validation="required" name="sequence">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 pr-0 mt-2">
                                    <div class="form-group m-form__group">
                                        <label for="" class="required mb-1" style="font-weight: 600;">Pay Date</label>
                                        <div class="input-group date" id="pay-date">
                                            <input type="text" class="form-control m-input"
                                                data-validation="required"
                                                name="pay_date" autocomplete="off"
                                                placeholder="MMM. DD, YYYY">
                                            <span class="input-group-addon">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-2 pr-0">
                                    <div class="form-group m-form__group">
                                        <label for="date-range" class="required mb-1"
                                            style="font-weight: 600;">
                                            SELECT DATE RANGE
                                        </label>
                                        <div class="input-group" id="date-picker">
                                            <input type="text" class="form-control m-input" readonly=""
                                            placeholder="MMM. DD, YYYY - MMM. DD, YYYY"
                                            data-validation="required" id="date-range"
                                            name="date_range">
                                            <span class="input-group-addon">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xl-2 col-lg-2 col-md-2 text-right">
                                    <button type="submit"
                                        class="btn btn-info m-btn m-btn--icon btnAdvance_search m--margin-top-25">
                                        <span><i class="fa fa-search"></i><span>SEARCH</span></span>
                                    </button>
                                    <button type="button" class="btn btn-warning btnReset btnAdvance_search pull-right m--margin-top-25 text-white ml-2" onclick="resetFilter(this)">
                                            <span><i class="fa fa-refresh "></i>RESET</span>
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
                                    <div class="form-group m-form__group">
                                        <label for="payroll_group" class="mb-1" style="font-weight: 600;">
                                            PAYROLL GROUP
                                            <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                        </label>
                                        <select class="form-control" id="payroll_group" multiple="multiple"></select>
                                    </div>
                                </div>
                                <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 ">
                                    
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group m-form__group">
                                        <label for="" class="mb-1" style="font-weight: 600;">
                                            Employee
                                            <span class="m-form__help" style="text-transform: none; font-width: 600;">(Optional)</span>
                                        </label>
                                        <select name="employees[]" id="employees" class="form-control"
                                        multiple="multiple"></select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm mb-0" data-portlet="true">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Payroll Sheet <small>No Earners Data</small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-bottom-30">
                    <div class="table-responsive-sm">
                            <table class="table table-bordered" id="table-payroll-sheet" style="width: 100%">
                                <thead>
                                <tr>
                                    <th scope="" class="text-center align-middle" rowspan="2">#</th>
                                    <th scope="" class="text-center align-middle" rowspan="2">EMPLOYEE</th>
                                    <th scope="" class="text-center align-middle" rowspan="2">RATE</th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip" id="allow"
                                            data-placement="top"
                                            data-original-title="STANDARD ALLOWANCE"
                                            data-skin="dark">
                                            ALLOW
                                        </span></th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                            data-placement="top"
                                            data-original-title="DAYS WORKED"
                                            data-skin="dark">
                                            DAYS
                                        </span>
                                    </th>
                                    <th scope="" rowspan="2" class="align-middle">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="OVERTIME (AMT)"
                                              data-skin="dark">
                                            OT
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="OVERTIME NIGHT DIFFERENTIAL (AMT)"
                                              data-skin="dark">
                                            ND
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="HOLIDAY"
                                              data-skin="dark">
                                            HOL
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="BASIC PAY"
                                              data-skin="dark">
                                            BASIC
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="ALLOWANCE"
                                              data-skin="dark" id="allowance">
                                            ALLOWANCE
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="ADJUSTMENTS"
                                              data-skin="dark" id="adjustment">
                                            ADJUSTMENTS
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="GROSS PAY"
                                              data-skin="dark">
                                            GROSS
                                        </span>
                                    </th>
                                    <th scope="" class="align-middle" rowspan="2">SSS</th>
                                    <th scope="" class="align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="SSS PROVIDENT"
                                              data-skin="dark">
                                              S.PROV
                                        </span></th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="PHILHEALTH"
                                              data-skin="dark">
                                            PHIC
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="PAG-IBIG"
                                              data-skin="dark">
                                            HDMF
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">TAX</th>
                                    <th scope="" class="text-center" colspan="4">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="LOANS"
                                              data-skin="dark">
                                                LOANS
                                        </span>
                                    </th>
                                    <th scope="" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="NET PAY"
                                              data-skin="dark">
                                            NET PAY
                                        </span>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="COMPANY LOANS"
                                              data-skin="dark">
                                            C.A.
                                        </span>
                                    </th>
                                    <th scope="" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="TOTAL CHARGES"
                                              data-skin="dark">
                                            CHRG
                                        </span>
                                    </th>
                                    <th scope="" class="text-center">
                                        <span data-toggle="m-tooltip"
                                            data-placement="top"
                                            data-original-title="SSS LOANS"
                                            data-skin="dark">
                                            SSS
                                        </span>
                                    </th>
                                    <th scope="" class="text-center">
                                        <span data-toggle="m-tooltip"
                                            data-placement="top"
                                            data-original-title="HDMF LOANS"
                                            data-skin="dark">
                                            HDMF
                                        </span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="m--hide">
                                    <tr>
                                        <th scope="" class="m--font-boldest" colspan="5">GRAND TOTAL</th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                        <th scope=""></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>