<link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css"); ?>">
<link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload.css"); ?>">
<link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload-ui.css"); ?>">
<noscript><link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload-noscript.css"); ?>"></noscript>
<noscript><link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload-ui-noscript.css"); ?>"></noscript>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Pending Resume Masterfile
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
								<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
									<i class="la la-ellipsis-h m--font-brand"></i>
								</a>
								<div class="m-dropdown__wrapper">
									<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 22.5px;"></span>
									<div class="m-dropdown__inner">
										<div class="m-dropdown__body">
											<div class="m-dropdown__content">
												<ul class="m-nav">
													<li class="m-nav__section m-nav__section--first">
														<span class="m-nav__section-text">
															Quick Action
														</span>
													</li>
													<li class="m-nav__item">
                                                    <a href="<?php echo site_url("crs/resume");?>" class="m-nav__link"  >
															<i class="m-nav__link-icon flaticon-open-box"></i>
															<span class="m-nav__link-text" >
																Masterfile
															</span>
														</a>
													<a href="<?php echo site_url("crs/archive");?>" class="m-nav__link"  >
															<i class="m-nav__link-icon flaticon-open-box"></i>
															<span class="m-nav__link-text" >
																Archives
															</span>
														</a>
                                                        <a href="<?php echo site_url("crs/interview");?>" class="m-nav__link"  >
															<i class="m-nav__link-icon flaticon-open-box"></i>
															<span class="m-nav__link-text" >
																For Interview
															</span>
														</a>
                                                        <a href="<?php echo site_url("crs/blacklist");?>" class="m-nav__link"  >
															<i class="m-nav__link-icon flaticon-open-box"></i>
															<span class="m-nav__link-text" >
																Blacklisted
															</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
                <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-4">
								
                                    
                                </div>
								
								<div class="col-md-4">
									<div class="dropdown">
										<button class="btn btn-brand dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Actions
										</button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
											<a class="dropdown-item" href="#">
												<i class="la la-search-plus"></i> Query Builder
											</a>
											<a class="dropdown-item" href="#">
												<i class="la la-barcode"></i> Generate Barcode
											</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item" href="#">
												<i class="la la-file-archive-o"></i> Generate Barcode
											</a>
										</div>
									</div>
								</div>
                            </div>
                        </div>
                       <div class="col-xl-4 order-1 order-xl-2 m--align-right">
					   <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                        <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                        <span class="m-input-icon__icon m-input-icon__icon--left">
                                            <span>
                                                <i class="la la-search"></i>
                                            </span>
                                        </span>
                                    </div>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
						<div class="row align-items-center">
							<div class="col-xl-12 order-2 order-xl-1">
							<div style='float:right !important;' class="form-group m-form__group row">
								<div class="col-md-4">
                                	<div class="m-input-icon m-input-icon--left">
										<button class="btn btn-secondary dropdown-toggle" aria-expanded="true"><i class="la la-table"></i></button>
									</div>
								</div>
								<div class="col-md-4">
                                	<div class="m-input-icon m-input-icon--left">
										<button class="btn btn-secondary dropdown-toggle" aria-expanded="true"><i class="la la-external-link"></i></button>
									</div>
								</div>
							</div>
								<div class="colms-12">
									<div class="btn-group m-btn-group" role="group" aria-label="...">
										<button type="button" class="btn btn-primary" id="reload_dtTbl">
											<i class="la la-refresh"></i>
										</button>
										<div class="m-btn-group btn-group" role="group">
											<button id="tbl-btn-share" type="button" class="btn btn-success m-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="la la-table"></i>
											</button>
											<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
												<a class="dropdown-item" href="#">
													Dropdown link
												</a>
												<a class="dropdown-item" href="#">
													Dropdown link
												</a>
												<a class="dropdown-item" href="#">
													Dropdown link
												</a>
												<a class="dropdown-item" href="#">
													Dropdown link
												</a>
											</div>
										</div>
										<div class="m-btn-group btn-group" role="group">
											<button id="tbl-btn-share" type="button" class="btn btn-info m-btn m-btn--pill-last dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="la la-share"></i>
											</button>
											<div class="dropdown-menu" aria-labelledby="btnGroupDrop2" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
												<a class="dropdown-item" href="#">
													Excel
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
					<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-resume" width="100%">
								<thead>
									<tr>
										<th>Name</th>
                                        <th>School</th>
                                        <th>Course</th>
                                        <th>Position</th>
                                        <th>Status</th>
                                        <th>Tag</th>
                                        <th>Recruitment</th>
                                        <th>Application Date</th>
										<th>Action</th>
									</tr>
								</thead>

								<tbody>	
								</tbody>
							</table>
						</div>
					<!--end: Datatable -->
          <div class="modal fade" id="modal_form_delete" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title"></h3>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  
                </div>
                <div class="modal-body form">
                  <form action="#" id="form_delete" class="form-horizontal">
                    <input type="hidden" value="" name="delete_id"/> 
                   <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                    
                    <div class="col-md-12">
                     <b>Are you sure you want to Delete data</b> 
                    </div>
                    </div>
                    
                    </div>
                   <div class="modal-footer">
				   <button type="button" onclick="delete_resume()" class="btn btn-danger m-btn m-btn--custom m-btn--icon  btnNew" >Delete</button>
                      <button type="button" class="btn btn-defualt m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">Cancel</button>
                    </div>
                    </form>
                  </div><!-- /.modal-content -->
                </div>
    </div>
  </div>
					<div class="modal fade" id="modal_form_document" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title"></h3>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  
                </div>
                <div class="modal-body form">
                  <form action="#" id="form_document" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" value="" name="id"/> 
				
                   <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

				   <!-- <div class="form-group">
  <label class="control-label col-md-4 labelfile">Files</label>
  <div class="col-md-12">
    <input id="fileupload" type="file" name="files[]" style="color:transparent;"  multiple class="">
      <input type="text" name="filename" id="filename"  class="form-control" readonly data-validation="required">
      
  </div>
 
