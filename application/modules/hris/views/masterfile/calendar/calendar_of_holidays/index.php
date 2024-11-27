<style type="text/css">
   
</style>
<div class="m-content">
    <div class="m-portlet" id="m_portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar-2"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        Calendar of Holidays
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" id="calendar_of_holidays_tab"
                    role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab"
                           href="#list-view-tab" role="tab" onclick="clearCalender()">
                            TABULAR VIEW
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab"
                           href="#calender-view-tab" role="tab" onclick="CalendarBasic.init();">
                            CALENDAR VIEW
                        </a>
                    </li>
                </ul>
                <!-- <button class="btn btn-primary btn-sm m-btn m-btn--icon m-btn--pill btnSave" type="button" id="calendar_of_holidays_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span><span>Options</span><span class="dropdown-toggle"></span></span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
                    <a class="dropdown-item" data-toggle="modal" href="#" onclick="clearCalender()">
                        TABULAR VIEW
                    </a>
                    <a class="dropdown-item" data-toggle="modal" href="#" onclick="CalendarBasic.init();">
                        CALENDAR VIEW
                    </a>
                </div> -->
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="list-view-tab">
                    <div class="row m--margin-top-20 m--margin-bottom-30">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <button type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill mb-2 btnNew" onclick="openAddHolidayModal(null, true)">
                                <i class="la la-plus"></i>
                                ADD HOLIDAY
                            </button>
                        </div>
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
                        <table class="table table-bordered table-hover" id="table-calendar-of-holidays" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>DATE FROM</th>
                                <th>DATE TO</th>
                                <th>HOLIDAY DESCRIPTION</th>
                                <th>TAGGED COMPANY</th>
                                <th>CLASSIFICATION</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="calender-view-tab">
                    <div id="m_calendar" class="fc fc-unthemed fc-ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade document-modal-container"
         data-keyboard="false" data-backdrop="static"
         modal-exempt-custom tabindex="-1"
         role="dialog"></div>

    <div class="modal fade document-modal-confirm-container"
         data-keyboard="false" data-backdrop="static"
         modal-exempt-custom tabindex="-1"
         role="dialog"></div>
</div>