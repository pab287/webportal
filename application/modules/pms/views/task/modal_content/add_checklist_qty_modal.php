<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><?php echo ($title)? $title: "No title"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="frmSetTaskQty" method="post" action="<?php echo site_url("pms/task/do_post_event/set_checklist_item_qty"); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="checklist_id" value="<?php echo ($temp_id)? $temp_id: 0; ?>" />
        <input type="hidden" name="item_id" value="<?php echo ($item_id)? $item_id: 0; ?>" />
        <div class="modal-body">
            <div class="m-widget1" style="padding: 0;">
                <div class="m-widget1__item">
                    <div class="row m-row--no-padding align-items-center">
                        <div class="col">
                            <h3 class="m-widget1__title">TASK</h3>
                            <span class="m-widget1__desc"><?php echo ($current_task)? $current_task: "No Current Task"; ?></span>
                        </div>
                    </div>
                </div>
                <div class="m-widget1__item">
                    <div class="row m-row--no-padding align-items-center">
                        <div class="col-12">
                            <div class="form-group m-form__group row m--marginless">
                                <label for="tempQty" class="col-4 m-widget1__title col-form-label">Quanitity *</label>
                                <div class="col-8">
                                    <input id="tempQty" class="form-control m-input" type="text" name="qty" autocomplete="off" data-validation="valid_quantity" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-widget1__item">
                    <div class="row m-row--no-padding align-items-center">
                        <div class="col-12">
                            <div class="form-group m-form__group">
                                <label for="remarks" class="m-widget1__title m--margin-bottom-10">Request Remarks *</label>
                                <textarea class="form-control m-input" id="remarks" name="remarks" data-validation="required" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btnSave">Save</button>
            <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
        </div>
        </form>
    </div>
</div>