<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-4">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div id="left_pane-card" class="m-card-profile">
                        <div class="m-card-profile__title m--hide">Title Profile</div>
                        <div class="m-card-profile__pic m-card-user__pic">
                            <div class="m-card-profile__pic-wrapper">
                                <img id="image--holder" v-bind:src="left_pane.display_avatar" alt="">
                            </div>
                        </div>
                        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                            <li class="m-nav__separator m-nav__separator--fit"></li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnUpload" href="javascript:void(0);" data-toggle="modal" data-target="#modalUpdatePhoto">
                                    <i class="m-nav__link-icon flaticon-profile-1"></i>
                                    <span class="m-nav__link-text">Upload Profile Photo</span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnBack" href="<?php echo base_url("hris/transaction/hire"); ?>">
                                    <i class="m-nav__link-icon flaticon-list"></i>
                                    <span class="m-nav__link-text">Back to Hire List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-8">
            <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_user_profile_tab_1" role="tab" aria-expanded="true">Personal Information</a>
                        </li>
                    </ul>
                </div>
                <!-- hire from crs -->
                <div class="m-portlet__head-tools">
                    <ul class="m-portlet__nav">
                        <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push">
                            <button class="nav-link btn btn-info btn-sm" href="javascript:void(0)" aria-expanded="true" onclick="crsModal()">Hire From CRS</button>
                        </li>
                    </ul>
                </div>
                <!-- hire from crs -->
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="m_user_profile_tab_1" aria-expanded="true">
                    <?php echo $this->load->view("hris/masterfile/employee/tabs/new_personal_information"); ?>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpdatePhoto" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalUpdatePhotoContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Photo</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
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
                <div class="modal-footer">
                    <button class="btn btn-danger modalClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTempContent" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Temp Title</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">test</div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="crs-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hire From CRS</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            Applicants
                        </div>
                        <div class="col-md-12">
                            <select id="crs-applicants" name="" id="" class="form-control">
                                <option value="">Select an Option</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="submit-selected" class="btn btn-primary btnNew"> Submit </button>
                </div>
            </div>
        </div>
    </div>
</div>