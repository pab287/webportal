<style>
    #to-remove{
        display: none
    }
    .colcat1{
        flex: 0 0 22%;
        max-width: 22%
    }
    .colcat3{
        flex: 0 0 1%;
        max-width: 1%
    }
    .colcat4{
        flex: 0 0 25%;
        max-width: 25%
    }
    .colcat5{
        flex: 0 0 30%;
        max-width: 30%;
    }
</style>

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Document</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-add_policy" method="post" action="<?php echo site_url("qms/masterfile/set_modal_policy"); ?>" enctype="multipart/form-data" autocomplete="off">
<!-- <form id="form-add_policy" method="post" action="#" enctype="multipart/form-data"> -->
    <div class="modal-body">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="policy-category" class="form-control-label required">Category</label>
                    <select class="form-control" name="category" id="policy-category" data-validation="required">
                        <option value="">Select an Option</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="policy-docno" class="form-control-label required">Document No.</label>
                    <div id="for-memorandum" class="d-none">
                        <div class="row align-items-center justify-content-center">
                            <div class="colcat1 p-0">
                                <input type="hidden" id="memo-ref_code">
                                <span>Memo No.</span>
                            </div>
                            <div class="colcat1 p-0">
                                <input type="number" id="memo-ref_year" name="ref_year" class="form-control m-input" placeholder="year" data-validation="required">
                            </div>
                            <div class="colcat3 p-0">
                                <span>-</span>
                            </div>
                            <div class="colcat4 p-0">
                                <input type="number" id="memo-ref_series" name="ref_series" class="form-control m-input" placeholder="series" data-validation="required">
                            </div>
                            <div class="colcat4 p-0">
                                <input type="text" id="memo-ref_department" name="ref_department" class="form-control m-input" placeholder="dept" data-validation="required">
                            </div>
                        </div>
                    </div>
                    <div id="for-kra" class="d-none">
                        <div class="row align-items-center pl-3">
                            <div class="colcat1 p-0">
                                <input type="hidden" id="kra-ref_code">
                                <span>KRA/KPI</span>
                            </div>
                            <div class="colcat5 p-0">
                                <input type="text" class="form-control m-input" placeholder="year" aria-describedby="basic-addon1"  data-validation="required">
                            </div>
                        </div>
                    </div>
                    <div id="others-category">
                        <input type="text" name="document_no" id="policy-docno" class="form-control" data-validation="required">
                    </div>

                    <!-- <div class="input-group">
                        <input type="text" class="form-control input-sm" />
                        <span class="input-group-btn" style="width:0px;"></span>
                        <input type="text" class="form-control input-sm" />
                    </div> -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="policy-revision" class="form-control-label required">Revision No.</label>
                    <input type="number" name="revision_no" id="policy-revision" class="form-control" data-validation="required">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="policy-effective" class="form-control-label required">Implementation Date</label>
                    <input type="text" name="effective_date" id="policy-effective" class="form-control" data-validation="required">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="policy-name" class="form-control-label required">Title</label>
                    <input type="text" name="title" id="policy-name" class="form-control" data-validation="required">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="policy-obj" class="form-control-label required">Objective</label>
                    <textarea type="text" name="objective" id="policy-obj" class="form-control" data-validation="required" rows="6"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="policy-scope" class="form-control-label required">Scope</label>
                    <input type="text" name="scope" id="policy-scope" class="form-control" data-validation="required">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="policy-company" class="form-control-label required">Company under Document</label>
                    <select class="form-control" name="company[]" id="policy-company" data-validation="required" multiple>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="policy-company" class="form-control-label required">Department(s) under Document</label>
                    <select class="form-control" name="department[]" id="policy-department" multiple>
                    </select>
                </div>
            </div>
        </div>

        <div class="row" id="to-remove">
            <div class="col-md-4 border-right">
                <div class="form-group">
                    <label for="policy-author" class="form-control-label required">Prepared By</label>
                    <select class="form-control" name="author" id="policy-author" data-validation="required">
                        <option value="">Select an Option</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="policy-prepared_date" class="form-control-label required">Date</label>
                    <input class="form-control" name="prepared_date" id="policy-prepared_date" data-validation="required">
                </div>
            </div>

            <div class="col-md-4 border-right">
                <div class="form-group">
                    <label for="policy-reviewed" class="form-control-label required">Reviewed and Confirmed By</label>
                    <select class="form-control" name="reviewed_by[]" id="policy-reviewed" multiple data-validation="required">
                        <option value="">Select an Option</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="policy-reviewed_date" class="form-control-label required">Date</label>
                    <input class="form-control" name="reviewed_date" id="policy-reviewed_date" data-validation="required">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="policy-approved" class="form-control-label required">Approved By</label>
                    <select class="form-control" name="approved_by" id="policy-approved" data-validation="required">
                        <option value="">Select an Option</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="policy-approved_date" class="form-control-label required">Date</label>
                    <input class="form-control" name="approved_date" id="policy-approved_date" data-validation="required">
                </div>
            </div>
        </div>

        <hr>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="policy-desc" class="form-control-label required">Attachments</label>
                    <div class="m-dropzone dropzone m-dropzone--primary dz-clickable" id="m-dropzone-one">
                        <div class="m-dropzone__msg dz-message needsclick">
                            <h3 class="m-dropzone__msg-title">Drop files here or click to upload.</h3>
                            <span class="m-dropzone__msg-desc">Accepts only a PDF File.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
        <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
    </div>
</form>