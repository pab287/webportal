<div class="m-content">
    <div class="m-portlet" id="m_portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar-2"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        EVENTS PAGE SETTINGS
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <button type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill mb-2 btnNew" data-toggle="modal" data-target="#new_option">
                        <i class="la la-plus"></i>
                        ADD OPTION
                    </button>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-events_settings" style="width: 100%;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>OPTION NAME</th>
                                <th>TYPE</th>
                                <th>CREATED BY</th>
                                <th>CREATED AT</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade show" id="new_option" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content" id="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">NEW OPTION</h5>
                        <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form id="new_option_form" onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="option_name">OPTION NAME</label>
                                        <input type="text" class="form-control" id="option_name" name="name">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="option_name">OPTION TYPE</label>
                                        <input type="text" class="form-control" id="option_name" name="type">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success btn-submit btnSave" type="submit"></i>SAVE</button>
                            <button class="btn btn-danger text-white btnBack" data-dismiss="modal"></i>CLOSE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade show" id="edit_options" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">EDIT OPTIONs</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>