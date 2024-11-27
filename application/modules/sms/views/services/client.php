<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Recipients
                        <small>

                        </small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row m-row--no-padding m-row--col-separator-xl">
                <div class="col-md-12 col-lg-12 col-xl-6">
                    <!--begin:: Widgets/Stats2-1 -->
                    <div class="m-widget1">
                        <div class="row mb-2">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <button type="button" onclick="remove_all()"
                                        class="btn btn-sm btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill btnNew">
                                    Remove All Recipients
                                </button>
                            </div>
                            <div class="offset-xl-2 offset-lg-2 offset-md-2 col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <input type="text" class="form-control m-input"
                                           placeholder="Search Recipient" id="search-recipient">
                                </div>
                            </div>
                        </div>

                        <table class="m-datatable__table table table-striped table-bordered" id="tbl-recipients" width="100%">
                            <thead>
                            <th>Acc No.</th>
                            <th>Mobile No.</th>
                            <th>Name</th>
                            <th>Data 1</th>
                            <th>Data 2</th>
                            <th>Data 3</th>
                            <th>Action</th>
                            </thead>
                        </table>
                    </div>
                    <div class="m-widget1">
                        <h5 class="mb-2">Import Recipient</h5>
                        <form id="frm-upload_recipients" method="post" enctype="multipart/form">
                            <div class="form-group m-form__group">
                                <label class="custom-file">
                                    <input type="file" name="files" id="fileupload" required class="custom-file-input">
                                    <span class="custom-file-control" id="file_append"></span>
                                </label>
                            </div>
                            <div class="form-group m-form__group">
                                <button type="button" class="btn btn-brand btnNew" id="upload">
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>
                    <!--end:: Widgets/Stats2-1 -->
                </div>
                <div class="col-md-12 col-lg-12 col-xl-6">
                    <form id="frmSendSms">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <!--begin:: Widgets/Stats2-2 -->
                        <div class="m-widget1">
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-2 col-form-label">
                                    Header
                                </label>
                                <div class="col-10">
                                    <select id="head" name="head" class="form-control select2">
                                        <option selected="selected">Hi</option>
                                        <option>Hello</option>
                                        <option>Dear</option>
                                        <option>Good day</option>
                                        <option>Greetings</option>
                                        <option>Good morning</option>
                                        <option>Good afternoon</option>
                                        <option>Good evening</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    Message
                                </label>
                                <div class="col-10">
                                    <textarea id="message" name="message" placeholder="" rows="6" class="form-control" type="text"></textarea>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-2 col-form-label">
                                    Footer
                                </label>
                                <div class="col-10">
                                    <textarea id="foot" name="foot" placeholder="" rows="2" class="form-control" type="text">This is a computer generated message. Please Do not reply.</textarea>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-2 col-form-label">
                                    Template
                                </label>
                                <div class="col-9">
                                    <select id="template" name="template">

                                    </select>
                                </div>
                                <button type="button" class=" btn btn-success m-btn btnNew" id="temp_button"
                                        data-toggle="m-tooltip" data-placement="bottom" title="" data-delay='{"show": 200, "hide": 0}'
                                        data-original-title="Click to load template message."
                                        data-skin="dark">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="m-widget1">
                            <button type="submit" class="btn btn-info">
                                Send Sms
                            </button>
                            <!-- <button type="button" class="btn btn-success btnNew">
                                Update Status
                            </button> -->
                        </div>
                    </form>
                    <!--begin:: Widgets/Stats2-2 -->
                </div>
            </div>

        </div>
    </div>

    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Outbox
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body  m-portlet__body--no-margin">
            <div class="row">
                <div class="col-lg-12">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered" id="table-outbox" width="100%">
                            <thead>
                            <tr>
                                <th>Mobile No.</th>
                                <th>Recipient</th>
                                <th>Message</th>
                                <!-- <th>Message ID</th> -->
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>