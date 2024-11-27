<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="<?php echo site_url("eforms/billing/billing"); ?>" title="Go to Masterfile"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Create Billing
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
                <form id="frmCreateBill" method="POST" class="m-form" action="<?php echo site_url('eforms/billing/create_new_bill')?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="prev_reading_id" id="prev_reading_id">
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                        <div class="m-form__heading">
                            <h3 class="m-form__heading-title">
                                Account Information
                            </h3>
                        </div>
                        <div class="row m--margin-bottom-20">
                            <div class="col-md-4">
                                <div class="form-group m-form__group">   
                                    <label>Select Account</label>
                                    <select name="account_id" class="form-control m-input" id="accountSelect" data-validation="required">
                                    
                                    </select>
                                </div>             
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group m-form__group">   
                                    <label>Select Reading</label>
                                    <select name="reading_id" class="form-control m-input" id="readingSelect" data-validation="required">
                                    
                                    </select>
                                </div>             
                            </div>       

                            <div class="col-md-4">
                                <div class="form-group m-form__group">
                                    <label>Due Date</label>
                                    <input autocomplete="off" class="form-control m-input" name="due_date" readonly id="duedate" type="text" data-validation="required">
                                </div>             
                            </div>        
                        </div>
                        <div class="row m--margin-bottom-20">
                            <div class="col-md-4">
                                <div class="form-group m-form__group">   
                                    <label>Customer name :</label>
                                    <input class="form-control m-input customer_name" type="text" style="pointer-events: none;">
                                </div>             
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group m-form__group">   
                                    <label>Meter No :</label>
                                    <input class="form-control m-input meter_no" type="text" style="pointer-events: none;">
                                    <input type="hidden" id="meterno_raw">
                                </div>             
                            </div>  
                            <div class="col-md-2">
                                <div class="form-group m-form__group">   
                                    <label>Block No. :</label>
                                    <input class="form-control m-input block_no" type="text" style="pointer-events: none;">
                                </div>             
                            </div>  
                            <div class="col-md-2">
                                <div class="form-group m-form__group">   
                                    <label>Lot No. :</label>
                                    <input class="form-control m-input lot_no" type="text" style="pointer-events: none;">
                                </div>             
                            </div>  
                        </div>
                        <hr>
                        <div class="m-form__heading">
                            <h3 class="m-form__heading-title">
                                Billing Information
                            </h3>
                        </div>
                        <div class="row m--margin-bottom-20">
                            <div class="col-md-6">
                                <div class="form-group m-form__group">   
                                    <label>Billing Address :</label>
                                    <input class="form-control m-input billing_address" type="text" style="pointer-events: none;">
                                </div>             
                            </div>  
                            <div class="col-md-3">
                                <div class="form-group m-form__group">   

                                    <label>Billing From :</label>
                                    <input autocomplete="off" class="form-control m-input billing_from" type="text" readonly name="billing_from" data-validation="required">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group m-form__group">   
                                    <label>Billing To :</label>
                                    <input autocomplete="off" class="form-control m-input billing_to" type="text" readonly name="billing_to" data-validation="required">
                                </div>
                            </div>
                        </div>
                        <div class="m-form__heading">
                            <h3 class="m-form__heading-title">
                                Readings
                            </h3>
                        </div>
                        <div class="row m--margin-bottom-20 reading-details"> 
                            <div class="col-md-2">
                                <div class="form-group m-form__group">   
                                    <label>Previous :</label>
                                    <input class="form-control m-input previous" style="pointer-events: none;" name="previous" type="text" data-validation="required">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group m-form__group">   
                                    <label>Current :</label>
                                    <input class="form-control m-input current" style="pointer-events: none;" name="current" type="text" data-validation="required">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group m-form__group">   
                                    <label>Usage :</label>
                                    <input class="form-control m-input usage" style="pointer-events: none;" name="usage" type="text" data-validation="required">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group m-form__group">   
                                    <label>Rate :</label>
                                    <input class="form-control m-input rate" style="pointer-events: none;" type="text" name="rate" data-validation="required">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row m--margin-bottom">
                        <div class="col-md-4">
                            <div class="form-group form__group">   
                                <h5 class="m-form__heading-title">
                                    Total Charges
                                </h5>
                                <input class="form-control-lg m-input total_charge text-right" type="text" style="font-weight: bold; pointer-events: none;" name="total_charges">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot">
                    <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                        <button type="submit" class="btn btn-accent btnSave">
                            Submit
                        </button>
                        <a href="<?php echo site_url("eforms/billing/billing"); ?>">
                            <button type="button" class="btn btn-secondary btnCancel">
                                Cancel
                            </button>
                        </a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>