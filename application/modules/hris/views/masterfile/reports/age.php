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
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Company</label>
                                <select id="company" name="company"class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Station</label>
                                <select id="station" name="station"class="form-control">
                                    <option></option>
                                    <option value="not_assigned">Not Assigned</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Department</label>
                                <select id="department" name="department"class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Age Range</label>
                                <select id="age_range" name="age_range"class="form-control">
                                    <option></option>
                                    <option value="18-24">18-24 years</option>
                                    <option value="25-34">25-34 years</option>
                                    <option value="35-44">35-44 years</option>
                                    <option value="45-54">45-54 years</option>
                                    <option value="55-64">55-64 years</option>
                                    <option value="above">65+ years</option>
                                </select>
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