<div class="m-content">
    <!--begin::Portlet-->
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Hired Personnel
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools"></div>
        </div>
        <div class="m-portlet__body">
            <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">&nbsp;</div>
                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    </div>
                </div>
            </div>

            <!--begin: Datatable -->
            <div class="table-responsive-sm">
                <table class="table table-hover table-bordered" id="table-hired" width="100%">
                    <thead>
                    <tr>
                      <th></th>
                      <th>id</th>
                        <th>Name</th>
                        <th>School</th>
                        <th>Course</th>
                        <th>Position</th>
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
        </div>
    </div>
</div>
<div class="modal fade" id="modal-hired-resume-dialog" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modal-hired_content">
            <div class="modal-header">
                <h3 class="modal-title">HIRED RESUME</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form m-form">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Firstname</label>
                            <p class="form-control m--marginless">{{row.firstname}}</p>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Lastname</label>
                            <p class="form-control m--marginless">{{row.lastname}}</p>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Date of application</label>
                            <p class="form-control m--marginless">{{row.applied_dt}}</p>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Recruitment Source</label>
                            <p class="form-control m--marginless">{{row.recruitment}}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Tag</label>
                            <div class="form-control custom-p_textarea m--marginless">
                                <template v-if="row.tag1_count > 0">
                                    <span v-for="(item, index) in row.tag1" class="m-badge m-badge--success m-badge--wide m--margin-bottom-5">{{item}}</span>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">School</label>
                            <div class="form-control custom-p_textarea m--marginless">
                                <template v-if="row.school_count > 0">
                                    <span v-for="(item, index) in row.school" class="m-badge m-badge--success m-badge--wide m--margin-bottom-5">{{item}}</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Course</label>
                            <div class="form-control custom-p_textarea m--marginless">
                                <template v-if="row.course_count > 0">
                                    <span v-for="(item, index) in row.course" class="m-badge m-badge--success m-badge--wide m--margin-bottom-5">{{item}}</span>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Position</label>
                            <div class="form-control custom-p_textarea m--marginless">
                                <template v-if="row.position_count > 0">
                                    <span v-for="(item, index) in row.position" class="m-badge m-badge--success m-badge--wide m--margin-bottom-5">{{item}}</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label class="control-label">Remarks</label>
                    <p class="form-control custom-p_textarea m--marginless">{{row.description}}</p>
                </div>
                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                <div class="form-group mt-3">
                    <h5>Attachments</h5>
                    <div class="m-widget2">
                        <div class="m-widget2__item m-widget2__item--danger">
                            <div class="m-widget2__checkbox">
                                <div class="m-widget2__img m-widget2__img--icon">
                                    <img src="<?php echo base_url("assets/images/file_icons/pdf.svg"); ?>" alt="">
                                </div>
                            </div>
                            <div class="m-widget2__desc">
                                <span class="m-widget2__text">{{row.filename}}</span>
                                <span class="m-widget2__user-name">
                                    <span class="m-widget2__link">&nbsp;</span>
                                </span>
                            </div>
                            <div class="m-widget2__actions">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
            <div class="modal-footer">
                <button type="reset" class="btn btn-danger m-btn m-btn--custom m-btn--icon  cancel" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<style>
#dropdownMenuButton:after {
    display: none;
}
table.dataTable {
    table-layout: fixed;
    word-break: break-all;
}
table#table-hired td {
    word-break: break-word;
}
.m-widget2 .m-widget2__item .m-widget2__img.m-widget2__img--icon img{
    width: 3rem;
}
.m-widget2__actions {
    width: 100% !important;
    text-align: right !important;
}
</style>