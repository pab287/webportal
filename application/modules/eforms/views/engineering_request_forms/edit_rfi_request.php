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

    .file-link {
        cursor: pointer;
    }

    .file-link:hover {
        color: #007bff; /* bootstrap blue */
        text-decoration: underline;
    }

    .file-viewer {
        height: 75vh;
        background: #111;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .file-frame {
        width: 100%;
        height: 100%;
        border: none;
    }

    .preview-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

</style>
<div class="m-content" id="edit_rfi_content">
    <div class="row">
        <div class="col-md-12 col-sm-12">
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
                            <li class="nav-item m-tabs__item" v-if="canEdit || reqNoted">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#replyForm" role="tab">
                                    <h5 class="m-portlet__head-text">
                                        REPLY
                                        <i v-show="changes.attachment || changes.reply" style="color: blue;"
                                        class="fa fa-exclamation-circle ml-2"
                                        data-toggle="tooltip"
                                        data-theme="dark"
                                        title="Changes have been made in this page. Do not forget to click SAVE!">
                                        </i>
                                    </h5>
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
                                                                <div class="m-widget4__item d-flex align-items-center pt-0">
                                                                    <div class="m-widget4__img m-widget4__img--icon">
                                                                        <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                                                    </div>
                                                                    <div class="m-widget2__desc file-link" @click="openFile(item)">
                                                                        <span class="m-widget4__text">{{ item.filename.length > 30 ? item.filename.slice(0, 30) + '...' : item.filename }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="col-12 text-center">
                                                        <strong><h4>NO ATTACHMENTS</h4></strong>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="replyForm" v-if="canEdit || reqNoted">
                                        <form  id="edit_reply_form" enctype="multipart/form-data">
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
                                                        <label for="reply_needed" class="form-control-label m--font-bold">
                                                            REPLY
                                                        </label>
                                                        <textarea name="reply" id="reply_needed" class="form-control" data-validation="required"></textarea>
                                                        <span id="reply_needed-error" class="help-block form-error" style="display: none;">This is a required field</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="m-separator m-separator--dashed m-separator--md"></div>
                                            <div class="row pb-2">
                                                <div class="col-12">
                                                    <label for="fileupload" class="form-control-label m--font-bold">
                                                        ATTACHMENTS
                                                    </label>
                                                    <div class="form-group mb-0" v-if="showUpdate">
                                                        <span class="btn btn-success fileinput-button">
                                                            <i class="glyphicon glyphicon-plus"></i>
                                                            <span>SELECT FILE</span>
                                                            <input type="file" id="fileupload" name="files[]" accept=".pdf, .docx, application/pdf, .jpg" multiple @change="onFileChange($event)">
                                                        </span>
                                                    </div>
                                                   
                                                </div>
                                                <div class="col-12" v-if="showUpdate">
                                                    <span id="fileupload-error" class="help-block form-error" style="display: none;">This is a required field</span>
                                                </div>
                                            </div>
                                            <template v-if="replyAttachments.length >= 1">
                                                <div class="row col-12">
                                                    <template v-for="(item, index) in replyAttachments">
                                                        <div v-bind:class="getClass(item.filename)">
                                                            <div class="m-widget4__item d-flex align-items-center pt-0">
                                                                <div class="m-widget4__img m-widget4__img--icon">
                                                                    <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                                                </div>
                                                                <div class="m-widget2__desc file-link" @click="openFile(item,true)" dis>
                                                                    <span class="m-widget4__text">{{ item.filename.length > 50 ? item.filename.slice(0, 50) + '...' : item.filename }}</span>
                                                                </div>
                                                                <div class="m-widget2__actions ml-auto" v-if="showUpdate">
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
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <strong><h5>NO ATTACHMENTS</h5></strong>
                                                    </div>
                                                </div>
                                            </template>
                                            <div class="m-separator m-separator--dashed m-separator--md"></div>
                                            <hr/>
                                            <div class="row">
                                                <div class="col-6">
                                                    <button class="btn btn-info btnEdit" type="submit" v-show="showUpdate" :disabled="!changes.attachment && !changes.reply">UPDATE</button>
                                                    <button class="btn btn-warning text-light btnEdit" type="button" @click="editReply" v-show="reply.status == 'pending' && canEdit && !showUpdate">EDIT</button>
                                                    
                                                </div>
                                                <div class="col-6 text-right">
                                                    <button class="btn btn-success btnApprove_action" type="button" @click="processReply('approved')" v-show="reply.status == 'for_approve' && reply.reply != ''">APPROVE</button>
                                                    <button class="btn btn-danger btnDisapprove_action" type="button" @click="processReply('pending')" v-show="reply.status == 'for_approve' && reply.reply != ''">DISAPPROVE</button>
                                                    <button class="btn btn-primary text-light btnApprove_action" type="button" @click="processReply('noted')"  v-show="reply.status == 'approved' && reply.reply != ''">NOTE</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </dvi>
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
                <div class="modal-body p-0 file-viewer">
                    <img v-if="isImage" :src="filePath" class="preview-img">
                    <iframe v-else :src="filePath" class="file-frame"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>