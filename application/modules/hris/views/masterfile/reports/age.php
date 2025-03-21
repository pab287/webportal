<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                HRIS AGE REPORT
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--marginless row">
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Company</label>
                                <select id="company" name="company"class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Station</label>
                                <select id="station" name="station"class="form-control">
                                    <option></option>
                                    <option value="not_assigned">Not Assigned</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Department</label>
                                <select id="department" name="department"class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12" >
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-4 col-md-4">
                                        <label class="control-label">Age range: </label>
                                    </div>
                                    <div class="col-2">
                                        <strong><label class="control-label" id="age_range_min"></label></strong>
                                    </div>
                                    <div class="col-2">
                                        <strong><label class="control-label" id="age_range_max"></label></strong>
                                    </div>
                                </div>
                                <div>
                                    <div id="age_range_slider"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 table-responsive">
                        <table id="hris_age_reports" class="table table-bordered table-hover" width="100%">
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>