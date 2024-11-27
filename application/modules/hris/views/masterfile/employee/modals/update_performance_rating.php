<div class="modal fade" tabindex="-1" role="dialog" id="update-performance-rating-modal">
    <form action="" id="frm-update-performance-rating">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="la la-edit mr-2"></i>Update Performance Rating</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="current" name="current">

                    <div class="row">
                        <div class="col-12 text-center">
                            <div id="update-performance-rating"></div>
                        </div>
                    </div>
                    <p style="min-height: 20px;" id="scale-description" class="mt-3 mb-0 text-center m--font-boldest"></p>
                    <input type="hidden" id="rating-value" name="rating">

                    <!-- <div class="form-group mt-3">
                        <label for="purpose">Purpose</label>
                        <input type="text" class="form-control" name="purpose" id="purpose" autocomplete="off" data-validation="required">
                    </div> -->

                    <div class="form-group mt-4">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" class="form-control" id="remarks" autocomplete="off"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnUpdate_performance_rating"><i class="la la-check mr-2"></i>Update</button>
                    <button type="button" class="btn btn-danger btnUpdate_performance_rating" data-dismiss="modal"><i class="la la-times mr-2"></i>Close</button>
                </div>
            </div>
        </div>
    </form>
</div>