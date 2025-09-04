<div class="row" id="events-content">
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
                        </div>
                        <div class="m-portlet__body">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center text-uppercase">
                                        <h3 class="m-widget1__title m--font-boldest" v-text="eventsData.event_title"></h3>
                                        <small class="text-muted" v-text="eventsData.description"></small>
                                        <span class="badge px-3 py-2 mt-2" :class="eventStatus.class"v-text="eventStatus.label"></span>
                                    </div>
                                </div>
                            </div>
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
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center text-uppercase">
                                    <h3 class="m-widget1__title">Guest Speakers</h3>
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
                                    <button type="button" class="btn m-btn m-btn--sm btn-success m-btn--custom m-btn--icon m-btn--air m-btn--pill mb-2 btnNew" data-toggle="modal" data-target="#addNewParticipant">
                                        <i class="la la-user-plus"></i>
                                        ADD PARTICIPANT
                                    </button>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table display table-bordered table-striped dataTable no-footer" id="participantsTable">
                                            <thead class="w-100">
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
                                            <tbody class="w-100">
                                                <!-- <template v-for="participant in eventsData.participants">
                                                    <tr>
                                                        <td v-text="participant.participant_name"></td>
                                                        <td v-text="participant.position"></td>
                                                        <td v-text="participant.company"></td>
                                                    </tr>
                                                </template> -->
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
</div>

<div class="modal fade show" id="addNewParticipant" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ADD NEW PARTICIPANT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id='new_event_form' onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                <div class="modal-body" id="event_calendar_body">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="first_name" class="form-control-label required">First Name</label>
                                <input name="first_name" type="text" class="form-control m-input" placeholder="first name" data-validation="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="middle_name" class="form-control-label required">Middle Name</label>
                                <input name="middle_name" type="text" class="form-control m-input" placeholder="middle name" data-validation="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="last_name" class="form-control-label required">Last Name</label>
                                <input name="last_name" type="text" class="form-control m-input" placeholder="last name" data-validation="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone" class="form-control-label required">Phone Number</label>
                                <input name="phone" type="text" class="form-control m-input" placeholder="Phone number" data-validation="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email" class="form-control-label required">Email</label>
                                <input name="email" type="text" class="form-control m-input" placeholder="email" data-validation="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone" class="form-control-label required">Phone Number</label>
                                <input name="phone" type="text" class="form-control m-input" placeholder="Phone number" data-validation="required">
                            </div>
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

