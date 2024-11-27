<div class="modal-dialog" role="dialog">
    <form action="<?= base_url('hris/masterfile/update_performance_evaluation') ?>"
          id="employee-data-update-performance-evaluation" enctype="multipart/form-data">
        <div class="modal-content">
            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="current_filename" value="<?= $data->filename ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="modal-header">
                <h5 class="modal-title"><i class="la la-edit mr-2"></i>Edit Performance Evaluation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="year" class="form-control-label">Year *</label>
                    <div class="col-md-6 m--padding-left-0">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input id="year" name="year" type="text" class="form-control m-input date" autocomplete="off"
                                   data-validation="required" data-inputmask="'alias': 'yyyy'" data-mask="" value="<?= $data->year ?>"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quarter" class="form-control-label">Quarter *</label>
                    <select id="quarter" name="quarter" autocomplete="off" data-validation="required"
                            class="form-control m-input select2">
                        <option value="" disabled selected>Select an option</option>
                        <?php if (isset($other) && $other): ?>
                            <?php foreach ($other->quarter as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label" class="form-control-label">Date</label>
                    <div class="input-group m--margin-top-5">
                            <span class="input-group-addon">
                                <i class="la la-calendar"></i>
                            </span>
                        <input id="range" type="text" name="range" class="form-control m-input"
                               readonly value="<?= $data->range ?>" style="background-color: #f4f5f8;"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fileupload_performance" class="form-control-label">Attachment</label>
                    <span class="btn btn-success fileinput-button btn-sm pull-right">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Select file</span>
                            <input type="file" id="fileupload_performance" name="files" onchange="setFilename(this, '#temp_fileupload')">
                        </span>
                    <p id="temp_fileupload" class="form-control m-input m--margin-top-10" disabled="disabled"><?= $data->filename ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnEdit"><i class="la la-check mr-2"></i>
                    Save Changes
                </button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>
                    Cancel
                </button>
            </div>
        </div>
    </form>
</div>