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
                                <div class="col-3">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">Invited</small>
                                    <div class="m--font-boldest text-dark" v-text="participantsCount.invited"></div>
                                </div>
                                <div class="col-3">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">pending</small>
                                    <div class="m--font-boldest text-dark" v-text="participantsCount.pending"></div>
                                </div>
                                <div class="col-3">
                                    <small class="text-uppercase text-muted m--font-boldest d-block">Confirmed</small>
                                    <div class="m--font-boldest text-dark"  v-text="participantsCount.confirmed"></div>
                                </div>
                                <div class="col-3">
                                    <small class="text-uppercase text-muted m--font-boldest d-block" >Declined</small>
                                    <div class="m--font-boldest text-dark" v-text="participantsCount.declined"></div>
                                </div>
                            </div>
                            <div class="m-separator m-separator--dashed d-xl-12"></div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center text-uppercase">
                                    <h3 class="m-widget1__title m--font-boldest">Guest Speakers</h3>
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
                    <div class="m-portlet" id="m_portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="la la-user"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        Event Participants
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <button type="button" v-if="!eventAlreadyHappened" class="btn m-btn m-btn--sm btn-success m-btn--custom m-btn--icon m-btn--air m-btn--pill mb-2 btnNew" data-toggle="modal" data-target="#addNewParticipant">
                                        <i class="la la-user-plus"></i>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" id="addNewParticipant" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ADD NEW PARTICIPANT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id='new_event_form' onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
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
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <input name="first_name" type="text" class="form-control m-input" placeholder="First Name" v-model="participantData.firstname" data-validation="required" maxlength="50">
                                    </div>
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <input name="middle_name" type="text" class="form-control m-input" placeholder="Middle Name" v-model="participantData.middlename" maxlength="50">
                                    </div>
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <input name="last_name" type="text" class="form-control m-input" placeholder="Last Name" v-model="participantData.lastname" data-validation="required" maxlength="50">
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
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <input name="first_name" type="text" class="form-control m-input" placeholder="First Name" data-validation="required" v-model="participantDataSelected.firstname" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                                    </div>
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <input name="middle_name" type="text" class="form-control m-input" placeholder="Middle Name" v-model="participantDataSelected.middlename" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
                                    </div>
                                    <div class="col-sm-12 col-md-4 mb-3">
                                        <input name="last_name" type="text" class="form-control m-input" placeholder="Last Name" data-validation="required"  v-model="participantDataSelected.lastname" maxlength="50" :disabled="participantDataSelected.is_employee == 1">
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

    <div class="modal fade show" id="modalTempContent" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content" id="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Temp Title</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">test</div>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="attendanceSheet" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Attendance Sheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id='attendance_sheet_form' onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-control-label">Event</label>
                                <input name="title" type="text" class="form-control m-input" placeholder="Event" v-model="eventsData.event_title" disabled>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-control-label">Description</label>
                                <textarea name="description" class="form-control m-input" placeholder="Event description..."  v-model="eventsData.description" disabled style="resize:none"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-control-label required">Date</label>
                                <input id="attendanceDate" name="attendanceDate" type="text" class="form-control m-input" placeholder="Select Attendance Date" data-validation="required" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-control-label required">Start</label>
                                <input id="startTime" name="startTime" type="text" class="form-control m-input" placeholder="Start" data-validation="required" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-control-label required">End</label>
                                <input id="endTime" name="endTime" type="text" class="form-control m-input" placeholder="End" data-validation="required" readonly>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-control-label">Venue</label>
                                <input name="venue" type="text" class="form-control m-input" placeholder="Venue" v-model="eventsData.event_venue" disabled>
                            </div>
                            <div class="col-md-12">
                                <label class="form-control-label">Expected Attendees</label>
                                <input name="attendees" type="text" class="form-control m-input" placeholder="Venue" v-model="participantsCount.confirmed" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btnSave"><i class="la la-check mr-2"></i>GENERATE</button>
                        <button class="btn btn-danger text-white btnBack" data-dismiss="modal"><i class="la la-times mr-2"></i>CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>