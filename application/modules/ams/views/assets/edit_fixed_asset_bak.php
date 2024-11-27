<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--tabs m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Asset View
                            </h3>
                        </div>
                    </div>

                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger"
                            role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_portlet--details" role="tab" aria-expanded="true">
                                    Details
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet--components" role="tab" aria-expanded="false">
                                    Components
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet--documents" role="tab" aria-expanded="false">
                                    Documents
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet--accountability" role="tab" aria-expanded="false">
                                    Accountability
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet--borrowing" role="tab" aria-expanded="false">
                                    Borrowing
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="m_portlet--details" aria-expanded="true">
                            <?php $this->load->view("edit_fixed_asset_tabs/details"); ?>
                        </div>
                        <div class="tab-pane" id="m_portlet--components" aria-expanded="false">
                            <?php $this->load->view("edit_fixed_asset_tabs/components"); ?>
                        </div>
                        <div class="tab-pane" id="m_portlet--documents" aria-expanded="false">
                            <?php $this->load->view("edit_fixed_asset_tabs/documents"); ?>
                        </div>
                        <div class="tab-pane" id="m_portlet--accountability" aria-expanded="false">
                            <?php $this->load->view("edit_fixed_asset_tabs/accountability"); ?>
                        </div>
                        <div class="tab-pane" id="m_portlet--borrowing" aria-expanded="false">
                            <?php $this->load->view("edit_fixed_asset_tabs/borrowing"); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
</div>
<?php $this->load->view("modals/new_component"); ?>
<?php $this->load->view("modals/add_component"); ?>
<?php $this->load->view("modals/add_document"); ?>
<?php //$this->load->view("templates/upload/content");?>
<div id="fileupload-modal_content"></div>
<div class="modal fade document-modal-container" tabindex="-1" role="dialog"></div>