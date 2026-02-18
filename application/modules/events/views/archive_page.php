<div class="m-content">
    <div class="m-portlet" id="m_portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar-2"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        Company Events Archive
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row m--margin-top-20 m--margin-bottom-30">
                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-2">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input type="text" placeholder="Year" class="form-control m-input" id="filter-year"
                                value="<?= date('Y') ?>" readonly>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                    <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                        <span class="m-input-icon__icon m-input-icon__icon--left">
                            <span>
                                <i class="la la-search"></i>
                            </span>
                        </span>
                        <input type="text" class="form-control m-input m-input--solid"
                                placeholder="Search..." id="search-holidays">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="table-calendar-of-holidays-archive" style="width: 100%;">
                    <thead>
                    <tr>
                        <th></th>
                        <th>TRAINING TITLE</th>
                        <!-- <th>DESCRIPTION</th> -->
                        <th>VENUE</th>
                        <th>SPEAKERS</th>
                        <th>ACTIONS</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>