<style>
    @media screen and (max-width: 690px){
        #fix-mobile{
            display: flex;
            flex-wrap: wrap;
        }

        #fix-mobile button, #fix-mobile a{
            flex: 1 1 22%;
            max-width: 100%;
        }

        #fix-mobile button, #fix-mobile a{
            margin-bottom: 10px;
        }
    }

    @media screen and (max-width: 480px){
        #fix-mobile button, #fix-mobile a{
            flex: 0 0 100%;
            max-width: 100%;
        }

        .mCSB_inside > .mCSB_container {
            margin-right: 5px;
        }

        #fix-mobile .m-btn--icon > span{
            display: unset;
        }
    }
    .custom-file span.help-block.form-error { float: right; }
    .has-error .custom-file-control {
        border: 1px solid rgb(185, 74, 72);
    }
</style>

<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="masterfile" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Overtime Request Form
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <?php $this->current_action = $this->core_layout->getCurrentActions();?>
                <form id="frm_new">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				    <div class="m-portlet__body">
					    <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="row">
                                    <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12 required">EMPLOYEE</label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <div class="form-group m-form__group row">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                <select id="employee" name="employee" data-validation="required"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12 required">COMPANY</label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <div class="form-group m-form__group row">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                <textarea class="form-control m-input" id="details" rows="5" data-validation="required" disabled></textarea>
                                                <input type="hidden" name="company" id="company"/>
                                                <input type="hidden" name="department" id="department"/>
                                                <input type="hidden" name="position" id="position"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12 required">DATE & TIME</label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <div class="form-group m-form__group row">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 input-group date" id="date_time">
                                                <input class="form-control m-input" type="text" id="date" data-validation="required" autocomplete="off" />
                                                <input type="hidden" name="date_from" id="date_from"/>
                                                <input type="hidden" name="date_to" id="date_to"/>
                                                <span class="input-group-addon">
                                                        <i class="la la-calendar glyphicon-th"></i>
                                                </span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12 required">PURPOSE</label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <div class="form-group m-form__group row">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                <textarea class="form-control m-input" id="purpose" name="purpose" rows="6" data-validation="required" autocomplete="off"></textarea>
                                                <input type="hidden" id="prevLen" value="0"/>
                                            </div>
                                        </div>
                                        <span class="m-form__help" style="text-transform: none; font-width: 600;">
                                            <i>Note: Auto bullets when pressing "Enter".</i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="row">
                                    <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12 required">REQUESTED BY</label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <div class="form-group m-form__group row">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                            <select id="requested_by" name="requested_by"  data-validation="required"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">REMARKS</label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <textarea class="form-control m-input" id="remarks" name="remarks" rows="5" autocomplete="off"></textarea>
                                    </div>
                                </div>
                                <?php if((in_array("approve_action", $this->current_action))): ?>
                                    <div class="row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12 required">UPLOAD ATTACHMENT IMAGE</label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <div class="form-group m-form__group row">
                                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                    <div class="custom-file">
                                                        <input id="temp_fileupload" type="file" name="files" class="custom-file-input" data-validation="required" multiple />
                                                        <span class="custom-file-control" id="file_append"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-11 col-lg-11 col-sm-12 col-xs-12">
                                            <div id="progress_approve" class="progress progress-striped active mb-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="display:none;">
                                                <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-11 col-lg-11 col-sm-12 col-xs-12 mt-3">
                                        <div id="uploaded-attach" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollbar-shown="true" data-scrollable="true" style="overflow: visible; position: relative;">
                                            <div id="mCSB_3" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" style="max-height: none; min-height: 92px;">
                                                <div id="mCSB_3_container" class="mCSB_container" style="position: relative; top: 0px; left: 0px;" dir="ltr">
                                                    <div id="tempModalApproveImages">
                                                        <template v-if="count > 0">
                                                            <div class="row">
                                                                <div class="col-md-2 col-lg-2 col-sm-2 col-xs-12" v-for="(item, index) in rows">
                                                                    <div class="m-temp__pic text-center">
                                                                        <a :href="item.image" data-lightbox="tempimage" :title="item.filename" :data-title="item.filename">
                                                                            <img class="m-temp__img" :title="item.filename" :alt="item.filename" :src="item.thumbnail" width="75" height="75" style="margin-bottom: 0.5rem;" />
                                                                        </a>
                                                                        <div class="m-checkbox-inline">
                                                                            <label class="m-checkbox">
                                                                                <input type="checkbox" name="attachment_image[]" :value="item.current_image" class="temp-attachment_image" @click="getCheckedCount" />{{renderImageLabel(index)}}<span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 m--margin-top-10 text-left">
                                                                <input id="checked_count" type="hidden" data-validation="checkbox_group_min1" value="0" />
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                                                                <div class="m-alert__icon">
                                                                    <i class="flaticon-exclamation-1"></i>
                                                                    <span></span>
                                                                </div>
                                                                <div class="m-alert__text">
                                                                    <strong>
                                                                        Image(s) not found!
                                                                    </strong>
                                                                    Upload image first
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="mCSB_3_scrollbar_vertical" class="mCSB_scrollTools mCSB_3_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: block;">
                                                <div class="mCSB_draggerContainer">
                                                    <div id="mCSB_3_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; display: block; height: 14px; max-height: 280px; top: 0px;">
                                                        <div class="mCSB_dragger_bar" style="line-height: 50px;"></div>
                                                    </div>
                                                    <div class="mCSB_draggerRail"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m--align-right">
                    <?php if(in_array("approve_action", $this->current_action) && in_array("save", $this->current_action)): ?>
                        <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnApprove_action">
                            <span>
                            <i class="la la-save"></i>
                                <span>
                                Save & Approve
                                </span>
                            </span>
                        </button>
                    <?php endif; ?>
                    <?php if(in_array("save", $this->current_action) && !in_array('approve_action', $this->current_action)): ?>
                        <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnSave">
                            <span>
                            <i class="la la-save"></i>
                                <span>
                                Save
                                </span>
                            </span>
                        </button>
                    <?php endif; ?>
                        <a href="<?php echo site_url('eforms/overtime/masterfile') ?>" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnBack">
                            <span>CANCEL</span>
                        </a>
                    </div>
                </form>
			</div>
		</div>
	</div>
</div>