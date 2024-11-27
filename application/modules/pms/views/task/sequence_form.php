<div class="m-content">
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Sequence Form
                            <small>House Blocks</small>
                        </h3>
                    </div>
                </div>
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--accent  m-tabs-line--right" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_portlet_tab_grid" role="tab">
                                <i class="fa fa-th"></i>
                                Grid
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_tab_list" role="tab">
                                <i class="fa fa-th-list"></i>
                                List
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="tab-content">
                    <div class="tab-pane active" id="m_portlet_tab_grid">
                        <?php echo $this->load->view("pms/task/form_content/form_grid", null, true); ?>
                    </div>
                    <div class="tab-pane " id="m_portlet_tab_list">
                        <?php echo $this->load->view("pms/task/form_content/form_list", null, true); ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="modal-sequence_form-list" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-m modal-xl" role="document">
            <div id="sequenceFormModalContent" class="modal-content">
                <?php echo $this->load->view("pms/task/modal_content/form_list_action", null, true); ?>
            </div>
        </div>
    </div>
</div>