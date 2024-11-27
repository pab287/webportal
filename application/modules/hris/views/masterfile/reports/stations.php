<style>
    .table-cb {
        display: inline;
        padding-left: 16px;
    }

    .table-cb span {
        height: 12px;
        width: 12px;
        display: inline;
    }

    .table-cb span:after {
        width: 3px;
        height: 8px;
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <form id="frm-filter-hris-station" class="m-form" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-manpower_report">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                                <h3 class="m-portlet__head-text">Filter Options</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);"  data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                        <i class="la la-angle-down"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder required">COMPANY</label>
                                    <select name="company" id="company" class="form-control" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot text-right">
                        <button type="submit" class="m-btn btn btn-success btnAdvance_search btn-submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Employee's Without Station
                                <small>
                                    List
                                </small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form class="m-form" id="frm-assign-station" method="post">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row">
                            <div class="col-8 col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                <div class="form-group">
                                    <label for="station" class="m--font-bolder required">STATION</label>
                                    <div class="row">
                                        <div class="col-9 col-md-9 col-lg-9 col-sm-12">
                                            <select name="station" id="station" class="form-control" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                        <div class="col-3 col-md-3 col-lg-3 col-sm-12">
                                            <button type="submit" class="m-btn btn btn-primary btnSave btn-submit">Assign Station</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="m-portlet__foot">
                    <div class="table-responsive mt-3">
                        <table id="table-employee-no_station" width="100%" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="no-sort align-middle">
                                    <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                        <input type="checkbox" id="cb-select-all"><span></span>
                                    </label>
                                </th>
                                <th>Employee</th>
                                <th>Position</th>
                                <th>Department</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>