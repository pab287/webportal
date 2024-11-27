<style>
    .btn.btn-default:hover, .btn.btn-default.active,
    .btn.btn-default:active, .btn.btn-default:focus, .show > .btn.btn-default.dropdown-toggle,
    .btn.btn-secondary:hover, .btn.btn-secondary.active, .btn.btn-secondary:active,
    .btn.btn-secondary:focus, .show > .btn.btn-secondary.dropdown-toggle {
        background-color: #716aca;
        border-color: #716aca;
        color: #fff;
    }

    .custom-fullname a {
        color: #1b1b1b;
    }

    .select2-selection__rendered {
        font-weight: bold;
    }
</style>

<?php
    $current_emp_status = isset($_GET['status']) ? $_GET['status'] : 'Active';
    $current_search_value = isset($_GET['company']) ? $_GET['company'] : '';
    $current_sex = isset($_GET['sex']) ? $_GET['sex'] : '';
?>

<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Employee Masterfile
                            </h3>
                        </div>
                    </div>
                    <!-- <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#"
                                   class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                                    <i class="la la-ellipsis-h m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                          style="left: auto; right: 22.5px;"></span>
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
                                                        <a href="javascript:void()" class="m-nav__link btnNew"
                                                           data-toggle="modal"
                                                           data-target="#modal_archives">
                                                            <i class="m-nav__link-icon flaticon-open-box"></i>
                                                            <span class="m-nav__link-text">
																Archives
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void()" class="m-nav__link btnNew"
                                                           data-toggle="modal"
                                                           data-target="#modal_import">
                                                            <i class="m-nav__link-icon la la-upload"></i>
                                                            <span class="m-nav__link-text">
																Import
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
                    </div> -->
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1 d-inline">
                                <div class="row">
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" class="m--font-bold">FILTER BY EMPLOYEE STATUS</label>
                                        <select name="emp_status" id="emp_status" class="form-control">
                                            <option value="All">All</option>
                                            <?php foreach ($employee_status as $_status) { ?>
                                                <option value="<?= $_status->employee_status ?>"
                                                        <?= $_status->employee_status === $current_emp_status ? 'selected' : '' ?>
                                                >
                                                    <?= $_status->employee_status === 'Black Listed' ? 'BLACKLISTED' : $_status->employee_status ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-xl-2 col-lg-2 col-md-2 com-sm-12">
                                        <label for="" class="m--font-bold">SEX</label>
                                        <select name="emp_sex" id="emp_sex" class="form-control">
                                            <option value="All">All</option>
                                            <option value="Male" <?= "Male" === $current_sex ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= "Female" === $current_sex ? 'selected' : '' ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right"
                                 style="margin-top: 12px;">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid"
                                           placeholder="Search..." id="generalSearch"
                                           value="<?= $current_search_value ?>" autocomplete="off">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-employee" width="100%">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Employee</th>
                                <th>Performance Rating</th>
                                <th>201 File Status</th>
                                <th>Work Status</th>
                                <th>Date Hired</th>
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
            <!--end::Portlet-->
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="employee-archive-remarks-dialog">
        <div class="modal-dialog" role="document">
            <form action=""
                  id="frm-employee-archive-dialog"
                  onsubmit="event.preventDefault(); archiveEmployee(this);">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archive Employee Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Remarks</label>
                            <textarea name="archive_remarks" class="form-control m-input"
                                      data-validation="required"></textarea>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Archive</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal_import">
        <div class="modal-dialog" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Employee Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                </div>
            </div>
        </div>
    </div>

</div>

<?php
    $this->load->view('modals/performance_rating_remarks');
?>
