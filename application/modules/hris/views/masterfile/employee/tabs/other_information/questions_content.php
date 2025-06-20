<div id="questions-content">
    <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m--margin-bottom-15">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Employment Questions
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <button class="m-portlet__nav-link btn btn-sm btn-success m-btn btnSave btnUpdateQuestions">
                            <i class="fa fa-pencil"></i> Update
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <template v-for="(item, index) in vm_question" :key="index">
                <div class="form-group">
                    <label class="col-md-12"><h6 v-text="item.question"></h6></label>
                    <div class="col-md-12">
                        <p class="m-form-row__paragraph" v-text="hasAnswer(item.id)"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>