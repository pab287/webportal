<?php
    $name = $this->core_layout->getDisplayName(
        array("lastname" => $data->lastname,
            "firstname" => $data->firstname,
            "middlename" => $data->middlename,
            "suffix" => $data->suffix));
?>

<style>
    .avatar-buttons-container {
        left: 0;
        right: 0;
        bottom: 0;
        height: 16%;
        padding: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: row;
        justify-content: center;
    }
</style>

<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-4">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div id="left_pane-card" class="m-card-profile">
                        <div class="m--hide">
                            <button class="btn btn-primary m-btn m-btn--icon m-btn--icon-only m-btn--pill">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                        </div>
                        <div class="m-card-profile__pic m-card-user__pic">
                            <div class="m-card-profile__pic-wrapper position-relative"
                                 style="overflow: hidden;">
                                <img id="image--holder" :src="left_pane.display_avatar" alt="">
                            </div>
                        </div>
                        <div class="m-card-profile__details">
                            <span class="m-card-profile__name">
                                <?= $name["display_name_1"] ?>
                            </span>
                        </div>
                        <div class="d-flex flex-row mt-3">
                            <div class="flex-shrink-1 flex-grow-0">
                                <img class="company-logo pt-1" src="<? /*= base_url($data->company_logo) */ ?>"
                                        alt="">
                            </div>
                            <div class="d-flex flex-shrink-1 flex-grow-1 pl-4 flex-column">
                                <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                    <div class="col-xl-6">
                                        <span>ID No.</span>
                                    </div>
                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                        <span class="m--font-bolder"><?= $data->idno ?></span>
                                    </div>
                                </div>
                                <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                    <div class="col-xl-6">
                                        <span>Biometric No.</span>
                                    </div>
                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                        <span class="m--font-bolder"><?= $data->biometricno ?></span>
                                    </div>
                                </div>
                                <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                    <div class="col-xl-6">
                                        <span>Status:</span>
                                    </div>
                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                        <span class="m--font-bolder"><?= $data->work_status ?></span>
                                    </div>
                                </div>
                                <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                    <div class="col-xl-6">
                                        <span>Employee Status:</span>
                                    </div>
                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                        <span class="m--font-bolder"><?= $data->employee_status ?></span>
                                    </div>
                                </div>
                                <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                    <div class="col-xl-6">
                                        <span>Date Started:</span>
                                    </div>
                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                        <span class="m--font-bolder">
                                        <?php
                                        $date_start = $data->date_start != '0000-00-00' ? new DateTime($data->date_start) : "";
                                        echo !empty($date_start) ? $date_start->format("M d, Y") : "N/A";
                                        ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-1 text-sm-center text-xl-left text-lg-left row" <?=($data->employee_status != 'Active' && ($data->date_end != null || $data->date_end != '0000-00-00')) ? '' : 'hidden' ?> >
                                    <div class="col-xl-6">
                                        <span>Date Ended:</span>
                                    </div>
                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                        <span class="m--font-bolder">
                                            <?php
                                            $date_end = $data->date_end != '0000-00-00' ? new DateTime($data->date_end) : "";
                                            echo !empty($date_end) ? $date_end->format("M d, Y") : "N/A";
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                    <div class="col-xl-6">
                                        <span>Company:</span>
                                    </div>
                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                        <span class="m--font-bolder"><?=$data->company ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                            <li class="m-nav__separator m-nav__separator--fit"></li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnBack" href="<?php echo base_url("payroll/employee/employee_masterlist"); ?>">
                                    <i class="m-nav__link-icon fa fa-arrow-left"></i>
                                    <span class="m-nav__link-text">Back to Employee List</span>
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
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_user_profile_tab_3" role="tab" aria-expanded="true">Employment
                                    Data</a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_user_profile_tab_4" role="tab" aria-expanded="true">Payroll Information</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane" id="m_user_profile_tab_3" aria-expanded="true">
                        <?php echo $this->load->view("payroll/employee_profile/tabs/employment_data"); ?>
                        <?php echo $this->load->view("payroll/employee_profile/tabs/other_information/employment_content"); ?>
                    </div>
                    <div class="tab-pane active" id="m_user_profile_tab_4" aria-expanded="true">
                        <?php echo $this->load->view("payroll/employee_profile/tabs/payroll_information/payroll_content"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade document-modal-container"
         data-keyboard="false" data-backdrop="static"
         modal-exempt-custom tabindex="-1"
         role="dialog"></div>
</div>
