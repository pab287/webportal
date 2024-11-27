<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Import Employee
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="col-md-6">
                        <div class="m-section__content">   
                            <div class="m-demo">
                                <div class="m-demo__preview">
                                <span class="btn btn-success fileinput-button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span>Select file</span>
                                    <input type="file" id="fileupload" name="files">
                                </span>
                                <div id="progress" class="progress">
                                    <div class="progress-bar progress-bar-success"></div>
                                </div>
                                <div id="files" class="files"></div>
                                </div>
                            </div> 
                        </div>
                        <div class="m-section__content">   
                            
                        </div>
                    </div>

                   <!--begin: Datatable -->
                   <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm" style="overflow-y: auto;">
                        <table class="table table-striped table-bordered" id="table-employee_import" width="100%">
                           
                        </table>
                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div> 