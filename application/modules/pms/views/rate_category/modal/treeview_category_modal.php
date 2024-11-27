<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form action="<?=base_url('pms/rate_category/arrange_category_tree')?>"
              id="frm-category-tree">
            <div class="modal-header">
                <h5 class="modal-title">Rate Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" placeholder="Search..." class="form-control" id="search-tree">
                </div>
                <div id="category-tree" style="min-height: 300px;"
                     class="pt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">Save Changes</button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>
