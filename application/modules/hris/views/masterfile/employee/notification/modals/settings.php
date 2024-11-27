<div class="modal fade show" id="m_modal_settings" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
               
                <div class="m-section">
                    <h3 class="m-section__heading">
                       Nearing 1 month trigger gap
                    </h3>
                    <div class="m-section__content">
                    <form id="frm-settings-one-monthgap" action="<?php echo site_url("hris/masterfile/save_one_month_days_gap_setup");?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group m-form__group">
                            <div class="input-group">
                                <input style="height: auto;" type="number" class="form-control" placeholder="Please insert # of days" name="days" data-validation="required">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btnNew" type="submit">
                                        Save
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="m-separator m-separator--space m-separator--dashed"></div>
                    </form>
                    </div>
                </div>
                    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary">
                    Send message
                </button>
            </div>
        </div>
    </div>
</div>


<script>
     $.validate({
        form: "#frm-settings-one-monthgap",
        lang: "en",
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            $.ajax({
                url: formUrl,
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    $(currentForm)
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if(json.status){
                        toastr.success(json.message,"HRIS Notification",5000);
                    }else{
                        toastr.error("Error processing request.","HRIS Notification",5000);
                    }
                }
            });
            return false;
        }
    });
</script>