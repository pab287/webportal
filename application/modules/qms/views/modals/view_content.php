<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"></i>View <b><?=$data->title ?></b> Document</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div class="row" id="is_file_exist">
        <div class="col-md-5" id="pdf-prev">
            <div id="view_pdf_container_<?=$data->id ?>" ></div>
        </div>
        <div class="col-md-7">
            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="font-weight-bold mr-2" for="docno">Document No.:</label>
                    <span id="docno"><?=$data->document_no ?></span>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold mr-2" for="revno">Revision No.:</label>
                    <span id="revno"><?=$data->revision_no ?></span>
                </div>
                <div class="col-md-6">
                    <label class="font-weight-bold mr-2" for="effective">Effective Date:</label>
                    <span id="effective"><?=date('M d, Y', strtotime($data->effective_date)) ?></span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="title" class="font-weight-bold mr-2">Title:</label>
                    <span id="title"><?=$data->title ?></span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="scope" class="font-weight-bold mr-2">Scope:</label>
                    <span id="scope"><?=$data->scope ?></span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="objective" class="font-weight-bold mr-2">Objective:</label>
                    <span id="objective"><?=$data->objective ?></span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="objective" class="font-weight-bold mr-2">Category:</label>
                    <span id="objective"><?=$data->name ?></span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="objective" class="font-weight-bold mr-2">Companies under Document:</label>
                    <span id="objective"><?=$data->companies ?></span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="objective" class="font-weight-bold mr-2">Department(s) under Document:</label>
                    <span id="objective"><?=$data->departments ?></span>
                </div>
            </div>
            <div class="row align-items-center mb-3 <?=(strtoupper($data->name) == 'FORMS' || strtoupper($data->name) == 'FORM' || $this->session->userdata("logged_in")['department'] == 41) ? '' : 'd-none' ?>" style="gap: 20px">
                <div class="col-md-3">
                    <button id="pdfDownload" class="btn btn-primary d-flex" style="gap: 5px" onclick="downloadPDF(<?=$data->id ?>, '<?=$data->filename ?>', 'download')"><span class="la la-cloud-download"></span> Download PDF</button>
                    <!-- <a id="pdfDownload" href="<?//=base_url('uploads/files/ppm/policy/'.$data->id.'/'.$data->filename) ?>" class="btn btn-primary" download><span class="la la-cloud-download"></span> Download PDF</a> -->
                </div>
                <div class="col-md-4">
                    <label for="download" class="font-weight-bold mr-2 mb-0"><?=$data->total_download <= 1 ? 'Total Download:' : 'Total Downloads:' ?></label>
                    <span id="pdf_download"><?=$data->total_download ?></span>
                </div>
            </div>
            <div class="row align-items-center <?=(strtoupper($data->name) == 'FORMS' || strtoupper($data->name) == 'FORM'  || $this->session->userdata("logged_in")['department'] == 41) ? '' : 'd-none' ?>" style="gap: 20px">
                <div class="col-md-3">
                    <button id="pdfPrint" class="btn btn-primary d-flex" style="gap: 5px" onclick="downloadPDF(<?=$data->id ?>, '<?=$data->filename ?>', 'print')"><span class="fa fa-print"></span> Print Document</button>
                </div>
                <div class="col-md-4">
                    <label for="download" class="font-weight-bold mr-2 mb-0"><?=$data->total_print <= 1 ? 'Total Print:' : 'Total Prints:' ?></label>
                    <span id="pdf_print"><?=$data->total_print ?></span>
                </div>
            </div>
        </div>
    </div>
    <hr class="my-4">
    <div class="description">
        <div class="row mb-3">
            <div class="col-md-12">
                <h6 class="font-weight-bold">Description</h6>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-7" id="border-policy-right">
                <?php if($data->author): ?>
                    <div class="prepared_by mb-2">
                        <label for="prepared" class="font-weight-bold mr-2 mb-0">Prepared By: </label>
                        <span id="prepared"><?=$data->author ?> <small><?=date('M d, Y', strtotime($data->prepared_date)) ?></small></span>
                    </div>
                <?php endif; ?>

                <?php if($data->author): ?>
                    <div class="reviewed_by mb-2">
                        <label for="reviewed" class="font-weight-bold mr-2 mb-0">Reviewed and Confirmed By: </label>
                        <span id="reviewed"><?=$data->reviewed_by ?> <small><?=date('M d, Y', strtotime($data->reviewed_date)) ?></small></span>
                    </div>
                <?php endif; ?>
                
                <?php if($data->approved_by): ?>
                    <div class="approved_by mb-2">
                        <label for="approved" class="font-weight-bold mr-2 mb-0">Approved By: </label>
                        <span id="approved"><?=$data->approved_by ?> <small><?=date('M d, Y', strtotime($data->approved_date)) ?></small></span>
                    </div>
                <?php endif; ?>

                <div class="added <?=(!is_numeric($data->updated_by)) ? "mb-2" : "" ?>">
                    <label for="added_by" class="font-weight-bold mr-2 mb-0">Added By: </label>
                    <span id="added_by"><?=$data->added_by ?> <small><?=date('M d, Y', strtotime($data->added_dt)) ?></small></span>
                </div>
                
                <?php if(!is_numeric($data->updated_by)): ?>
                    <div class="updated">
                        <label for="updated_by" class="font-weight-bold mr-2 mb-0">Updated By: </label>
                        <span id="updated_by"><?=$data->updated_by ?> <small><?=date('M d, Y', strtotime($data->updated_dt)) ?></small></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-5">
                <h6 class="font-weight-bold">Older Version(s)</h6>
                
                <?php if(count($data->versions) > 0): ?>
                    <div class="m-list-timeline">
                        <div class="m-list-timeline__items">
                            <?php foreach($data->versions as $row): ?>
                                <!-- <a href="javascript:void(0)" id="notif-user" style="text-decoration: none">
                                </a> -->
                                <a href="javascript:void(0)" class="m-list-timeline__item" id="notif-user" style="text-decoration: none">
                                    <span class="m-list-timeline__badge"></span>
                                    <span class="m-list-timeline__text">
                                        <?=$row->document_file ?>   

                                        <small>
                                            <p class="m-0"><b>Document No:</b> <?=$row->document_no ?></p>
                                            <p class="m-0"><b>Revision No:</b> <?=$row->revision_no ?></p>
                                            <p class="m-0"><b>Effective Date:</b> <?=$row->effective_date ?></p>
                                        </small>
                                    </span>
                                    <span class="m-list-timeline__time"><?=date('M d, Y h:i A', strtotime($row->created_at)) ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    No older version found.
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>
