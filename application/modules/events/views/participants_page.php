<style>
   .toggle-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #5BC236;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .toggle-label {
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .m-widget2__desc {
            line-height: 50px; /* Match the height of the parent */
        }

        .btn-xs {
            padding: 0.15rem 0.3rem;
            font-size: 0.65rem;
            line-height: 1;
            border-radius: 0.2rem;
        }
</style>
<div class="row" id="events-content">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="col-12">
        <div class="m-content">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="m-portlet" id="m_portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="la la-calendar"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        Event Details
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <a href="<?= base_url('events') ?>" class="custom-btn-link">
                                    <span class="m--font-bolder">Masterfile</span>
                                </a>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex flex-column text-center text-uppercase">
                                        <h3 class="m-widget1__title m--font-boldest" v-text="eventsData.event_title"></h3>
                                        <small class="text-muted" v-text="eventsData.description"></small>
                                        <span class="badge px-3 py-2 mt-2" :class="eventStatus.class"v-text="eventStatus.label"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-separator m-separator--dashed d-xl-12"></div>
                            <div class="row text-center bg-light rounded p-3 mx-1 mb-4 text-uppercase">
                                <div class="col-6">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">Venue</small>
                                    <div class="m--font-boldest text-dark" v-text="eventsData.event_venue"></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">Dates</small>
                                    <div class="m--font-boldest text-dark" v-text="formatDate(eventsData.event_from,eventsData.event_to)"></div>
                                </div>
                            </div>
                            <div class="m-separator m-separator--dashed d-xl-12"></div>
                            <div class="row text-center bg-light rounded p-3 mx-1 mb-4 text-uppercase">
                                <div class="col-4">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">Invited</small>
                                    <div class="m--font-boldest text-dark" v-text="participantsCount.invited"></div>
                                </div>
                                <!-- <div class="col-3">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">pending</small>
                                    <div class="m--font-boldest text-dark" v-text="participantsCount.pending"></div>
                                </div> -->
                                <div class="col-4">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">Confirmed</small>
                                    <div class="m--font-boldest text-dark"  v-text="participantsCount.confirmed"></div>
                                </div>
                                <div class="col-4">
                                    <small class="text-uppercase text-muted m--font-boldest d-block" >Declined</small>
                                    <div class="m--font-boldest text-dark" v-text="participantsCount.declined"></div>
                                </div>
                            </div>
                            <div class="m-separator m-separator--dashed d-xl-12"></div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center text-uppercase">
                                    <h3 class="m-widget1__title m--font-boldest">RESOURCE PERSON</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <template v-for="speaker in eventsData.speakers">
                                    <div class="col-12 mb-4 text-center">
                                        <div class="card bg-light border-0 text-uppercase">
                                        <div class="card-body p-3">
                                            <h6 class="card-title m--font-boldest mb-1 text-dark" v-text="speaker.speaker_name"></h6>
                                            <p class="card-text small text-muted mb-0">
                                            {{ speaker.position }} <span v-if="speaker.company">at {{ speaker.company }}</span>
                                            </p>
                                        </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-sm-12">
                    <div class="m-portlet m-portlet--tabs" id="m_portlet_2">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-tools">
                                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary" role="tablist">
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#participantTab" role="tab">
                                            Participants
                                        </a>
                                    </li>
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#scheduleTab" role="tab">
                                            Schedule
                                        </a>
                                    </li>
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#attachmentTab" role="tab">
                                            Attachments
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="participantTab">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                            <button type="button" v-if="!eventAlreadyHappened" class="btn m-btn m-btn--sm btn-success mb-2 btnNew" data-toggle="modal" data-target="#addNewParticipant">
                                                ADD PARTICIPANT
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table display table-bordered table-striped dataTable no-footer" id="participantsTable">
                                                    <thead>
                                                        <!-- <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr> -->
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="scheduleTab">
                                    <div class="m-portlet m-portlet--collapsed" data-portlet="true" id="m_portlet_schedule">
                                        <div class="m-portlet__head" style="height: 3rem;">
                                            <div class="m-portlet__head-caption">
                                                <div class="m-portlet__head-title">
                                                    <h3 class="m-portlet__head-text">
                                                        MANAGE SCHEDULE
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="m-portlet__head-tools">
                                                <ul class="m-portlet__nav">
                                                    <li class="m-portlet__nav-item">
                                                        <a href="javascript:void(0);"  data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                                            <i class="la la-angle-down"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="m-portlet__body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <form  id="new_event_sched" onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                                                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                        <div class="row">
                                                            <div class="col-2">
                                                                <button type="submit" class="btn m-btn m-btn--sm btn-success mb-2 btnNew">
                                                                    ADD SCHEDULE
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group m-form__group">
                                                                    <label class="form-control-label required" for="title">TITLE</label>
                                                                    <input type="text" id="title" name="title" class="form-control m-input" placeholder="Title" data-validation="required" >
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group m-form__group">
                                                                    <label class="form-control-label required" for="description">DESCRIPTION</label>
                                                                    <textarea type="text" id="description" name="description" class="form-control m-input" placeholder="Description" data-validation="required" ></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group m-form__group">
                                                                    <label class="form-control-label required" for="description">LOCATION</label>
                                                                    <textarea type="text" id="location" name="location" class="form-control m-input" placeholder="Location" data-validation="required" ></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="form-group m-form__group">
                                                                <label for="schedule_start">Inclusive Date</label>
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="la la-clock-o"></i></span>
                                                                        </div>
                                                                        <input id="schedule_date" name="event_date" type="text" class="form-control m-input" placeholder="Schedule" data-validation="required" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group m-form__group">
                                                                <label for="schedule_start">Start Time</label>
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="la la-clock-o"></i></span>
                                                                        </div>
                                                                        <input id="schedule_start" name="start" type="text" class="form-control m-input" data-validation="required" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group m-form__group">
                                                                    <label for="schedule_end">End Time</label>
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="la la-clock-o"></i></span>
                                                                        </div>
                                                                        <input id="schedule_end" name="end" type="text" class="form-control m-input" data-validation="required" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <template v-for="(items, date) in schedule" :key="date">
                                            <template v-for="(item, index) in items" :key="item.id">
                                                <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                                    <div class="card bg-light rounded p-3">
                                                        <div class="card-body pb-0">
                                                            <div class="row mb-3">
                                                                <div class="col-12">
                                                                    <h5 class="m--font-transform-u font-weight-bold text-center"  v-text="formatDateLocale(date)"></h4>
                                                                    <h4 class="m--font-transform-u font-weight-bold"  v-text="item.title"></h4>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 mb-3">
                                                                    <span class="badge badge-primary text-uppercase w-100" v-text="formatTime(item.start,item.end)" style="font-size: 12px;"></span>
                                                                </div>
                                                                <div class="col-12 mb-3">
                                                                    <span class="badge badge-info text-uppercase w-100" v-text="item.location"  style="font-size: 12px;"></span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-12">
                                                                    <div class="row">
                                                                        <span class="col-12 m--font-transform-u font-weight-bold">description: </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-12">
                                                                    <div class="row">
                                                                        <textarea class="form-control m-input col-12 text-uppercase" v-text="item.description" rows="3" style="resize: none; overflow-y: auto;" readonly></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row text-center">
                                                                    <span class="col-4 p-1">
                                                                        <button class="btn btn-info m-btn m-btn--icon m-btn--icon-only text-white w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Take Attendance" @click="takeAttendance(item)">
                                                                            <i class="la la-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                    <span class="col-4 p-1">
                                                                        <button class="btn btn-warning m-btn m-btn--icon m-btn--icon-only text-white w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Schedule" @click="editSchedule(item)">
                                                                            <i class="la la-pencil"></i>
                                                                        </button>
                                                                    </span>
                                                                    <span class="col-4 p-1">
                                                                        <button class="btn btn-danger m-btn m-btn--icon m-btn--icon-only text-white w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove Schedule" @click="deleteSchedule(item.id)">
                                                                            <i class="la la-trash"></i>
                                                                        </button>
                                                                    </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                                <div class="tab-pane" id="attachmentTab">
                                    <div class="row mb-3">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                            <button type="button" class="btn m-btn m-btn--sm btn-success mb-2 btnNew" data-toggle="modal" data-target="#newAttachment">
                                                ADD ATTACHMENT
                                            </button>
                                        </div>
                                    </div>
                                    <hr/>
                                    <template v-if="attachments.length <= 0">
                                        <div id="alert-no-attachment-yet">
                                            <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                                        </div>
                                    </template>
                                    <div class="accordion">
                                        <div class="m-widget2">
                                            <template v-for="(files, type, index) in attachments" :key="type">
                                                <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                                                    <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="collapse" :data-target="'#type-' + index + '-collapsibleDocuments'" aria-expanded="false" :aria-controls="'type-' + index + '-collapsibleDocuments'">
                                                        <i class="more-less fa fa-chevron-right"></i>
                                                    </button>
                                                    <h5 class="ml-3 mb-0" v-text="formatTypeLabel(type)"></h5>
                                                    <span class="pl-2 m-menu__link-badge">
                                                        <span class="m-badge m-badge--success" id="documents-total-badge">{{files.length}}</span>
                                                    </span>
                                                </div>
                                                <div :id="'type-' + index + '-collapsibleDocuments'" class="collapse">
                                                    <template v-if="files.length <= 0">
                                                        <div id="alert-no-document-yet">
                                                            <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <template v-for="(item, fileIndex) in files" :key="fileIndex">
                                                            <div v-bind:class="getClass(getAttachmentExtension(item.filename))">
                                                                <div class="m-widget4__item m-0 p-0">
                                                                    <div class="m-widget4__img m-widget4__img--icon">
                                                                        <img v-bind:src="getExtension(getAttachmentExtension(item.filename))" alt="" height="50" width="50">
                                                                    </div>
                                                                    <div class="m-widget2__desc">
                                                                        <span class="m-widget4__text" @click="openFile(item.filename,item.type)">{{item.filename}}</span>
                                                                    </div>
                                                                    <div class="m-widget2__actions">
                                                                        <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" v-on:click="removeAttachment(item.id,item.type,item.filename)">
                                                                            <i class="m-nav__link-icon flaticon-circle"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>
                                                <hr v-if="index < Object.keys(attachments).length - 1"/>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MODALS -->
    <div class="modal fade show" id="addNewParticipant" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ADD NEW PARTICIPANT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id='new_event_form' onsubmit="return false;" onkeydown="return event.key !== 'Enter';" enctype="multipart/form-data">
                    <div class="modal-body" id="event_calendar_body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="toggle-container">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="nonEmployeeToggle" name="is_employee" @click="toggleEmployeeFields()" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <label for="nonEmployeeToggle" class="toggle-label">EMPLOYEE</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group m-form__group">
                                    <label for="employee-select" class="form-control-label required">Employee</label>
                                    <select id="employee-select" name="employee-select" placeholder="Select an option" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-control-label required">Participant Name</label>
                                <div class="row">
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="first_name" type="text" class="form-control m-input" placeholder="First Name" v-model="participantData.firstname" data-validation="required" maxlength="50">
                                    </div>
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="middle_name" type="text" class="form-control m-input" placeholder="Middle Name" v-model="participantData.middlename" maxlength="50">
                                    </div>
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="last_name" type="text" class="form-control m-input" placeholder="Last Name" v-model="participantData.lastname" data-validation="required" maxlength="50">
                                    </div>
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="suffix" type="text" class="form-control m-input" placeholder="suffix" v-model="participantData.suffix" maxlength="50">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_phone" class="form-control-label required">Phone Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="la la-phone"></i></span>
                                    </div>
                                    <input id="new_phone" name="phone" type="text" class="form-control m-input" placeholder="e.g. 09XXXXXXXXX" v-model="participantData.mobile_no" data-validation="required" maxlength="13">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_email" class="form-control-label required">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="la la-envelope"></i></span>
                                    </div>
                                    <input id="new_email" name="email" type="email" class="form-control m-input" placeholder="example@domain.com" v-model="participantData.email" data-validation="required email" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="company" class="form-control-label required">Company</label>
                                <input name="company" type="text" class="form-control m-input" placeholder="Company" v-model="participantData.company" data-validation="required" maxlength="50">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="department" class="form-control-label required">Department</label>
                                <input for="department" name="department" type="text" class="form-control m-input" placeholder="Department" v-model="participantData.department" data-validation="required" maxlength="50">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="position" class="form-control-label required">Position</label>
                                <input name="position" type="text" class="form-control m-input" placeholder="Position" v-model="participantData.position" data-validation="required" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btnSave"><i class="la la-check mr-2"></i>SAVE</button>
                        <button class="btn btn-danger text-white btnBack" data-dismiss="modal"><i class="la la-times mr-2"></i>CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="editParticipant" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">EDIT PARTICIPANT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id='edit_participant_form' onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                    <div class="modal-body" id="event_calendar_body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="toggle-container">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="nonEmployeeToggleEdit" name="is_employee" :value="participantDataSelected.is_employee" :checked="participantDataSelected.is_employee == 1" disabled>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <label for="nonEmployeeToggle" class="toggle-label">EMPLOYEE</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-control-label required">Participant Name</label>
                                <div class="row">
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="first_name" type="text" class="form-control m-input" placeholder="First Name" data-validation="required" v-model="participantDataSelected.firstname" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                                    </div>
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="middle_name" type="text" class="form-control m-input" placeholder="Middle Name" v-model="participantDataSelected.middlename" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                                    </div>
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="last_name" type="text" class="form-control m-input" placeholder="Last Name" data-validation="required"  v-model="participantDataSelected.lastname" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                                    </div>
                                    <div class="col-sm-12 col-md-3 mb-3">
                                        <input name="suffix" type="text" class="form-control m-input" placeholder="Suffix" data-validation="required"  v-model="participantDataSelected.suffix" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-control-label required">Phone Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="la la-phone"></i></span>
                                    </div>
                                    <input id="phone" name="phone" type="text" class="form-control m-input" placeholder="e.g. 09XXXXXXXXX" data-validation="required"  v-model="participantDataSelected.mobile_no" maxlength="13">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-control-label required">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="la la-envelope"></i></span>
                                    </div>
                                    <input id="email" name="email" type="text" class="form-control m-input" placeholder="example@domain.com" data-validation="required email" v-model="participantDataSelected.email" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-control-label required">Company</label>
                                <input name="company" type="text" class="form-control m-input" placeholder="Company" data-validation="required" v-model="participantDataSelected.company" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-control-label required">Department</label>
                                <input name="department" type="text" class="form-control m-input" placeholder="Department" data-validation="required" v-model="participantDataSelected.department" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-control-label required">Position</label>
                                <input name="position" type="text" class="form-control m-input" placeholder="Position" data-validation="required" v-model="participantDataSelected.position" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info btnSave"><i class="la la-check mr-2"></i>UPDATE</button>
                        <button class="btn btn-danger text-white btnBack" data-dismiss="modal"><i class="la la-times mr-2"></i>CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="attendanceSheet" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <template v-for="(item, index) in participantSched">
                            <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                <div class="card bg-light rounded p-3">
                                    <div class="card-body pb-0">
                                        <div class="row mb-3">
                                            <div class="col-8">
                                                <h4 class="m--font-transform-u font-weight-bold"  v-text="item.title">
                                                </h4>
                                            </div>
                                            <div v-if="item.is_assigned == 1"  class="col-4 text-right">
                                                <span class="badge badge-success d-inline-flex align-items-center py-2">
                                                    Assigned
                                                </span>
                                            </div>
                                            <div v-else class="col-4 text-right">
                                                <span  class="badge badge-warning d-inline-flex align-items-center py-2">
                                                    Unassigned
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <span class="badge badge-primary text-uppercase w-100" v-text="formatTime(item.start,item.end)" style="font-size: 12px;"></span>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <span class="badge badge-info text-uppercase w-100" v-text="item.location"  style="font-size: 12px;"></span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="row">
                                                    <span class="col-12 m--font-transform-u font-weight-bold">description: </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="row">
                                                    <textarea class="form-control m-input col-12 text-uppercase" v-text="item.description" rows="3" style="resize: none; overflow-y: auto;" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div v-if="item.is_assigned == 0" class="col-12">
                                                <div class="row text-center">
                                                    <span class="col-12 text-right p-1">
                                                        <button @click="assignParticipant(item.schedule_id, item.participant_id)" class="btn btn-success m-btn text-white w-100" :disabled="loadingAssign[item.schedule_id]">
                                                            <span v-if="!loadingAssign[item.schedule_id]">ASSIGN</span>
                                                            <span v-else>
                                                                <i class="fa fa-spinner fa-spin"></i>
                                                            </span>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div v-else class="col-12">
                                                <div class="row text-center">
                                                    <span class="col-12 text-right p-1">
                                                        <button @click="unassignParticipant(item.schedule_id, item.participant_id)" class="btn btn-danger m-btn text-white w-100" :disabled="loadingUnassign[item.schedule_id]">
                                                            <span v-if="!loadingUnassign[item.schedule_id]" class="text-center">UNASSIGN</span>
                                                            <span v-else>
                                                                <i class="fa fa-spinner fa-spin"></i>
                                                            </span>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="newAttachment" tabindex="-1">
        <form id="New_Add_File" onsubmit="return false;" onkeydown="return event.key !== 'Enter';" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">UPLOAD ATTACHMENT</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="attachment_type" class="form-control-label required">Attachment Type</label>
                                <select id="attachment_type" name="attachment_type" class="form-control m-input" data-validation="required"></select>
                            </div>
                            <div class="form-group col-12">
                                <div class=" mt-4">
                                    <span class="btn btn-success fileinput-button">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span>Select file</span>
                                        <input type="file" id="fileupload" name="files[]" accept=".pdf, .docx, application/pdf, .jpg" data-validation="required" multiple="multiple">
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                            <div id="new_preview" class="m-widget2 row">
                                <template v-if="uploadedFiles.length >= 1">
                                    <div class="m-portlet m-portlet--rounded col-lg-12">
                                        <div class="m-portlet__body row" style="max-height: 300px; overflow-y: auto;">
                                            <template v-for="(item, index) in uploadedFiles">
                                                <div v-bind:class="getClass(item.type)">
                                                    <div class="m-widget4__item m-0 p-0">
                                                        <div class="m-widget4__img m-widget4__img--icon">
                                                            <img v-bind:src="getExtension(item.type)" alt="" height="50" width="50">
                                                        </div>
                                                        <div class="m-widget2__desc">
                                                        <span class="m-widget4__text">{{ item.name.length > 20 ? item.name.slice(0, 20) + '...' : item.name }}</span>
                                                        </div>
                                                        <div class="m-widget2__actions">
                                                            <button type="button" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" v-on:click="fileDelete(item.id)">
                                                                <i class="m-nav__link-icon flaticon-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="m-portlet m-portlet--rounded col-lg-12">
                                        <div class="m-portlet__body row">
                                            <strong><h5>NO ATTACHMENTS</h5></strong>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>UPLOAD</button>
                        <button class="btn btn-danger text-white btnBack" data-dismiss="modal"><i class="la la-times mr-2"></i>CANCEL</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="edit_schedule" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form  id="edit_event_sched" onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                    <div class="modal-body">
                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" id="id" name="id" v-model="editSched.id">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group m-form__group">
                                    <label class="form-control-label required" for="title">TITLE</label>
                                    <input type="text" id="title" name="title" class="form-control m-input" placeholder="Title" data-validation="required" v-model="editSched.title">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group m-form__group">
                                    <label class="form-control-label required" for="description">DESCRIPTION</label>
                                    <textarea type="text" id="description" name="description" class="form-control m-input" placeholder="Description" data-validation="required" v-model="editSched.description"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group m-form__group">
                                    <label class="form-control-label required" for="description">LOCATION</label>
                                    <textarea type="text" id="location" name="location" class="form-control m-input" placeholder="Location" data-validation="required" v-model="editSched.location"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group m-form__group">
                                        <label for="schedule_start">Inclusive Date</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="la la-clock-o"></i></span>
                                                </div>
                                                <input id="edit_schedule_date" name="event_date" type="text" class="form-control m-input" placeholder="Schedule" data-validation="required" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group m-form__group">
                                        <label for="schedule_start">Start Time</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="la la-clock-o"></i></span>
                                                </div>
                                                <input id="edit_schedule_start" name="start" type="text" class="form-control m-input" placeholder="Schedule" data-validation="required"  data-provide="timepicker" data-minuteStep="10" data-showMeridian="true" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group m-form__group">
                                            <label for="schedule_end">End Time</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="la la-clock-o"></i></span>
                                                </div>
                                                <input id="edit_schedule_end" name="end" type="text" class="form-control m-input" placeholder="Schedule" data-validation="required"  data-provide="timepicker" data-minuteStep="10" data-showMeridian="true" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btnSave"></i>UPDATE</button>
                        <button type="button" class="btn btn-warning text-white" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generate_attendance" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Attendance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card rounded-0 border-0">
                                <div class="row">
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <div class="mb-3 bg-light p-3 px-4 rounded">
                                            <span class="font-weight-bolder text-dark font-size-sm text-uppercase">Title: </span>
                                            <div class="text-dark font-weight-bold">{{ editSched.title }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <div class="mb-3 bg-light p-3 px-4 rounded">
                                            <span class="font-weight-bolder text-dark- font-size-sm text-uppercase">Date: </span>
                                            <div class="text-dark font-weight-bold">{{ formatDateLocale2(editSched.event_date) }} ● {{ formatTime(editSched.start,editSched.end) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <div class="mb-3 bg-light p-3 px-4 rounded">
                                            <span class="font-weight-bolder text-dark font-size-sm text-uppercase">Location: </span>
                                            <div class="text-dark font-weight-bold">{{ editSched.location }}</div>
                                        </div>
                                    </div>
                                    <div class="separator separator-dashed my-5"></div>
                                    <div class="col-12">
                                        <span class="font-weight-bolder text-dark font-size-sm text-uppercase">Description</span>
                                        <textarea class="form-control m-input col-12 text-uppercase" rows="3" style="resize: none; overflow-y: auto;" readonly>{{ editSched.description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="attendanceTable">
                                    <template v-if="attendance.length <= 0">
                                        <thead></thead>
                                    </template>
                                    <template v-else>
                                    <thead>
                                            <tr>
                                                <th hidden>ID</th>
                                                <th>#</th>
                                                <th>Participant</th>
                                                <th>Company</th>
                                                <th>Contact</th>
                                                <th>Attendance</th>
                                            </tr>
                                        </thead>
                                    </template>
                                    <template v-if="attendance.length <= 0">
                                        <tbody>
                                            <tr>
                                                <td colspan="6" class="m--font-boldest text-center">NO RECORDS FOUND</td>
                                            </tr>
                                        </tbody>
                                    </template>
                                    <template>
                                        <tbody id="attendanceBody">
                                            <tr v-for="(item, index) in attendance" :key="item.id">
                                                <td hidden>{{ item.id }}</td>
                                                <td>{{ index + 1 }}</td>
                                                <td>
                                                    <strong>{{ item.fullname }}</strong><br>
                                                    <small class="text-muted">{{ item.position }}</small>
                                                </td>
                                                <td>
                                                    {{ item.company }}<br>
                                                    <small class="text-muted">{{ item.department }}</small>
                                                </td>
                                                <td>
                                                    {{ item.email }}<br>
                                                    {{ item.mobile_no }}
                                                </td>
                                                <td class="text-center">
                                                    <label class="m-checkbox m-checkbox--bold m-checkbox--state-success">
                                                        <input type="checkbox" class="form-check-input h-10px w-10px" :value="item.is_present" @change="togglePresence(item.id,$event.target.checked ? 1 : 0)" true-value="1" false-value="0" :checked="item.is_present == 1"/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btnSave" @click="exportAttendance(attendance)">EXPORT</button>
                    <button type="button" class="btn btn-warning text-white" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pdfViewerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">File Viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" style="width: 100%; height: 800px;" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btnArchive" @click="removeCertificate(emp_attendance_selected.id)">Remove Certificate</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="modalTempContent" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content" id="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">DO NOT REMOVE THIS IS FOR UPLOADING</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">DO NOT REMOVE THIS IS FOR UPLOADING</div>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="attendanceCheck" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employee Attendance Record<h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="bg-light p-2 rounded mb-3 text-center">
                                <h5 class="mb-2 m--font-boldest">{{emp_attendance_selected.fullname}}</h6>
                                <span>{{emp_attendance_selected.position}}</span><br/>
                                <span>{{emp_attendance_selected.company}} | {{emp_attendance_selected.department}}</span>
                            </div>
                        </div>
                        <template v-for="(item, index) in employee_attendance" :key="index">
                            <template v-for="(sch, idx) in item.schedules" :key="idx">
                                <div class="col-4">
                                    <div class="bg-gray p-4 mb-3" :class="sch.is_present == 1 ? 'border-success' : 'border-danger'" style="border: 2px solid; border-radius: 10px;">
                                        <div class="m--font-boldest ">{{ sch.title }}</div>
                                        <small class="m--font-bolder">{{ formatDateLocale2(item.event_date) }} ● {{ formatTime(sch.start,sch.end) }}</small><br/>
                                        <small>{{ sch.location }}</small><br />
                                        <span v-if="sch.is_present == 1" class="badge bg-success">Present</span>
                                        <span v-else class="badge bg-danger">Absent</span>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btnSave" data-dismiss="modal" @click="uploadCertificate(emp_attendance_selected.id)"></i>AWARD CERTIFICATE</button>
                    <button class="btn btn-danger text-white btnBack" data-dismiss="modal"></i>CLOSE</button>
                </div>
            </div>
        </div>
    </div>

</div>
