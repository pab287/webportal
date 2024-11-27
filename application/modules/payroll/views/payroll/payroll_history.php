<div class="m-content">
    <div class="row">
        <form class="col-xl-3 col-lg-3 col-md-3 col-sm-12"
              id="frm-filter-payroll-history">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon">
                            <i class="fa fa-filter"></i>
                        </span>
                            <h3 class="m-portlet__head-text">
                                Filter
                            </h3>
                        </div>
                    </div>
                </div>
                <div id="tempFilter" class="m-portlet__body">
                    <div class="form-group">
                        <label for="">GROUP BY</label>
                        <div class="m-checkbox-inline">
                            <label class="m-radio">
                                <input type="radio"
                                       name="group"
                                       data-validation="required"
                                       value="1" class="valid"
                                       @click="tempShowByDates(1)"
                                       checked>
                                PAY DATE<span></span>
                            </label>
                            <label class="m-radio">
                                <input type="radio"
                                       name="group"
                                       data-validation="required"
                                       value="2" class="valid"
                                       @click="tempShowByDates(2)">
                                MONTH<span></span>
                            </label>
                            <label class="m-radio">
                                <input type="radio"
                                       name="group"
                                       data-validation="required"
                                       value="3" class="valid"
                                       @click="tempShowByDates(3)">
                                YEAR<span></span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5" v-if="show_by_date === true">
                        <label class="m-checkbox">
                            <input id="filter_date_range" type="checkbox"
                                   name="filter_by_date_range"
                                   class="valid" @click="tempShowPicker()">
                            BY DATE RANGE ?<span></span>
                        </label>
                    </div>
                    <div class="row m-animate-fade-in" id="filter-by-month-year" v-if="show_picker === false">
                        <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12">
                            <label for="">MONTH</label>
                            <select class="form-control"
                                    name="filter_month">
                                <option></option>
                            </select>
                        </div>
                        <div class="form-group col-xl-5 col-lg-5 col-md-5 col-sm-12 pl-0">
                            <label for="">YEAR</label>
                            <select class="form-control"
                                    name="filter_year"></select>
                        </div>
                    </div>

                    <div class="form-group m-animate-fade-in" id="filter-by-date-range" style="height: 74px;" v-if="show_picker === true">
                        <label for="date-range">SELECT DATE RANGE</label>
                        <div class="input-group" id="date-picker">
                            <input type="text" class="form-control m-input valid" readonly=""
                                   placeholder="MMM DD, YYYY - MMM DD, YYYY"
                                   id="date-range"
                                   name="date_range" style="">
                            <span class="input-group-addon">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button class="m-btn btn btn-success btnAdvance_search"
                                type="submit">
                            GO
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="col">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon">
                            <i class="fa fa-list"></i>
                        </span>
                            <h3 class="m-portlet__head-text">
                                History List
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="table-responsive-sm">
                        <table class="table table-bordered"
                               id="tbl-payroll-history"
                               width="100%">
                            <thead>
                            <tr>
                                <th>PAY DATE</th>
                                <th>PAY MONTH</th>
                                <th>COVERAGE DATE</th>
                                <th>TOTAL SSS</th>
                                <th>TOTAL PHIC</th>
                                <th>TOTAL HDMF</th>
                                <th>TOTAL TAX</th>
                                <td>TOTAL NET PAY</td>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>