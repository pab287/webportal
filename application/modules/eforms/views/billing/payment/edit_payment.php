<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="<?php echo site_url("eforms/billing/payment"); ?>" title="Go to Masterfile"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Edit Payment
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
                </div>
                <form id="updatePayment" method="POST" class="m-form m-form--group-seperator-dashed" action="<?php echo site_url('eforms/billing/update_payment')?> ">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" value="">
                    <div class="m-portlet__body">
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                            <div class="m-form__heading">
                                <h3 class="m-form__heading-title">
                                    Account Information
                                </h3>
                            </div>
                            <div class="row m--margin-bottom-20">

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Select Account</label>
                                        <select name="account_id" class="form-control m-input" id="accountSelect" data-validation="required">
                                        
                                        </select>
                                    </div>             
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Customer Name</label>
                                        <input class="form-control m-input customer_name" readonly type="text">
                                    </div>             
                                </div>  
                                
                            </div>  
                        </div>
                            <hr>
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10 form__group" >
                            <div class="m-form__heading">
                                <h3 class="m-form__heading-title">
                                    Payment Information
                                </h3>
                            </div>
                            <div class="row m--margin-bottom-20">

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Select Bill</label>
                                        <select name="bill_id" class="form-control m-input" id="billSelect" data-validation="required">
                                        
                                        </select>
                                    </div>             
                                </div>  

                            </div>  
                            <div class="row m--margin-bottom-20">

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Payment Type</label>
                                        <select name="payment_type" class="form-control m-input" id="payment_type" data-validation="required">
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="bank">Bank</option>
                                        </select>
                                    </div>             
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Payment Date</label>
                                        <div class="input-group date" id="m_datepicker_payment_date">
                                            <input type="text" class="form-control m-input payment_date" name="payment_date" placeholder="Select date" data-validation="required">
                                        </div>
                                    </div>             
                                </div> 

                            </div>  
                            <div class="row m--margin-bottom-20">

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Received Amount</label>
                                        <input type="number" class="form-control m-input received_amount" autocomplete="off" name="received_amount" data-validation="required">
                                    </div>             
                                </div>  
                                
                            </div>  
                        
                        </div>
                        <hr>
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                            <div class="m-form__heading">
                                <h3 class="m-form__heading-title">
                                    Penalty Details
                                </h3>
                            </div>
                            <div class="row m--margin-bottom-20">

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Overdue</label>
                                        <input class="form-control m-input overdue" name="overdue" readonly type="text" data-validation="required">
                                    </div>             
                                </div>  
                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Net Payment</label>
                                        <input class="form-control m-input net_payment" name="net_payment" readonly type="text" data-validation="required">
                                    </div>             
                                </div>

                            </div>
                        </div> 
                    </div>
                    <div class="m-portlet__foot">
                        <div class="m-form__actions">
                            <button type="submit" class="btn btn-accent btnSave">
                                Update
                            </button>
                            <a href="<?php echo site_url("eforms/billing/payment"); ?>">
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