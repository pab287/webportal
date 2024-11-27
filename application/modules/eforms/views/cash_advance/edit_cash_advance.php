<div class="m-content">
    <div class="row">
	    <div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="view_cash_advance?id=<?php echo $_GET['id'];?>" title="Back to Viewing" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Cash Advance Detail
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <form id="edit_cash_advance_renderer" action="<?php base_url('eforms/cash_advance/save_cash_advance'); ?>" >
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Employee:
                                    </label>
                                    <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                        <select id="select_employee" name="employee"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Company:
                                    </label>
                                    <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                        <textarea class="form-control m-input" id="company" name="company" rows="4" data-validation="required" v-text="vm_tab1.company_details" disabled></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Amount Applied:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <input class="form-control m-input columnAlign" type="text" name="amt_applied" id="amt_applied" maxlength="22" data-validation="required" v-model="vm_tab1.amt_applied"/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row m-radio-inline">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Deduction Type:
                                    </label> &nbsp; &nbsp; &nbsp;
                                    <label class="m-radio">
                                        <input class="form-control m-input" type="radio" name="deduct_type" value="percentage"/>
                                            Percentage
                                        <span></span>
                                    </label>
                                    <label class="m-radio">
                                        <input class="form-control m-input" type="radio" name="deduct_type" value="fixed">
                                            Fixed
                                        <span></span>
                                    </label>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label" id="lbl_todeduct">
                                        Percentage to deduct:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <input class="form-control m-input columnAlign" type="text" name="amt_deduct" id="amt_deduct" data-validation="required" v-model="vm_tab1.amt_to_b_deducted"/>
                                            <p id="help-block" style="color: red; font-size: 12px;">Note: <i> The set amount/percentage shall be deducted from your salary every payday. 20% shall be the minimum rate for CA deduction.</i>
                                            </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Purpose:
                                    </label>
                                    <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                        <textarea name="purpose" id="purpose" class="form-control" rows="5" data-validation="required" v-model="vm_tab1.purpose"></textarea>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-lg-2 col-sm-2 col-xs-12 col-form-label">
                                        Uploads:
                                    </label>
                                <div class="col-md-10 col-lg-10 col-sm-10 col-xs-12">
                                    <div class="form-group m-form__group">
                                        <div class="custom-file" >
                                            <input type="file" name="files" id="temp_fileupload" onchange="tempFileUpload()" class="custom-file-input" data-validation="required" multiple>
                                            <!-- <input type="hidden" id="path" name="path"/>
                                            <input type="hidden" id="filename" name="filename"/> -->
                                            <span class="custom-file-control" id="file_append"></span>
                                        </div>							
								    </div>
                                    <div class="form-group m-form__group text-center">
                                        <img id="picture" name="picture" style="max-width: 250px; margin: 0 auto;"><br>
                                    </div>
                                </div>
                            </div>
                            <div id ="tempUploadedAttachment">
               <div class="form-group m-form__group row">
               <template v-if="counter > 0">
                    <div class="col-md-6 col-lg-6 col-sm-12" v-for="(row, index) in vm_tab1.rows">
                        <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded mb-3">
                            <div class="m-portlet__body pb-1 pt-1 pl-3">
                                <input type="hidden" name="file_path[]" :value="row.path" />
                                <input type="hidden" name="file_name[]" :value="row.filename" />
                                <input type="hidden" name="file_type[]" :value="row.extension" />
                                <div class="m-widget4" style="overflow-wrap: anywhere;">
                                    <div class="m-widget4__item">
                                        <div class="m-widget4__img">
                                            <a :href="row.image_url" data-lightbox="gallery"  :data-title="row.filename">
                                            <img :src="row.thumbnail" alt="image" width="75" height="75" style="object-fit: contain;"/>
                                            </a>
                                        </div>
                                        <div class="m-widget4__info">
                                            <p class="m-widget4__title mb-0">{{row.filename}}</p>						 		 
                                            <p class="m-widget4__sub mb-0"><span class="m--font-boldest">File Type: {{row.extension}}</span></p>						 		 
                                        </div>
                                        <div class="m-widget4__ext">
                                            <a class="m-widget4__icon" @click="removeAttachment(index)">
                                                <i class="la la-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-if="counter == 0">
                  <div class = "col-12">
                <div class="alert alert-brand m-alert m-alert--air m-alert--outline" role="alert">
                    <strong>FILE(S)</strong> No Photo / Pdf file(s) found!			  	
                </div>
                </div>
                </template>

               </div>
               </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnNew">
                                <span>
                                    <i class="la la-save"></i>
                                    <span>
                                        Save
                                    </span>
                                </span>
                            </button>
                            <a href="<?php echo base_url("eforms/cash_advance/masterfile"); ?>">
                                <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnNew">
                                    <span>
                                        Close
                                    </span>
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
			</div>
            <!--end::Portlet-->  
		</div>
	</div>
</div>
