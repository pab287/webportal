<div class="modal-header">
    <h5 class="modal-title" id="personnelRequestModalLabel">Personnel Request - <small>Completed</small></h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <p><strong>Remarks</strong></p>
            <p class="form-control m-input m-input--custom_textarea" disabled><?php echo $remarks; ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <h4><small>Original Personnel</small> - <?php echo $people_no; ?></h4>
        </div>
        <div class="col-md-4">
            <h4><small>Hired</small> - <?php echo $current; ?></h4>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-danger modalClose" data-dismiss="modal">Close</button>
</div>