</div> -->
<div class="form-group">
  <label class="control-label col-md-4">Firstname</label>
  <div class="col-md-12">
  <input type="text" name="firstname" id="firstname" placeholder="" class="form-control" data-validation="required">
  
  </div>
  
</div>
<div class="form-group">
  <label class="control-label col-md-4">Lastname</label>
  <div class="col-md-12">
  <input type="text" name="lastname" id="lastname" placeholder="" class="form-control" data-validation="required">
   
  </div>
 
</div>
<div class="form-group">
  <label class="control-label col-md-4">Recruitment Source</label>
  <div class="col-md-12">
  <select id="recruitment" name="recruitment" data-placeholder="Enter Recruitment" class="form-control"onchange="show()" data-validation="required">
                          <option value=""></option>
                           <option value="Mynimo">MYNIMO</option>
                            <option value="Jobstreet">JOBSTREET</option>
                            <option value="Walk In">WALK IN</option>
                            <option value="REFERRAL">REFERRAL</option>
                           </select>
						   <div class="form-group" id="referral_text">
                             <input type="text" name="referral" id="referral" placeholder="Enter Referral" class="form-control" >
							 </div>
  </div>
 
</div>
<div class="form-group">
  <label class="control-label col-md-4">Status</label>
  <div class="col-md-12">
  <select id="status" name="status"  class="form-control">
     <option value=" "> </option>
                          <option value="pending">PENDING</option>
                           <option value="forinterview">FOR INTERVIEW</option>
                           <option value="doneinterview">DONE INTERVIEW</option>
                            <option value="blacklisted">BLACKLISTED</option>
                           </select>
                          
  </div>

</div>
<div class="form-group">
  <label class="control-label col-md-4">Tag</label>
  
        <div class="col-md-12">
     <select  id="tag_id"  name="tag_id" data-placeholder="" multiple="multiple" class="form-control" onchange="tag()" >
      
    </select>
    <div>
	<div class="form-group" id="tag_text">
      <input type="text" name="tag_temp" id="tag_temp" class="form-control" maxlength="524200"data-validation="required" readonly>
	  </div>
    </div>
      
        </div>
