<div class="modal fade" tabindex="-1" role="dialog"
     id="time-manual-overtime-entry-modal">
    <form action="" id="frm-time-manual-overtime-entry">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-xl-3 col-sm-12"></div>
                        <div class="col-xl-6 col-lg-6 col-xl-6 col-sm-12 form-group">
                            <label for="date">
                                <span>Date</span>
                                <span class="m--font-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="date"
                                   style="height: auto; font-size: 20px; font-weight: 500;"
                                   data-validation="required" name="date" autocomplete="off" />
                        </div>
                        <div class="col-xl-3 col-lg-3 col-xl-3 col-sm-12"></div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-xl-3 col-sm-12"></div>
                        <div class="col-xl-6 col-lg-6 col-xl-6 col-sm-12 form-group">
                            <label for="time">
                                <span>Time</span>
                                <span class="m--font-danger">*</span>
                            </label>
                            <input type="time" class="form-control" id="time"
                                   style="height: auto; font-size: 20px; font-weight: 500;"
                                   data-validation="required" name="time" autocomplete="off" />
                        </div>
                        <div class="col-xl-3 col-lg-3 col-xl-3 col-sm-12"></div>
                    </div>

                    <input type="hidden" id="field" name="field">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnUpdate">Done</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>