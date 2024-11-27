<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                <?= $title ?>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
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
                                                        <a href="javascript:void()" class="m-nav__link" data-toggle="modal" data-target="#modal_archives">
                                                            <i class="m-nav__link-icon flaticon-open-box"></i>
                                                            <span class="m-nav__link-text">
																Archives
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="<?php echo base_url('ams/assets/status') ?>" class="m-nav__link">
                                                            <i class="m-nav__link-icon flaticon-clipboard"></i>
                                                            <span class="m-nav__link-text">
																Status
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
                    <div class="row">
                        <div class="col-xl-4">
                            <a href="<?= base_url('ams/vehicles/vehicle_masterfile') ?>"
                               class="btn btn-outline-metal m-btn m-btn--icon m-btn--custom m-btn--pill m-btn--hover-primary m-btn--air btnNew"
                               title="Return to Fixed Asset Masterfile">
                                <span>
                                    <i class="la la-arrow-left"></i>
                                    <span>Back</span>
                                </span>
                            </a>
                        </div>
                        <div class="col-xl-5"></div>
                        <div class="col-xl-3">
                            <div class="form-group m-form__group">
                                <div class="m-input-icon m-input-icon--left">
                                    <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                                        <span>
                                            <i class="la la-binoculars"></i>
                                        </span>
                                    </span>
                                    <div class="input-group">
                                        <input type="search" class="form-control" placeholder="Search Here..."
                                               style="height: auto;" id="search-vehicle-with-status">
                                        <span class="input-group-btn">
                                            <button class="btn btn-secondary" title="Clear Filter" style="border-color: #cdcdcd;" onclick="clearSearch()">
                                                <i class="la la-close"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll m--margin-top-20">
                        <table class="table table-striped table-bordered" id="table-vehicles-with-status" width="100%">
                            <thead>
                            <tr>
                                <th>Images</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Category</th>
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
</div>