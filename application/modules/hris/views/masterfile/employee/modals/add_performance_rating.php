<div class="modal fade" tabindex="-1" role="dialog" id="add-performance-rating-modal">
    <form action="" id="frm-add-performance-rating">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Added Performance Rating</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div id="add-performance-rating"></div>
                        </div>
                    </div>
                    <p style="min-height: 20px;" id="scale-description" class="mt-3 mb-0 text-center m--font-boldest"></p>
                    <input type="hidden" id="rating-value" name="rating">

                    <div class="form-group mt-3" id="for_rehire_display" hidden>
                        <label class="m-radio m-radio--solid m-radio--success">
                            <input type="radio" name="for_rehire" value="0" data-validation="required">
                            Rehire
                            <span></span>
                        </label><br>
                        <label class="m-radio m-radio--solid m-radio--danger">
                            <input type="radio" name="for_rehire" value="1" data-validation="required">
                            Not For Rehire
                            <span></span>
                        </label><br>
                    </div>
                    <input type="hidden" name="purpose" value="0" id="performance_purpose">
                    <div class="form-group mt-4">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" class="form-control" id="remarks" autocomplete="off"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnAdd_performance_rating">Save Rating</button>
                    <button type="button" class="btn btn-danger btnAdd_performance_rating" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>