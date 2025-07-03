<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-question-answers"
              method="post" action="<?= base_url("hris/masterfile/update_employee_question_answers"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Update Employment Questions</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <input type="hidden" name="id" value="<?= $data->id; ?>"/>
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="modal-body">
                <template v-for="(item, index) in vm_question" :key="index">
                    <div class="form-group m-form__group row">
                        <label class="col-md-12"><h6 v-text="item.question"></h6></label>
                        <div class="col-md-12">
                            <input type="hidden" type="text" :name="'question_id'" :value="item.id">
                            <input type="text" :name="'answer'" :value="item.answer" autocomplete="off" class="form-control m-input" data-validation="required"/>
                            <input type="hidden" :name="'question'" :value="item.question" type="text">
                        </div>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>