<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="../resume" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								View Resume
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body">
                    <div class="row" id="viewResume">
                        <div class="col-md-6">
                            <div class="form-group m-form__group row" >
                                <label class="col-3 ">
                                Name
                                </label>
                                <div class="col-9" >
                                <b><p><span v-text="vm_tab1.firstname"></span>&nbsp;<span v-text="vm_tab1.lastname"></span>&nbsp;<span v-text="vm_tab1.suffix"></span></p></b>
                                </div> 
                            </div>
                            <div class="form-group m-form__group row" >
                                <label class="col-3 ">
                                Recruitment Source
                                </label>
                                <div class="col-9" >
                                <b><p v-text="vm_tab1.recruitment">&nbsp;</p></b>
                                </div> 
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-3">
                                Status
                                </label>
                                <div class="col-9">
                                <span class="m-badge text-white m-badge--wide font-weight-bold" :class="class_name" role="alert" v-text="vm_tab1.status"></span>
                                </div>
                            </div>
                            <div class="form-group m-form__group row" >
                                <label class="col-3 ">
                                Schools
                                </label>
                                <div class="col-9" >
                                <b><p v-text="vm_tab1.school">&nbsp;</p></b>
                                </div> 
                            </div>
                            <div class="form-group m-form__group row" >
                                <label class="col-3">
                                Course
                                </label>
                                <div class="col-9" id="edited">
                                <b><p v-text="vm_tab1.course">&nbsp;</p></b>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group m-form__group row" id="approve">
                                <label class="col-3">
                                Tags
                                </label>
                                <div class="col-9" >
                                <b><p v-text="vm_tab1.tag1">&nbsp;</p></b>
                                </div>
                            </div>
                            <div class="form-group m-form__group row" id="approve">
                                <label class="col-3">
                                Position
                                </label>
                                <div class="col-9" >
                                <b><p v-text="vm_tab1.position">&nbsp;</p></b>
                                </div>
                            </div>     
                            <div class="form-group m-form__group row" id="cancel">
                                <label class="col-3">
                                Date of Application
                                </label>
                                <div class="col-9" >
                                <b><p v-text="vm_tab1.created_dt">&nbsp;</p></b>
                                </div>
                            </div>
                            <div class="form-group m-form__group row" id="cancel">
                                <label class="col-3">
                                Remarks
                                </label>
                                <div class="col-9" >
                                <b><p v-text="vm_tab1.description">&nbsp;</p></b>
                                </div>
                            </div>
                        </div>
                          
                    </div>   
				</div>
			</div>
        </div>
    </div>
</div>
<!--end::Portlet-->

<?php $this->load->view("modals/add_file_resume") ?>
<?php $this->load->view("modals/view_file_resume") ?>