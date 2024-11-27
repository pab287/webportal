<div class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"
     id="no-employee-biometric-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Successful!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m--font-bold m--regular-font-size-lg1" id="message">
                    ATTENDANCE SUCCESSFULLY IMPORTED & TIMESHEET WAS SUCCESSFULLY GENERATED.
                </div>

                <div class="m--margin-top-20">
                    <i class="fa fa-warning m--font-warning"></i>
                    <span class="m--font-bold" style="border-bottom: 1px dotted grey;">
                        Employee not found for the ff. biometric nos.
                    </span>
                </div>
                <div class="biometric-list-container m--margin-top-10">
                    <div class="list flex-row flex-wrap">
                        <!--<div class="list__item" data-biometric="112233" style="flex: 0 0 33%;">
                            <div class="list__item__cell">
                                <button type="button"
                                        class="btn btn-sm btn-default m-btn
                                               m-btn--sm m-btn--icon m-btn--icon-only m-btn--pill
                                               m-btn--hover-success
                                               btnNew"
                                        data-toggle="m-tooltip"
                                        data-skin="dark"
                                        data-placement="top"
                                        data-original-title="Add Employee"
                                        onclick="openAddEmployeeModal(112233)">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="list__item__cell list__item__cell--bolder">112233</div>
                        </div>-->
                    </div>
                </div>

                <div class="m--margin-top-25">
                    <i class="fa fa-warning m--font-warning"></i>
                    <span class="m--font-bold" style="border-bottom: 1px dotted grey;">
                        EMPLOYEE W/O SHIFTS DETECTED.
                    </span>
                </div>
                <div class="no-shifts-container m--margin-top-10">
                    <div class="list flex-row flex-wrap">
                        <!--<div class="list__item" data-biometric="112233" style="flex: 0 0 33%;">
                            <div class="list__item__cell">
                                <button type="button"
                                        class="btn btn-sm btn-default m-btn
                                               m-btn--sm m-btn--icon m-btn--icon-only m-btn--pill
                                               m-btn--hover-success
                                               btnNew"
                                        data-toggle="m-tooltip"
                                        data-skin="dark"
                                        data-placement="top"
                                        data-original-title="Add Employee"
                                        onclick="openAddEmployeeModal(112233)">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="list__item__cell list__item__cell--bolder">112233</div>
                        </div>-->
                    </div>
                </div>

                <!--<div class="m--margin-top-25">
                    <div class="d-inline text-muted export-csv-link m--font-bold"
                         onclick="exportAsCsv($('#biometric_no_array').val())">
                        <i class="fa fa-external-link"
                           style="font-size: 11px;"></i>
                        <span style="text-transform: none;"
                              class="m--regular-font-size-sm1">
                        EXPORT LIST AS CSV.
                    </span>
                    </div>
                </div>-->

                <input type="hidden" id="biometric_no_array" value="" class="form-control">
                <input type="hidden" id="no_shifts_array" value="" class="form-control">
                <input type="hidden" id="timesheet-imports-id" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>