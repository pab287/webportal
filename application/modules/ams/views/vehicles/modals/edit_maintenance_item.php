<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form action="<?= base_url("ams/vehicles/update_maintenance_log") ?>" id="frm-edit-maintenance-log">
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">

            <div class="modal-header">
                <h5 class="modal-title">Edit Maintenance Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label>DESCRIPTION</label>
                    <input type="text" class="form-control m-input"
                           name="last_value" autocomplete="off" disabled
                           value="<?= $data->description ?>">
                </div>

                <div class="row mt-5">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group m-form__group">
                            <label>
                                LAST ODO VALUE
                            </label>
                            <input type="text" class="form-control m-input"
                                   name="last_value" autocomplete="off"
                                   value="<?= $data->last_value ?>">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group m-form__group">
                            <label>Last Schedule</label>
                            <div class="input-group date dt-picker">
                                <input type="text" class="form-control m-input"
                                       name="last_date_perform" autocomplete="off" value="<?= $data->last_date_perform ?>">
                                <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group m-form__group">
                            <label>
                                NEXT ODO VALUE
                            </label>
                            <input type="text" class="form-control m-input"
                                   name="next_value" autocomplete="off"
                                   value="<?= $data->next_value ?>">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group m-form__group">
                            <label>
                                INTERVAL DATE
                            </label>
                            <input type="text" class="form-control m-input"
                                   name="intDate" autocomplete="off"
                                   value="<?= $data->intDate ?>">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group m-form__group">
                            <label>Next Schedule</label>
                            <div class="input-group date">
                                <input type="text" class="form-control m-input"
                                       name="next_date_perform" autocomplete="off"
                                       readonly
                                       value="<?= $data->next_date_perform ?>">
                                <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                            </div>
                        </div>
                    </div>
                    <div class="offset-xl-6 offset-sm-0"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">
                    Save Changes
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(".dt-picker").datepicker({
        todayHighlight: true,
        todayBtn: "linked",
        clearBtn: true,
    });
</script>