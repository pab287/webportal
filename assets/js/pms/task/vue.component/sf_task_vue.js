var componentA = {
    data: function(){
        return {
            id: 0, 
            sf_status: 1, 
            csrf_token: _csrf_hash, 
            redirect: baseUrl("pms/project/set_modal_project_unit_status")
        } 
    }, template: `
    <form id="frmUpdateUnitStatus" method="post" v-bind:action="redirect">
        <input type="hidden" name="csrf_token" v-model="csrf_token">
        <input type="hidden" name="id" value="id">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Status</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <div class="m-checkbox-inline">
                    <label class="m-checkbox">
                        <input type="radio" name="sf_status" value="0" v-model="sf_status"> Awaiting
                        <span></span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="m-checkbox-inline">
                    <label class="m-checkbox">
                        <input type="radio" name="sf_status" value="1" v-model="sf_status"> Ongoing
                        <span></span>
                    </label>
                    <label class="m-checkbox">
                        <input type="radio" name="sf_status" value="2" v-model="sf_status"> On Hold
                        <span></span>
                    </label>
                    <label class="m-checkbox">
                        <input type="radio" name="sf_status" value="3" v-model="sf_status"> Completed
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-danger btnClose">Close</button>
        </div>
    </form>`
};