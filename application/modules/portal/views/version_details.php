<style>
    .ck-editor__editable_inline {
    min-height: 400px !important;
    max-height: 400px !important;
}
</style>
<div class="m-content">
    <div class="row">
		<div class="col-lg-5">
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
								New Version
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <div class="m-portlet__body">
                    <form id="add_version" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-3">
                                        Version
                                    </label>
                                    <div class="col-9">
                                        <input type="text" class="form-control m-input" name="version" data-validation="required" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label for="job_desc" class="col-3">Description</label>
                                    <div class="col-12">
                                        <textarea id="description" name="description" autocomplete="off" data-validation="required" class="form-control m-input" rows="20" style="min-height: 500px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnSave">
                                <span>
                                <i class="la la-save"></i>
                                    <span>
                                    Save
                                    </span>
                                </span>
                            </button>
                            <a href="masterfile">
                                <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnCancel">
                                    <span>
                                        Cancel
                                    </span>
                                </button>
                            </a>
                        </div>
                    </form>
                </div>
			</div>
		</div>
        <div class="col-lg-7">
            <div class="version_list" style="overflow-y: scroll; max-heigt: 400px;">
                <?php foreach($result as $data){ ?>
                <div class="m-portlet m-portlet--success m-portlet--head-solid-bg m-portlet--head-sm m-portlet--collapse mb-1" data-portlet="true" id="m_portlet_tools_1">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                </span>
                                <h3 class="m-portlet__head-text">
                                    <?= $data->version." | ".date("M d, Y", strtotime($data->created_at)) ?>
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="#" data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon" title="" data-original-title="Collapse">
                                        <i class="la la-angle-down"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body" style="display: none;">
                        <?= $data->description ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
	</div>
</div>
<script>
     
</script>