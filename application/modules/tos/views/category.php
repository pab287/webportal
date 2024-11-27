<div class="m-content">
	<div class="row">
		<div class="col-lg-12">

            <div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Add Category
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    ×
                                </span>
                            </button>
                        </div>
                        <form id="add_form" name="add_form">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="col-12 modal-body">
                                <div class="form-group">
                                    <label class="col-12 form-control-label">
                                    Name:
                                    </label>
                                    <div class="col-12">
                                        <input type='text' class="form-control" id="description" name="description" />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-submit btn-primary btnNew">
                                    Save
                                </button>
                            </div>
                        </form>
					</div>
                </div>
            </div>
            
            <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Edit Category
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    ×
                                </span>
                            </button>
                        </div>
                        <form id="edit_form" name="edit_form">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="col-12 modal-body">
                                <div class="form-group">
                                    <label class="col-12 form-control-label">
                                    Name:
                                    </label>
                                    <div class="col-12">
                                        <input type='text' class="form-control" id="edit_description" name="edit_description" v-model="vm_tab1.description"/>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-submit btn-primary btnNew">
                                    Save
                                </button>
                            </div>
                        </form>
					</div>
                </div>
            </div>

            <div class="modal fade" id="add_topic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Add Topic
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    ×
                                </span>
                            </button>
                        </div>
                        <form id="add_topic_form" name="add_topic_form">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="col-12 modal-body">
                                <div class="form-group row">
                                    <label class="col-2 form-control-label">
                                    Title:
                                    </label>
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="topic" name="topic"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-12">
                                        <input type="file" name="files" id="fileupload" multiple class="custom-file-input">
                                        <span class="custom-file-control" id="file_append"></span>
                                    </div>               
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" id="upload" class="btn btn-submit btn-primary btnNew">
                                    Save
                                </button>
                            </div>
                        </form>
					</div>
                </div>
            </div>

            <div class="modal fade" id="edit_topic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Edit Topic
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    ×
                                </span>
                            </button>
                        </div>
                        <form id="edit_topic_form" name="edit_topic_form">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="col-12 modal-body">
                                <div class="form-group row">
                                    <label class="col-2 form-control-label">
                                    Title:
                                    </label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" id="edit_topic_desc" name="edit_topic_desc" v-model="vm_tab2.name"/>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-submit btn-primary btnNew">
                                    Save
                                </button>
                            </div>
                        </form>
					</div>
                </div>
            </div>
            
            <div class="modal fade" id="add_file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Add File
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    ×
                                </span>
                            </button>
                        </div>
                        <form id="add_file_form" name="add_file_form">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="col-12 modal-body">
                                <div class="form-group">
                                    <div class="col-12">
                                        <input type="file" name="files" id="add_fileupload" multiple class="custom-file-input">
                                        <span class="custom-file-control" id="add_file_append"></span>
                                    </div>               
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" id="add_upload" class="btn btn-submit btn-primary btnNew">
                                    Save
                                </button>
                            </div>
                        </form>
					</div>
                </div>
            </div>
            
            <div class="modal fade" id="update_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Update File
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    ×
                                </span>
                            </button>
                        </div>
                        <form id="update_form" name="update_form">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="col-12 modal-body">
                                <div class="form-group">
                                    <div class="col-12">
                                        <input type="file" name="files" id="update_fileupload" class="custom-file-input">
                                        <span class="custom-file-control" v-text="vm_tab3.filename" id="update_file_append"></span>
                                    </div>               
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" id="update_upload" class="btn btn-submit btn-primary btnNew">
                                    Save
                                </button>
                            </div>
                        </form>
					</div>
                </div>
            </div>

		</div>
	</div>

    <div class="row" id="content">
        <div class="col-lg-4" id="category-navigation">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                             Categories
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div  class="form-group">
                        <a href="#" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle='modal' data-target='#add_modal'>
                            <span> 
                                <i class="la la-plus"></i>
                                New
                            </span>
                        </a>   
                    </div>
                    <div>   
                        <form id="tree">
                            <div id="treeview" class="tree-demo jstree jstree-1 jstree-default" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1_2" aria-busy="false">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div style="position: fixed;right: 30px;" id="preview-container">
        <!-- class="col-lg-8" -->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                File preview
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form id="file_preview">
                        <div id='file_dl' class='m-widget4'>
                            <div id="head_file">
                            </div>
                            <div class='m-widget4__item col-12'>         
                                <div class="row col-12">
                                    <div class="col-3">
                                        <div class='m-widget4__ext'>
                                            <span class='m-widget4__icon m--font-brand'>
                                                <i class='fa flaticon-file-1'></i>
                                            </span>
                                        </div>
                                        <div class='m-widget4__info'>    
                                            Name: <span class='m-widget4__text' v-text="vm_tab4.filename"></span>
                                            <br>
                                            Size: <span class='m-widget4__text' v-text="vm_tab4.filesize"></span> kb
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class='m-widget4__info '>    
                                            Created by: <span class='m-widget4__text' v-text="vm_tab4.created_name"></span>
                                            <br>
                                            Created date: <span class='m-widget4__text' v-text="moment(vm_tab4.created_dt).format('LL')"></span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class='m-widget4__info '>    
                                            Modify by: <span class='m-widget4__text' v-text="vm_tab4.modify_name"></span>
                                            <br>
                                            Modify date: <span class='m-widget4__text' v-text="moment(vm_tab4.modify_dt).format('LL')"></span> 
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class='m-widget4__ext'>
                                            <span class='m-widget4__number m--font-info'>
                                                <div class='m-widget4__ext'>
                                                    <a href='#' id="download_file" class='m-widget4__icon'>
                                                        <i class='la la-download'></i>
                                                    </a>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </form>

                    <div id="table_preview">
                        <div class="m-widget1">
                            <div id='path' class="row"></div>
                            <div id="container" style='text-align:center'></div>
                            <div id='get_files' class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                <table class="table table-striped table-bordered" id="table-topic" width="100%">
                                    <tbody>	
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>