<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Remove Task</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmRemoveTask" method="post" v-bind:action="url">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" v-model="id" />
<div class="modal-body">
    <div id="custom-alert"></div>
    <template v-if="count > 0">
        <div id="checklist_preview" class="form-group m-form__group animated fadeIn">NO CHECKLIST PREVIEW!</div>
    </template>
</div>
<div class="modal-footer">
    <button type="button" @click="confirmTaskRemoval" class="btn btn-primary">Remove</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
</div>
</form>