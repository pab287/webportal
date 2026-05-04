<div class="m-content" id="request-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <a type="button" href="masterfile" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                            <i class="la la-arrow-left"></i>
                        </a>
                    </span>
                    <h3 id="header" class="m-portlet__head-text">
                        New Request Forms
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="form-group m-form__group row col">
                        <div class="col-3 d-flex align-items-center">
                            <label class="m-radio m-radio--brand fs-3">
                                <input type="radio" name="eng_request_form" value="RFI" checked="">
                                Request for Information (RFI)
                                <span></span>
                            </label>
                        </div>
                        <!-- <div class="col-3 d-flex align-items-center">
                            <label class="m-radio m-radio--brand fs-3">
                                <input type="radio" name="eng_request_form" value="RFA">
                                Request For Approval (RFA)
                                <span></span>
                            </label>
                        </div> -->
                    </div>
                </div>
            </div>
            <div id="rfi-content">
                <form id="new_rfi_form" enctype="multipart/form-data">
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
                                <input type="text" id="project_location" name="project_location" class="form-control m-input"  autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="prepared_dt" class="form-control-label required">
                                    Requested By
                                </label>
                                <select type="text" id="requested_by" name="requested_by" class="form-control m-input" data-validation="required" autocomplete="off">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="reply_needed" class="form-control-label required">
                                    Reply Needed
                                </label>
                                <input type="text" id="reply_needed" name="reply_needed" class="form-control m-input" data-validation="required" autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="consultant" class="form-control-label required">
                                    ATTENTION
                                </label>
                                <input type="text" id="consultant" name="consultant" class="form-control m-input" placeholder="Name of Consultant" autocomplete="off" readonly>
                                <!-- <select type="text" id="consultant" name="consultant" class="form-control m-input" data-validation="required" autocomplete="off">
                                    <option></option>
                                </select> -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label required">TYPE</label>
                                <div class="row" v-for="(chunk, rowIndex) in chunkedTypes" :key="rowIndex">
                                    <div class="col-md-4" v-for="type in chunk" :key="type.id">
                                        <label :for="'type_' + type.id" class="m-radio m-radio--solid form-control-label">
                                            <input type="radio" name="request_type" :id="'type_' + type.id" :value="type.type_name" v-model="selectedType" data-validation="required" @change="handleTypeSelect(type)">
                                            {{ formatLabel(type.type_name) }}
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row" v-if="isOthersSelected">
                                    <div class="col-4">
                                        <input type="text" name="other_request_type" class="form-control" v-model="otherText" placeholder="Specify if Others" data-validation="required">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="information_needed" class="form-control-label required">
                                    INFORMATION NEEDED
                                </label>
                                <textarea name="information_needed" id="information_needed" class="form-control" data-validation="required"></textarea>
                               <span id="information_needed-error" class="help-block form-error" style="display: none;">This is a required field</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-separator m-separator--dashed m-separator--md"></div>
                    <div class="row pb-2">
                        <div class="col-12">
                            <label for="fileupload" class="form-control-label">
                                ATTACHMENTS
                            </label>
                            <div class="form-group">
                                <span class="btn btn-success fileinput-button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span>SELECT FILE</span>
                                    <input type="file" id="fileupload" accept=".pdf, .docx, application/pdf, .jpg" multiple>
                                </span>
                            </div>
                        </div>
                    </div>
                    <template v-if="attachments.uploadedFiles.length >= 1">
                        <div class="row col-12">
                            <template v-for="(item, index) in attachments.uploadedFiles">
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
                    </template>
                    <template v-else>
                        <div class="col-12">
                            <strong><h5>NO ATTACHMENTS</h5></strong>
                        </div>
                    </template>
                    <div class="m-separator m-separator--xl"></div>
                    <div class="row mt-3">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary btnSave">
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>