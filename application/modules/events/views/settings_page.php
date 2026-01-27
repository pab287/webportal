<style>
    .color-picker-input {
        height: 38px;          /* match Bootstrap input height */
        padding: 0;
        border-radius: 6px;
        cursor: pointer;
    }

    /* .color-picker-input::-webkit-color-swatch-wrapper {
        padding: 0;
    } */

    /* .color-picker-input::-webkit-color-swatch {
        border-radius: 6px;
        border: none;
    } */

</style>
<div class="m-content">
    <div class="m-portlet" id="m_portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar-2"></i>
                    </span>
                    <h3 class="m-portlet__head-text" id="page_title">
                        EVENTS PAGE SETTINGS
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <a href="javascript:void(0)" class="custom-btn-link" onclick="openArchive()">
                    <span class="m--font-bolder" id="archive_text">Archive</span>
                </a>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <button id="newOption" type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill mb-2 btnNew" data-toggle="modal" data-target="#new_option">
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
                                <th>COLOR</th>
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
                                        <input id="option_name" type="text" class="form-control" data-validation="required" name="name">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="option_type">OPTION TYPE</label>
                                        <input id="option_type" type="text" class="form-control" data-validation="required" name="type">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="option_color">OPTION COLOR</label>
                                        <div class="form-group input-group">
                                            <input id="option_color" type="color" class="form-control form-control-color">
                                            <input type="text" class="form-control" id="option_color_text" placeholder="HEX CODE" data-validation="required" name="hex_code">
                                        </div>
                                        <small class="form-text text-muted">Choose a color for this option</small>
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
                        <h5 class="modal-title">EDIT OPTIONS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="update_option_form" onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                        <input type="hidden" name="id" v-model="optionSelected.id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="option_name">OPTION NAME</label>
                                        <input type="text" class="form-control" name="name" v-model="optionSelected.name" data-validation="required">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="option_name">OPTION TYPE</label>
                                        <input type="text" class="form-control" name="type" v-model="optionSelected.type" data-validation="required">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="option_color">OPTION COLOR</label>
                                        <div class="form-group input-group">
                                            <input id="edit_option_color" type="color" class="form-control form-control-color">
                                            <input type="text" class="form-control" id="edit_option_color_text" placeholder="HEX CODE" data-validation="required" name="hex_code">
                                        </div>
                                        <small class="form-text text-muted">Choose a color for this option</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success btn-submit btnSave" type="submit"></i>UPDATE</button>
                            <button class="btn btn-danger text-white btnBack" data-dismiss="modal"></i>CLOSE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>