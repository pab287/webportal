<style>
    .textarea-view {
        height: auto !important; 
        min-height: 120px;
        max-height: 500px;
        white-space: normal;
        overflow-y: auto;
        background-color: #fff;
        cursor: default;
        resize: none;
        line-height: 1.6;
    }

    .textarea-view p {
        margin-bottom: 0.5rem;
    }

    .textarea-view ul,
    .textarea-view ol {
        padding-left: 1.5rem;
    }

</style>
<div class="m-content" id="edit_rfi_content">
    <div class="row">
        <div class="col-md-8 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Request for Information details
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools text-align-right">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line--primary" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#rfiForm" role="tab">
                                    <h5 class="m-portlet__head-text">FORM</h5>
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#replyForm" role="tab">
                                    <h5 class="m-portlet__head-text">REPLY</h5>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <dvi class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 tab-content">
                                    <div class="tab-pane active" id="rfiForm">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-control-label mb-2">
                                                                Project Name
                                                            </div>
                                                            <div class="form-control" v-text="content.project_name"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-control-label mb-2">
                                                                Project Location
                                                            </div>
                                                            <div class="form-control" v-text="content.project_location"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div for="prepared_dt" class="form-control-label mb-2">
                                                                Requested By
                                                            </div>
                                                            <div class="form-control" v-text="content.requested_by_name"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div for="reply_needed" class="form-control-label mb-2">
                                                                Reply Needed
                                                            </div>
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="la la-calendar"></i></span>
                                                                <div class="form-control" v-text="formatDate(content.reply_needed)"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-separator m-separator--dashed m-separator--md"></div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div for="consultant" class="form-control-label mb-2">
                                                                ATTENTION
                                                            </div>
                                                            <div class="form-control" v-text="content.consultant_name"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-control-label mb-2">
                                                                REQUEST TYPE
                                                            </div>
                                                            <div class="form-control" v-text="content.request_type"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-separator m-separator--dashed m-separator--md"></div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="form-control-label mb-2">
                                                                INFORMATION NEEDED
                                                            </div>
                                                            <div class="form-control textarea-view"
                                                                v-html="content.needed_info">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-separator m-separator--dashed m-separator--md"></div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label class="form-control-label">
                                                            ATTACHMENTS
                                                        </label>
                                                    </div>
                                                </div>
                                                <template v-if="attachments.length >= 1">
                                                    <div class="row col-12 border rounded p-2 mx-auto">
                                                        <template v-for="(item, index) in attachments">
                                                            <div v-bind:class="getClass(item.filename)">
                                                                <div class="m-widget4__item d-flex align-items-center pt-0" @click="openFile(item.filename)">
                                                                    <div class="m-widget4__img m-widget4__img--icon">
                                                                        <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                                                    </div>
                                                                    <div class="m-widget2__desc">
                                                                        <span class="m-widget4__text">{{ item.filename.length > 30 ? item.filename.slice(0, 30) + '...' : item.filename }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="col-12 text-center">
                                                        <strong>No attachments</strong>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="replyForm">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    REMARKS
                                                </div> 
                                                <div class="form-control textarea-view" v-html="reply.reply">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="m-separator m-separator--dashed m-separator--md"></div>

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-control-label">
                                                    ATTACHMENTS
                                                </label>
                                            </div>
                                        </div>
                                                <template v-if="replyAttachments.length >= 1">
                                                    <div class="row col-12 border rounded p-2 mx-auto">
                                                        <template v-for="(item, index) in replyAttachments">
                                                            <div v-bind:class="getClass(item.filename)">
                                                                <div class="m-widget4__item d-flex align-items-center pt-0" @click="openFile(item.filename)">
                                                                    <div class="m-widget4__img m-widget4__img--icon">
                                                                        <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                                                    </div>
                                                                    <div class="m-widget2__desc">
                                                                        <span class="m-widget4__text">{{ item.filename.length > 30 ? item.filename.slice(0, 30) + '...' : item.filename }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="col-12 text-center">
                                                        <strong>No attachments</strong>
                                                    </div>
                                                </template>
                                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <div class="form-control-label mb-2">
                                                        Reply From
                                                    </div>
                                                    <div class="form-control" v-text="reply.created_by_name"></div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <div class="form-control-label mb-2">
                                                        Company
                                                    </div>
                                                    <div class="form-control" v-text="reply.company_name"></div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <div class="form-control-label mb-2">
                                                        Position
                                                    </div>
                                                    <div class="form-control" v-text="reply.position_name"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button class="btn btn-primary" v-show="reply.status == 'pending'">APPROVE</button>
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-12">
                                                <h5>REPLY FROM:</h5>
                                            </div>
                                        </div> -->
                                        <!-- <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <div class="form-control-label mb-2">
                                                        COMPANY
                                                    </div>
                                                    <div class="form-control">HOMEWORLD</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <div class="form-control-label mb-2">
                                                        NAME
                                                    </div>
                                                    <div class="form-control">CHRISTIAN MARC NICO ALINGASA</div>
                                                </div>
                                            </div>
                                            <div class="m-separator m-separator--dashed m-separator--md"></div>
                                        </div> -->
                                        <!-- <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="information_needed" class="form-control-label">
                                                        REMARKS
                                                    </label>
                                                    <textarea name="information_needed" id="information_needed" class="form-control" data-validation="required"></textarea>
                                                <span id="information_needed-error" class="help-block form-error" style="display: none;">This is a required field</span>
                                                </div>
                                            </div>
                                        </div> -->
                                        <!-- <div class="row">
                                            <div class="col-12 text-right">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div> -->
                                        <!-- <div class="row pb-2">
                                            <div class="col-12">
                                                <label for="fileupload" class="form-control-label">
                                                    ATTACHMENTS
                                                </label>
                                                <div class="form-group">
                                                    <span class="btn btn-success fileinput-button">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        <span>SELECT FILE</span>
                                                        <input type="file" id="fileupload" name="files[]" accept=".pdf, .docx, application/pdf, .jpg" multiple>
                                                    </span>
                                                </div>
                                            </div>
                                        </div> -->
                                        <!-- <template v-if="uploadattachments.uploadedFiles.length >= 1">
                                            <div class="row col-12">
                                                <template v-for="(item, index) in uploadattachments.uploadedFiles">
                                                    <div v-bind:class="getClass(item.type)">
                                                        <div class="m-widget4__item d-flex align-items-center pt-0">
                                                            <div class="m-widget4__img m-widget4__img--icon">
                                                                <img v-bind:src="getExtension(item.type)" alt="" height="50" width="50">
                                                            </div>
                                                            <div class="m-widget2__desc">
                                                                <span class="m-widget4__text">{{ item.name.length > 50 ? item.name.slice(0, 50) + '...' : item.name }}</span>
                                                            </div>
                                                            <div class="m-widget2__actions ml-auto">
                                                                <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" v-on:click="fileDelete(index)">
                                                                    <i class="m-nav__link-icon flaticon-circle"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template> -->
                                        <!-- <template v-else>
                                            <div class="col-12">
                                                <strong><h5>NO ATTACHMENTS</h5></strong>
                                            </div>
                                        </template> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </dvi>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                OTHER INFORMATION
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="form-control-label mb-2">
                                    STATUS
                                </div>
                                <div class="form-control">PENDING REPLY</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary">APPROVE</button>
                            <button class="btn btn-success" data-toggle="modal" data-target="#replyModal">
                                REPLY
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="fileViewModal">
        <div class="modal-dialog modal-extra-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ATTACHMENTS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe :src="filePath" width="100%" height="600px" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="replyModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">REPLY TO THIS REQUEST</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="reply_form" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-control-label mb-2">
                                        INFORMATION NEEDED
                                    </div>
                                    <div class="form-control textarea-view"
                                        v-html="content.needed_info">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="information_needed" class="form-control-label m--font-bold">
                                        REPLY
                                    </label>
                                    <textarea name="reply" id="information_needed" class="form-control" data-validation="required"></textarea>
                                    <span id="information_needed-error" class="help-block form-error" style="display: none;">This is a required field</span>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row pb-2">
                            <div class="col-12">
                                <label for="fileupload" class="form-control-label m--font-bold">
                                    ATTACHMENTS
                                </label>
                                <div class="form-group">
                                    <span class="btn btn-success fileinput-button">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span>SELECT FILE</span>
                                        <input type="file" id="fileupload" name="files[]" accept=".pdf, .docx, application/pdf, .jpg" multiple data-validation="required">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <template v-if="replyAttachmentsUpload.uploadedFiles.length >= 1">
                            <div class="row col-12">
                                <template v-for="(item, index) in replyAttachmentsUpload.uploadedFiles">
                                    <div v-bind:class="getAttachClass(item.type)">
                                        <div class="m-widget4__item d-flex align-items-center pt-0">
                                            <div class="m-widget4__img m-widget4__img--icon">
                                                <img v-bind:src="getAttachExtension(item.type)" alt="" height="50" width="50">
                                            </div>
                                            <div class="m-widget2__desc">
                                                <span class="m-widget4__text">{{ item.name.length > 50 ? item.name.slice(0, 50) + '...' : item.name }}</span>
                                            </div>
                                            <div class="m-widget2__actions ml-auto">
                                                <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" v-on:click="fileDelete(index)">
                                                    <i class="m-nav__link-icon flaticon-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-12">
                                <strong><h5>NO ATTACHMENTS</h5></strong>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btnSave">Submit</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- <div class="m-content" id="edit_rfi">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 id="header" class="m-portlet__head-text">
                        Request for Information
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#manpowerTab" role="tab">
                            Manpower
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item" v-show="content.status != 'for approval'">
                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#candidatesTab" role="tab">
                            Candidates
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-8">
                    <form action="">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="project_name" class="form-control-label required">
                                        Project Name
                                    </label>
                                    <select type="text" id="project_name" name="project_name" class="form-control m-input" data-validation="required" autocomplete="off">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="project_name" class="form-control-label required">
                                        Project Location
                                    </label>
                                    <input type="text" id="project_location" name="project_location" class="form-control m-input" data-validation="required" autocomplete="off" readonly="">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="prepared_dt" class="form-control-label required">
                                        Date Prepared
                                    </label>
                                    <input type="text" id="prepared_dt" name="prepared_dt" class="form-control m-input" data-validation="required" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="reply_needed" class="form-control-label required">
                                        Reply Needed
                                    </label>
                                    <input type="text" id="reply_needed" name="reply_needed" class="form-control m-input" data-validation="required" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="send_to" class="form-control-label required">
                                        TO:
                                    </label>
                                    <input type="text" id="send_to" name="send_to" class="form-control m-input" autocomplete="off" data-validation="required email">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="cc_to" class="form-control-label required">CC:</label>
                                    <input type="text" id="cc_to" name="cc_to" class="form-control m-input" multiple="" autocomplete="off" data-validation="required" placeholder="email1@example.com, email2@example.com">
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="consultant" class="form-control-label required">
                                        ATTENTION: (Name of Consultant)
                                    </label>
                                    <input type="text" id="consultant" name="consultant" class="form-control m-input" autocomplete="off" data-validation="required">
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-control-label required">TYPE:</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="type_arch" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Architectural" id="type_arch" data-validation="required">
                                                Architectural
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type_mech" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Mechanical" id="type_mech">
                                                Mechanical
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type_fire" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Fire Protection" id="type_fire">
                                                Fire Protection
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="type_civil" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Civil / Structural" id="type_civil">
                                                Civil / Structural
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type_elec" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Electrical" id="type_elec">
                                                Electrical
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type_others" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Others" id="type_others">
                                                Others
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="type_interior" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Interior Design" id="type_interior">
                                                Interior Design
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type_plumbing" class="m-radio m-radio--solid form-control-label">
                                                <input type="radio" name="request_type" value="Plumbing" id="type_plumbing_radio">
                                                Plumbing
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" type="text" id="type_other_text" placeholder="Specify if Others">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="info_need" class="form-control-label required">INFORMATION NEEDED:</label>
                                    <textarea name="information_needed" class="form-control" id="info_need"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-control-label required" for="attachments_radio">ATTACHMENTS:</label>
                                    <div class="row" id="attachments_radio">
                                        <div class="col-6">
                                            <label for="att_plans" class="m-checkbox m-checkbox--solid form-control-label">
                                                <input type="checkbox" name="attachments[]" value="Plumbling" id="att_plans" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                                Plans/Drawings
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <label for="att_mats" class="m-checkbox m-checkbox--solid form-control-label">
                                                <input type="checkbox" name="attachments[]" value="Plumbling" id="att_mats" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                                Material Sample/s
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="att_spec" class="m-checkbox m-checkbox--solid form-control-label">
                                                <input type="checkbox" name="attachments[]" value="Plumbling" id="att_spec" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                                Specifications
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <label for="att_others" class="m-checkbox m-checkbox--solid form-control-label">
                                                <input type="checkbox" name="attachments[]" value="Plumbling" id="att_others" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                                Others:
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="att_test" class="m-checkbox m-checkbox--solid form-control-label">
                                                <input type="checkbox" name="attachments[]" value="Plumbling" id="att_test" data-validation="validate_checkbox_group" data-validation-qty="min1">
                                                Test Results
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <input class="form-control" type="text" id="att_others_text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-control-label required" for="remarks">REMARKS:</label>
                                    <textarea name="remarks" class="form-control" id="remarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-control-label required" for="reply_from">REPLY FROM:</label>
                                    <textarea name="reply" class="form-control" id="reply_from"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--md"></div>
                        <div class="row">
                    </form>
                </div>
            </div>
            <div class="col-4">
                <div class="m-portlet m-portlet--mobile">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                Other Information
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <div class="m-portlet__nav">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover">
                                        <a href="javascript:void(0);" class="m-portlet__nav-link m-dropdown__toggle dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand btnQuick_action" v-show="content.status != 'DISAPPROVED'">
                                            Actions
                                        </a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 40.5px;"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__body">
                                                    <div class="m-dropdown__content">
                                                        <ul class="m-nav">
                                                            <li class="m-nav__section m-nav__section--first">
                                                                <span class="m-nav__section-text">
                                                                    Quick Actions
                                                                </span>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="javascript:void(0);" class="m-nav__link btnApprove_action" @click="approveRFI()">
                                                                    <i class="m-nav__link-icon la la-thumbs-o-up"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Approve Request
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
                    </div>
                    <div class="m-portlet__body">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <table class="table table-striped m-table"> 
                                <thead>
                                    <tr>
                                        <th>Reference no:</th> 
                                        <th class="text-right" style="font-weight: bold;">MRF-GC&amp;C-2026-0002</th>
                                    </tr>
                                    <tr>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Status:</td>
                                        <td class="text-right" style="font-weight: bold;">ONGOING</td>
                                    </tr>
                                    <tr>
                                        <td>Prepared by:</td>
                                        <td class="text-right" style="font-weight: bold;">Juan Dela Cruz</td>
                                    </tr>
                                    <tr>
                                        <td>Created by:</td>
                                        <td class="text-right" style="font-weight: bold;">Jane Doe</td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->