<div class="form-group">
  <label class="control-label col-md-4">School</label>
  
        <div class="col-md-12">
     <select  id="school_id"  name="school_id" data-placeholder="" multiple="multiple" class="form-control" onchange="school()" >
   
    </select>
	<div class="form-group" id="school_text">
      <input type="text" name="school_temp" id="school_temp" class="form-control" maxlength="524200"data-validation="required" readonly>
	  </div>
    </div>
      
        </div>
        <div class="form-group">
  <label class="control-label col-md-4">Course</label>
  
        <div class="col-md-12">
     <select  id="course_id"  name="course_id" data-placeholder="" multiple="multiple" class="form-control" onchange="course()" >
    
    </select>
	<div class="form-group" id="course_text">
      <input type="text" name="course_temp" id="course_temp" class="form-control" maxlength="524200"data-validation="required" readonly>
      </div>
    </div>
     
        </div>
         <div class="form-group">
  <label class="control-label col-md-4">Position</label>
  
        <div class="col-md-12">
     <select  id="position_id"  name="position_id" data-placeholder="" multiple="multiple" class="form-control" onchange="position()" >
     
    </select>
    <div class="form-group" id="position_text">
      <input type="text" name="position_temp" id="position_temp" class="form-control" maxlength="524200"data-validation="required" readonly>
	  </div>  
    </div>
      
        </div>

       <div class="form-group">
  <label class="control-label col-md-4">Date of Application</label>
  <div class="col-md-12">
  <div class='input-group date' id="applied_dt">               
                  <input class="form-control m-input" type="text" name="applied_dt"  data-validation="required" />
                  <span class="input-group-addon">
                          <i class="la la-calendar glyphicon-th"></i>
                        </span>
                  </div>

  </div>

</div>

<div class="form-group">
  <label class="control-label col-md-4">Remarks</label>
  <div class="col-md-12">
     <input type="hidden" name="num" id="num"  class="form-control" >
  <textarea name="description2" id="description2" placeholder="" class="form-control" > </textarea> 
  </div>
</div>
<div class="form-group">
                        <label class="control-label col-md-3"></label>
                        <div class="col-md-9">
                          <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Select File</span>
                            <!-- The file input field used as target for the file upload widget -->
                            <input id="fileupload" type="file" name="files[]">
                          </span>
                          <!-- The global progress bar -->
                          <!-- <div id="progress" class="progress">
                              <div class="progress-bar progress-bar-success"></div>
                          </div> -->
                        </div>
                      </div>
<div class="form-group">
                        <label class="control-label col-md-4">File Name</label>
                        <div class="col-md-12">
                          <input id="filename" name="filename" placeholder="" class="form-control" type="text" disabled data-validation="required">
                          <input id="doc_filename" name="doc_filename" placeholder="" class="form-control" type="hidden">
                        </div>
                      </div>

                      
                   <div class="modal-footer">
				   
                     <button type="submit" id="btnSave" onclick="save_document()" class="btn btn-success m-btn m-btn--custom m-btn--icon  btnNew">Save</button>
                      <button type="reset" class="btn btn-danger m-btn m-btn--custom m-btn--icon  cancel" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                  </div><!-- /.modal-content -->
                </div>
    </div>
  </div>
				</div>
			</div>
			<!--end::Portlet-->
		</div>
	</div>
</div>

<script src="<?php echo base_url("assets/global/js/vendor/jquery.ui.widget.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/js/jquery.iframe-transport.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/js/jquery.fileupload.js"); ?>"></script>
              
<script>
                    /*jslint unparam: true */
                    /*global window, $ */
                    $(function () {
                        'use strict';
                        // Change this to the location of your server-side upload handler:
                        var str = "<?php echo base_url();?>";   
                        var url = str + 'uploads/module/crs/';
                        $('#fileupload').fileupload({
                            url: url,
                            dataType: 'json',
                            done: function (e, data) {
                                $.each(data.result.files, function (index, file) {
                                    //$('<p/>').text(file.name).appendTo('#files');
                                    $('#doc_filename').val(file.name);
                                    $('#filename').val(file.name);
                                    //$('#doc_fullpath').val('<?php echo base_url();?>server/php/files/' + file.name);
                                });
                            },
                            progressall: function (e, data) {
                                var progress = parseInt(data.loaded / data.total * 100, 10);
                                $('#progress .progress-bar').css(
                                    'width',
                                    progress + '%'
                                );
                            }
                        }).prop('disabled', !$.support.fileInput)
                            .parent().addClass($.support.fileInput ? undefined : 'disabled');
                    });
                </script>