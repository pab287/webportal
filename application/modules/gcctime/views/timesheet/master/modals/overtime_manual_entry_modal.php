<div class="modal fade" tabindex="-1" role="dialog"
     id="overtime-manual-entry-modal">
    <form id="frm-overtime-manual-entry">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group m--font-bolder col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <div class="row">
                                <div class="col-6">
                                    <label for="ot_in">In *</label>
                                    <div class="input-group date" id="m--datetimepicker_ot_in">
                                        <input id="ot_in" type="text" class="form-control m-input" name="ot_in" data-validation="" />
                                        <span class="input-group-addon">
                                            <i class="la la-calendar-check-o glyphicon-th"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="ot_out">Out *</label>
                                    <div class="input-group date" id="m--datetimepicker_ot_out">
                                    <input id="ot_out" type="text" class="form-control m-input" name="ot_out" readonly="readonly" />
                                        <span class="input-group-addon">
                                            <i class="la la-calendar-check-o glyphicon-th"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group m--font-bolder col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="row">
                                <div class="col-6">
                                    <label for="reg_ot">REG. OT</label>
                                    <input id="reg_ot" type="text" class="form-control m-input" name="reg_ot" />
                                </div>
                                <div class="col-6">
                                    <label for="ndiff_ot">N-DIFF. OT</label>
                                    <input id="ndiff_ot" type="text" class="form-control m-input" name="ndiff_ot" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group m--font-bolder col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-6">
                                    <label for="requested_by">Requested By *</label>
                                    <select id="requested_by" class="form-control m-input" name="requested_by">
                                        <option value="">&nbsp;</option>
                                    </select>
                                    <input type="hidden" id="requested_by_name" name="requested_by_name" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group m--font-bolder col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <label for="purpose">Purpose *</label>
                                    <textarea id="purpose" class="form-control m-input" name="purpose" rows="7"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnUpdate">Done</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>