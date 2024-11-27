<div class="modal fade" tabindex="-1" role="dialog"
     id="time-adjustments-filter-modal">
    <form action="" id="frm-time-adjustments-filter" class="m-form">
        <div class="modal-dialog" role="document"
             style="max-width: 30%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">FILTER LIST</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group">
                        <label for="filter-employees">By Employees</label>
                        <select name="filter_employees[]"
                                class="form-control select2-multiple-custom"
                                id="filter-employees" multiple></select>
                    </div>

                    <div class="form-group m-form__group">
                        <label for="filter-company">By Company</label>
                        <select name="filter_company"
                                id="filter-company" class="form-control">
                            <option></option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?= $company->id ?>"><?= $company->text ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group m-form__group">
                        <label for="filter-employees">Payroll Group
                            <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                        </label>
                        <select name="filter_payroll_group[]"
                            class="form-control"
                            id="filter-payroll_group"></select>
                    </div>

                    <div class="form-group m-form__group">
                        <label for="">By Date</label>
                        <div class="input-group" id="filter-date-range">
                            <span class="input-group-addon">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                            <input type="text" class="form-control m-input"
                                   name="filter_date_range" style="height: auto;" autocomplete="off">
                            <span class="input-group-btn">
                                <button type="button"
                                        class="btn btn-warning m-btn m-btn--icon m-btn--icon-only btnAdvance_search"
                                        style="height: 35px;"
                                        data-toggle="m-tooltip" data-original-title="Clear"
                                        data-skin="dark" data-placement="top"
                                        onclick="clearDateRange(event)">
                                    <i class="fa fa-ban"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info m-btn m-btn--icon btnAdvance_search">
                        <span>
                            <i class="fa fa-search"></i>
                            <span>Go</span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-warning m-btn m-btn--icon text-white btnAdvance_search"
                            onclick="resetFilter(this);">
                        <span>
                            <i class="fa fa-refresh"></i>
                            <span>Reset Filter</span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-success m-btn m-btn--icon btnAdvance_search"
                            onclick="clearFilter();">
                        <span>
                            <i class="fa fa-ban"></i>
                            <span>Clear</span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>