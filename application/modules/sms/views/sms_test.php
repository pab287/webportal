<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Testing
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                        <form action="<?php echo site_url("sms/send_sms"); ?>" id="form_sms">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                    <div class="form-group m-form__group row align-items-center">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control m-input" id="mobile" name="mobile" placeholder="Mobile Number" data-validation="required">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control m-input" id="message" name="message" placeholder="Message" data-validation="required">
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary btnSave" id="send_sms">Send</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>