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
								View Blacklisted
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body">
					<div class="row" id="viewBlacklisted">
            <div class="col-md-6">
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Name:
                </label>
                <div class="col-9"><b><p id="ref_no" v-text="row.name">&nbsp; </p></b></div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Recruitment Source
                </label>
                <div class="col-9" id="source">
                  <b><span v-text="row.recruitment"></span></b>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Tags
                </label>
                <div class="col-9" id="from">
                  <ul v-for="(item, index) in listItems(row.tags)">
                    <b><li v-text="item"></li></b>
                  </ul>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Status
                </label>
                <div class="col-9">
                  <b><span class="m-badge text-white m-badge--wide font-weight-bold" :class="class_name" role="alert" v-text="row.status"></span></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="other_remark">
                <label class="col-3">
                  School
                </label>
                <div class="col-9" >
                  <ul v-for="(item, index) in listItems(row.school)">
                    <b><li v-text="item"></li></b>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group m-form__group row" >
                <label class="col-3 ">
                  Courses
                </label>
                <div class="col-9" >
                  <ul v-for="(item, index) in listItems(row.course)">
                    <b><li v-text="item"></li></b>
                  </ul>
                </div> 
              </div>
              <div class="form-group m-form__group row" >
                <label class="col-3">
                  position
                </label>
                <div class="col-9" id="edited">
                  <ul v-for="(item, index) in listItems(row.position)">
                    <b><li v-text="item"></li></b>
                  </ul>
                </div>
              </div>
              <div class="form-group m-form__group row" id="approve">
                <label class="col-3">
                  Date of Application
                </label>
                <div class="col-9" >
                  <b><p v-text="row.date">&nbsp;</p></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="release_remark">
                <label class="col-3 col-form-label">
                  Remarks
                </label>
                <div class="col-9" id="remark">
                  <b><p  class="m--margin-top-5" v-text="row.description">&nbsp;</p></b>
                </div>
              </div> 
            </div>         
          </div>
          <div class="m-separator m-separator--dashed d-xl-12"></div> 
          <br>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group m-form__group row">
                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12">
                  <table class="table table-striped table-bordered" id="view_blacklisted_file_table" width="100%">
                    <thead>
                      <tr>
                        <th>File Name</th>
                        <th>File Size</th>
                        <th>Uploaded By</th>
                        <th>Date Uploaded</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
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

<?php $this->load->view("modals/view_blacklisted_file_modal") ?>
