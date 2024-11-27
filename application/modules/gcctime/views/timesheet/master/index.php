<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Filter By</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form action="" class="m-form" id="frm-filter">
                        <div class="form-group m-form__group pb-0">
                            <label for="cut-offs">Cut Off</label>
                            <select name="cut-off" id="cut-offs" class="form-control">
                                <option value=""></option>
                                <option value="21-5">21-5</option>
                                <option value="6-20">6-20</option>
                            </select>
                        </div>

                        <div class="form-group m-form__group pb-0">
                            <label for="date-range">Date</label>
                            <div class="input-group" id="date-picker">
                                <input type="text" class="form-control m-input" readonly="" placeholder=""
                                       name="date-range" id="date-range">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                            <span class="m-form__help"
                                  style="text-transform: none; font-width: 600;">
                                Default: Yesterday(<?= $yesterday ?>)
                            </span>
                        </div>

                        <div class="form-group m-form__group pb-0">
                            <label for="employees">Employee</label>
                            <select name="employees[]" id="employees"
                                    class="form-control select2-multiple-custom" multiple></select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="company">Company</label>
                            <select name="company" id="company" class="form-control">
                                <option></option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?= $company->id ?>"><?= $company->text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="payroll_group">
                                PAYROLL GROUP
                                <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                            </label>
                            <select class="form-control" id="payroll_group" multiple></select>
                        </div>

                        <div class="form-group m-form__group pb-0 <?= (sizeof($excluded_employees) >= 1) ? '' : 'm--hide' ?>"
                             id="excluded-employees">
                            <div class="m-radio-inline">
                                <label class="m-radio">
                                    <input type="radio" checked name="inclusive_filter" value="1">
                                    ACTIVE
                                    <span></span>
                                </label>
                                <label class="m-radio">
                                    <input type="radio" name="inclusive_filter" value="2">
                                    Excluded
                                    <span></span>
                                </label>
                                <label class="m-radio">
                                    <input type="radio" name="inclusive_filter" value="3">
                                    All
                                    <span></span>
                                </label>
                            </div>
                            <small>
                                <?php
                                    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                                ?>
                                <a href="#excluded-employees-list-modal"
                                   class="m-link m--font-boldest" data-toggle="modal">
                                    <?= $f->format(sizeof($excluded_employees)) ?>(<span
                                        class="m--font-boldest"><?= sizeof($excluded_employees) ?></span>) EMPLOYEES EXCLUDED
                                </a>
                            </small>
                        </div>

                        <div class="d-flex flex-row justify-content-end mt-4">
                            <button type="button" class="btn btn-warning m-btn m-btn--icon m-btn--pill btnAdvance_search m-btn--sm mr-1 text-white" onclick="resetFilter(this)">
                            <span>
                                <i class="fa fa-refresh"></i>
                                <span>Reset Filter</span>
                            </span>
                            </button>
                            <button type="submit" class="btn btn-info m-btn m-btn--icon m-btn--pill btnAdvance_search m-btn--sm">
                            <span>
                                <i class="fa fa-search"></i>
                                <span>Find</span>
                            </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Legend</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="legends d-flex flex-column">
                        <div class="legends__item d-flex flex-row align-items-center">
                            <div class="legends__item__identifier mr-3 d-flex flex-column align-items-center justify-content-center">
                                <i class="fa fa-check m--font-success" style="font-size: 20px;"></i>
                            </div>
                            <div class="legends__item__description m--font-bolder">VERIFIED</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier mr-3 d-flex flex-column align-items-center justify-content-center">
                                <i class="fa fa-refresh m--font-warning" style="font-size: 20px;"></i>
                            </div>
                            <div class="legends__item__description m--font-bolder">RE-GENERATE</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier mr-3 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa fa-flag" style="font-size: 20px;"></i>
                            </div>
                            <div class="legends__item__description m--font-bolder">HOLIDAY</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier legends__item__identifier--absent mr-3"></div>
                            <div class="legends__item__description m--font-bolder">ABSENT</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier legends__item__identifier--lacking mr-3"></div>
                            <div class="legends__item__description m--font-bolder">LACKING ENTRY</div>
                        </div>
                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier legends__item__identifier--multiple mr-3"></div>
                            <div class="legends__item__description m--font-bolder">MULTIPLE ENTRY</div>
                        </div>
                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier legends__item__identifier--no-shift mr-3"></div>
                            <div class="legends__item__description m--font-bolder">NO SHIFT SCHEDULE</div>
                        </div>
                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier legends__item__identifier--posted-payroll_entry mr-3"></div>
                            <div class="legends__item__description m--font-bolder">POSTED PAYROLL ENTRY</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier mr-3 d-flex flex-column align-items-center justify-content-center">
                                <i class="fa fa-car"></i>
                            </div>
                            <div class="legends__item__description m--font-bolder">WITH TRAVEL ORDER</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier mr-3 d-flex flex-column align-items-center justify-content-center">
                                <i class="flaticon-event-calendar-symbol"></i>
                            </div>
                            <div class="legends__item__description m--font-bolder">WITH LEAVE OF ABSENCE</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier mr-3 d-flex flex-column align-items-center justify-content-center">
                                <i class="fa fa-clock-o" style="font-size: 20px;"></i>
                            </div>
                            <div class="legends__item__description m--font-bolder">WITH OVERTIME</div>
                        </div>

                        <div class="legends__item d-flex flex-row align-items-center mt-3">
                            <div class="legends__item__identifier mr-3 d-flex flex-column align-items-center justify-content-center">
                                <i class="fa fa-exclamation-circle" style="font-size: 20px;"></i>
                            </div>
                            <div class="legends__item__description m--font-bolder">HAS PENDING TIME ADJUSTMENT</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Timesheet
                                <small>
                                    Masterfile
                                </small>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools
                                d-flex flex-row align-items-center
                                justify-content-end m--full-height">
                        <div class="dropdown">
                            <button class="btn btn-default m-btn m-btn--icon btnImport"
                                    type="button"
                                    id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                <span>
                                    <i class="fa fa-download"></i>
                                    <span class="m--padding-right-5">Import</span>
                                    <i class="la la-angle-down" style="font-size: 1rem;"></i>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                                 x-placement="bottom-start"
                                 style="position: absolute;
                                        transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px;
                                        will-change: transform;">
                                <a class="dropdown-item btn btnImport_attendance" href="javascript:void(0);"
                                   onclick="openImportModal('attendance')">
                                    <i class="fa fa-calendar"></i>
                                    Attendance
                                </a>
                                <a class="dropdown-item btn btnImport_timesheet" href="javascript:void(0);"
                                   onclick="openImportModal('timesheet')">
                                    <i class="fa fa-clock-o"></i>
                                    Timesheet
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0);"
                                   data-toggle="modal" data-target="#import-history-modal">
                                    <i class="fa fa-history"></i>
                                    History
                                </a>
                            </div>
                        </div>

                        <button class="m--margin-left-5 btn btn-default m-btn m-btn--icon"
                                data-toggle="modal" data-target="#generate-manually-modal">
                            <span>
                                <i class="fa fa-refresh"></i>
                                <span>Generate Manually</span>
                            </span>
                        </button>
                        
                        <span data-toggle="modal" data-target="#monthly-employees-list-modal">
                            <button class="m--margin-left-5 btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--hover-success"
                                    data-toggle="m-tooltip" data-skin="dark" data-original-title="Monthly Employees">
                                <i class="la la-calendar-o"></i>
                            </button>
                        </span>

                        <span data-toggle="modal" data-target="#excluded-employees-list-modal">
                            <button class="m--margin-left-5 btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--hover-danger"
                                    data-toggle="m-tooltip" data-skin="dark" data-original-title="Excluded Employees">
                                <i class="la la-hand-stop-o"></i>
                            </button>
                        </span>

                        <button type="button" class="m--margin-left-5 btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--hover-primary btnImport_timesheet"
                                data-toggle="m-tooltip" data-skin="dark"
                                data-original-title="Download Timesheet Template"
                                onclick="downloadTimesheetExcelTemplate()">
                            <i class="fa fa-download"></i>
                        </button>

                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div id="overtime_modal_action">
                                <button class="btn btn-success m-btn m-btn--icon m-btn--sm"
                                        onclick="openConfirmationModal('verifySelected')" disabled
                                        id="btn-verify">
                                        <span>
                                            <i class="fa fa-check"></i>
                                            <span class="m--font-bolder">Verify Selected</span>
                                        </span>
                                </button>
                                <template v-if="has_overtime_request">
                                    <button class="btn btn-warning m-btn m-btn--icon m-btn--sm m-animate-fade-in" @click="renderOvertimeModal()">
                                            <span>
                                                <i class="fa fa-clock-o"></i>
                                                <span class="m--font-bolder">Preview Overtime Record/s</span>
                                            </span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="offset-xl-2 offset-lg-2 offset-md-2 offset-sm-0 col-xl-4 col-lg-4 col-md-4
                                    col-sm-12 d-flex flex-row justify-content-end align-items-center">
                            <select name="statusFilter"
                                    id="timesheet-status-filter" class="form-control">
                                <option></option>
                                <optgroup label="Scrub Status">
                                    <option value="lacking">Lacking Entry</option>
                                    <option value="multiple">Multiple Entry</option>
                                    <option value="no-shift">No Shift Schedule</option>
                                    <option value="verified">Verified</option>
                                    <option value="unverified">Unverified</option>
                                </optgroup>
                                <optgroup label="Other Status">
                                    <option value="completed">Completed</option>
                                    <option value="incomplete">Incomplete</option>
                                </optgroup>
                            </select>

                            <div class="flex-shrink-0 flex-grow-0">
                                <button class="btn btn-brand m-btn m-btn--icon m-btn--icon-only ml-2"
                                        data-toggle="m-tooltip"
                                        data-original-title="Print"
                                        data-skin="dark" data-delay="{&quot;show&quot;: 300}"
                                        onclick="printTimesheet(this);">
                                    <i class="fa fa-print"></i>
                                </button>
                            </div>

                            <div class="m-btn-group btn-group ml-2" role="group">
                                <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <button id="btn-export-timesheet"
                                            type="button"
                                            data-toggle="m-tooltip"
                                            data-original-title="Export"
                                            data-skin="dark" data-delay="{&quot;show&quot;: 300}"
                                            class="btn btnExport btn-accent m-btn m-btn--icon m-btn--icon-only m-btn--sm">
                                        <i class="la la-external-link"></i>
                                    </button>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1"
                                     x-placement="bottom-end"
                                     style="position: absolute; transform: translate3d(-62px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <a href="javascript:void(0);" class="dropdown-item datatable-pdf" id="ExportPDF"
                                       onclick="exportAs('pdf');">
                                        <i class="m-nav__link-icon fa fa-file-pdf-o"></i>
                                        <span class="m-nav__link-text">PDF</span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item datatable-excel" id="ExportExcel"
                                       onclick="exportAs('excel');">
                                        <i class="m-nav__link-icon fa fa-file-excel-o"></i>
                                        <span class="m-nav__link-text">EXCEL</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive-sm mt-2">
                        <table class="table table-bordered"
                               id="tbl-timesheet" width="100%">
                            <thead>
                            <tr>
                                <th>Employee</th>
                                <th>
                                    <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                        <input type="checkbox" id="cb-select-all"><span></span>
                                    </label>
                                </th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>In</th>
                                <th>Out</th>
                                <th>In</th>
                                <th>Out</th>
                                <th>Late</th>
                                <th>
                                    <span data-toggle="m-tooltip" style="cursor: pointer;"
                                          data-original-title="UNDERTIME" data-skin="dark"
                                          data-delay='{"show": 300}'>
                                        UT
                                    </span>
                                </th>
                                <th>
                                    <span data-toggle="m-tooltip" style="cursor: pointer;"
                                          data-original-title="REGULAR HOURS" data-skin="dark"
                                          data-delay='{"show": 300}'>
                                        REG. HRS.
                                    </span>
                                </th>
                                <th>
                                    <span data-toggle="m-tooltip" style="cursor: pointer;"
                                          data-original-title="OVERTIME" data-skin="dark"
                                          data-delay='{"show": 300}'>
                                        OT
                                    </span>
                                </th>
                                <th>NDOT</th>
                                <th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog"
     id="time-adjustment-details-container-modal" modal-exempt-custom
     data-keyboard="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
    $this->load->view('../modals/container_modal');
    $this->load->view('modals/create_time_adjustment_modal');
    $this->load->view('modals/time_manual_entry_modal');
    $this->load->view('modals/time_manual_overtime_entry_modal');
    $this->load->view('modals/overtime_manual_entry_modal');
    $this->load->view('modals/import_modal', $biometric_devices);
    $this->load->view('modals/no_employee_biometric_modal');
    $this->load->view('modals/new_employee_modal', $for_select);
    $this->load->view('modals/import_history_modal');
    $this->load->view('modals/excluded_employees_modal', $excluded_employees);
    $this->load->view('modals/monthly_employees_modal');
    $this->load->view('modals/generate_manually_modal', array("companies"=>$companies));
    $this->load->view('../excluded_employees/modals/add_employee_modal');
    $this->load->view('../excluded_employees/modals/edit_excluded_employee_modal');
    $this->load->view('modals/import_possible_duplicates_modal');
    $this->load->view('modals/possible_matches_modal');
    $this->load->view('modals/add_shift_modal');
    $this->load->view('modals/look_up_and_update_modal');
    $this->load->view('../modals/confirmation_modal');
    $this->load->view('../modals/alert_modal');
    $this->load->view('modals/overtime_nobreak_modal');
?>