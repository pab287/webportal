<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Filter</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form onsubmit="event.preventDefault(); filterReport(this);">
                        <input type="hidden" name="csrf_token"
                               value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group m-form__group">
                            <label>Date</label>
                            <div class="">
                                <div class="input-group" id="date-range-picker">
                                    <input type="text" class="form-control m-input input-auto-height"
                                           readonly="" name="date" value="<?=date('Y/m/d')?> - <?=date('Y/m/d')?>"
                                           placeholder="Select date range" data-validation="required">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group m-form__group mt-4">
                            <label>Company</label>
                            <div class="input-group">
                                <select name="company" class="form-control m-input input-auto-height"
                                        data-validation="required"></select>
                            </div>
                        </div>

                        <div class="form-group m-form__group mt-4">
                            <label>Asset Type</label>
                            <div class="input-group">
                                <select name="type" class="form-control m-input input-auto-height"
                                        data-validation="required">
                                    <option></option>
                                    <option value="1">Fix Asset</option>
                                    <option value="2">Vehicle & Equipment</option>
                                    <option value="3">All</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group m-form__group mt-4">
                            <label>Asset Status</label>
                            <div class="input-group">
                                <label class="m-checkbox">
                                    <input type="checkbox" value="1" name="is_archived">
                                        is archived
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <button type="submit" class="btn btn-primary mt-3">Go</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Summary</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="chartdiv" style="width: 100%; height: 500px;"></div>
                </div>
            </div>

            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Detailed List</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m_datatable m-datatable m-datatable--default
                                m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered"
                               id="tbl-inventory-verified-list" width="100%">
                            <thead>
                            <tr>
                                <th>COMPANY</th>
                                <th>ASSET CODE</th>
                                <th>DESCRIPTION</th>
                                <th>STATUS</th>
                                <th>DATE CREATED</th>
                                <!-- <th>INV. STATUS</th> -->
                                <th>DATE CHECKED</th>
                                <th>LOCATION</th>
                                <th>ACCOUNTED TO</th>
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