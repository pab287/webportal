<div id="EditFileModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form id="editNewFileForm" enctype="multipart/form-data">
        <div class="modal-header">
            <h5 class="modal-title">Add File</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <span class="btn btn-success fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Select file</span>
                <input type="file" id="edit_fileupload" name="editfiles" multiple>
            </span>
            <div id="editprogress" class="progress mt-2">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="edit_files" class="files"></div>
                <!-- edit file preview -->
                <div id="edit_preview" class="m-widget2 row mt-3">
                    <template v-if="count">
                        <div class="m-portlet m-portlet--rounded col-lg-12">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            Attachments
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="m-portlet__body row mb-3">
                                <template v-for="(item, index) in rows">
                                    <div v-bind:class="getClass(item.filename)">
                                        <div class="m-widget4__item m-0 p-0">
                                            <div class="m-widget4__img m-widget4__img--icon">
                                                <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                            </div>
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__text" v-text="item.filename">
                                                </span>
                                            </div>
                                            <div class="m-widget2__actions">
                                                <div class="m-widget2__actions-nav">
                                                    <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                                        <a href="#" class="m-dropdown__toggle">
                                                            <i class="la la-ellipsis-h"></i>
                                                        </a>
                                                        <div class="m-dropdown__wrapper">
                                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 14.7032px;"></span>
                                                            <div class="m-dropdown__inner">
                                                                <div class="m-dropdown__body">
                                                                    <div class="m-dropdown__content">
                                                                        <ul class="m-nav">
                                                                            <li class="m-nav__item">
                                                                                <a href="#" class="m-nav__link" v-on:click="fileDelete(item.id)">
                                                                                    <i class="m-nav__link-icon flaticon-circle"></i>
                                                                                    <span class="m-nav__link-text">
                                                                                        Remove
                                                                                    </span>
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- end edit file preview -->
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success btnSave">Save changes</button>
            <button id="editfileClose" type="button" class="btn btn-danger btnCancel">Close</button>
        </div>
        </form>
    </div>
  </div>
</div>