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
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Have you ever been employed by us before? In what branch and what position?</h6></label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques1 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Who referred you to our company?</h6></label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques2 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Name of friends/relatives employed in this company.</h6></label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques3 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Where did you learn of the vacancy? Advertising / Walk In / Referral / School Placement / Others
                            (Pls.
                            Specify)</h6></label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques4 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Do you have any current illness or physical defects? If YES, please describe.</h6></label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques5 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Have you been hospitalized for the past 12 months? If YES, state what illness, date of confinement
                            and name
                            of hospital.</h6></label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques6 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Have you been charged of any Criminal, Civil, or Administrative Offense? If YES, please
                            describe.</h6>
                    </label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques7 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Have you Filed Any Labor Case Against Previous Employers? If YES, What Type DOLE,NLRC or Other,
                            please
                            describe.</h6></label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques8 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-md-12"><h6>Were you Involved or Have Previously Participated in any Labor Strike? If YES, please describe.</h6>
                    </label>
                    <div class="col-md-12">
                        <input type="text" name="ques[]" value="<?= $data->ques9 ?>"
                               autocomplete="off" class="form-control m-input" data-validation="required"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>