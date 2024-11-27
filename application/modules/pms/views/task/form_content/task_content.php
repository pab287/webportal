<div id="m_portlet_preview" class="m-portlet m-portlet--head-sm m-portlet--head-solid-bg m-portlet--bordered m-portlet--rounded fadeIn animated" data-portlet="true">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    <span v-text="row.label">&nbsp;</span>
                    <small>Current Task</small>
                </h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item">
                    <a href="javascript:void(0);" data-portlet-tool="remove" class="m-portlet__nav-link m-portlet__nav-link--icon" title="Close Task Preview" data-original-title="Close Task Preview">
                        <i class="la la-close"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-8">
                <div class="m-portlet m-portlet--info m-portlet--head-sm m-portlet--head-solid-bg m-portlet--rounded m--margin-bottom-15">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-file"></i>
                                </span>
                                <h3 class="m-portlet__head-text">Contract Information <small>Current</small></h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <!-- ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item" v-if="task_duration === true && row.task_count > 0">
                                    <a href="javascript:void(0);" class="m-portlet__nav-link btn btn-info m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew" @click="setNewContract(item_id, unit_id)">
                                        <i class="la la-plus"></i>
                                    </a>
                                </li>
                            </ul -->
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="form-group row m--margin-bottom-0">
                            <div class="col-lg-6">
                                <label>Contract #</label>
                                <p class="form-control m--marginless" v-text="row.wo_code">&nbsp;</p>
                            </div>
                            <div class="col-lg-3">
                                <label class="">Date Issued</label>
                                <p class="form-control" v-text="row.issued_date">&nbsp;</p>
                            </div>
                            <div class="col-lg-3">
                                <label class="">Date Due</label>
                                <p class="form-control" v-text="row.due_date">&nbsp;</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <label class="">Contractor</label>
                                <p class="form-control m--marginless" v-text="row.contractor">&nbsp;</p>
                            </div>
                        </div>
                    </div>
                </div>
                <template v-if="contractor_count > 0">
                <div class="m-portlet m-portlet--danger m-portlet--head-sm m-portlet--head-solid-bg m-portlet--rounded m--margin-bottom-15">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-calendar"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    SUB-TASK
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <!-- ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);" class="m-portlet__nav-link btn btn-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew" @click="setNewTask(item_id, unit_id)">
                                        <i class="la la-plus"></i>
                                    </a>
                                </li>
                            </ul -->
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <?php echo $this->load->view("pms/task/form_content/subtask_content", null, true); ?>
                    </div>
                </div>
                </template>
            </div>
            <div class="col-md-4">
                <template v-if="contractor_count > 0">
                    <div class="m-portlet m-portlet--info m-portlet--head-sm m-portlet--head-solid-bg m-portlet--rounded m--margin-bottom-15">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-user"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        Contractor <small>History</small>
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-scrollable" data-scrollable="true" data-max-height="300" style="max-height: 300px; min-height: 100px;">
                                <?php echo $this->load->view("pms/task/form_content/task_contractor", null, true); ?>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <!--div class="col-md-4">
                <div class="m-portlet m-portlet--head-sm m-portlet--head-solid-bg m-portlet--rounded m--margin-bottom-15" :class="is_pastdue === true? 'm-portlet--danger bounce animated': 'm-portlet--primary'">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-time-1"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    Task Duration <small v-if="is_pastdue === true">PAST DUE</small>
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <span class="m-badge m-badge--wide m--bg-warning m--font-light bounceIn animated" v-if="is_pastdue === true">PAST DUE</span>
                            <ul class="m-portlet__nav" v-if="contractor_count == 0">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);" class="m-portlet__nav-link btn btn-primary m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew" @click="setTaskDuration(item_id, unit_id)">
                                        <i class="la la-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget12">
                            <div class="m-widget12__item">
                                <span class="m-widget12__text1">
                                    <p class="m--marginless">Issued Date</p>
                                    <template v-if="row.cdate1">
                                        <span v-text="row.cdate1"></span>
                                    </template>
                                    <template v-else>
                                        <span>0000-00-00</span>
                                    </template>
                                </span>
                                <span class="m-widget12__text2">
                                    <p class="m--marginless">Due Date</p>
                                    <template v-if="row.cdate2">
                                        <span v-text="row.cdate2"></span>
                                    </template>
                                    <template v-else>
                                        <span>0000-00-00</span>
                                    </template>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div -->
        </div>
        <template v-if="contractor_count > 0">
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet m-portlet--primary m-portlet--head-sm m-portlet--head-solid-bg m-portlet--rounded m--margin-bottom-0">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-list"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    TASK STATUS
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools"></div>
                    </div>
                    <div class="m-portlet__body">
                    <form id="frmTaskStatusUpdate">
                        <div class="form-group">
                            <label>Current Status</label>
                            <div class="m-checkbox-inline">
                                <label class="m-checkbox">
                                    <input type="radio" name="task_status" value="0" v-model="row.status" @change="updateTaskStatus"> Awaiting
                                    <span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input type="radio" name="task_status" value="1" v-model="row.status" @change="updateTaskStatus"> In Progress
                                    <span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input type="radio" name="task_status" value="2" v-model="row.status" @change="updateTaskStatus"> Deferred
                                    <span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input type="radio" name="task_status" value="4" v-model="row.status" @change="updateTaskStatus"> Terminated
                                    <span></span>
                                </label>
                                <label class="m-checkbox">
                                    <input type="radio" name="task_status" value="3" v-model="row.status" @change="updateTaskStatus"> Completed
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea">Remarks</label>
                            <textarea class="form-control" id="exampleTextarea" rows="7" v-model="current_remarks"></textarea>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
        </template>
    </div>
</div>