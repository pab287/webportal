<style>
    .help-block.form-error{
        display: none;
    }

    .fc-time {
        display: none !important;
    }

    .fc-content{
        padding-top: 10px !important;
        padding-left: 10px !important;
    }

    .fc-event:hover {
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2) !important;
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }

    .fc-unthemed .fc-event.fc-start .fc-content:before{
        display: none;
    }

    @media (max-width: 768px) {
        .fc-event .m-widget4__item-title {
            font-size: 10px !important;
        }
        
        .fc-event .m-widget4__item-desc {
            font-size: 8px !important;
        }
    }
    .tab-pane {
        display: none;
    }
    .tab-pane.active {
        display: block;
    }
</style>
<div class="m-content">
    <div class="m-portlet" id="m_portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-calendar-2"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        Company Events
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" id="calendar_of_holidays_tab"
                    role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab"
                           href="#list-view-tab" role="tab">
                            TABULAR VIEW
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab"
                           href="#calender-view-tab" role="tab">
                            CALENDAR VIEW
                        </a>
                    </li>
                </ul>
                <!-- <button class="btn btn-primary btn-sm m-btn m-btn--icon m-btn--pill btnSave" type="button" id="calendar_of_holidays_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span><span>Options</span><span class="dropdown-toggle"></span></span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
                    <a class="dropdown-item" data-toggle="modal" href="#" onclick="clearCalender()">
                        TABULAR VIEW
                    </a>
                    <a class="dropdown-item" data-toggle="modal" href="#" onclick="CalendarBasic.init();">
                        CALENDAR VIEW
                    </a>
                </div> -->
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="list-view-tab" role="tabpanel">
                    <div class="row m--margin-top-20 m--margin-bottom-30">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <button type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill mb-2 btnNew" data-toggle="modal" data-target="#addNewEvent">
                                <i class="la la-plus"></i>
                                ADD EVENT
                            </button>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-2">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="la la-calendar"></i>
                                </span>
                                <input type="text" placeholder="Year" class="form-control m-input" id="filter-year"
                                       value="<?= date('Y') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span>
                                        <i class="la la-search"></i>
                                    </span>
                                </span>
                                <input type="text" class="form-control m-input m-input--solid"
                                       placeholder="Search..." id="search-holidays">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-calendar-of-holidays" style="width: 100%;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>TITLE</th>
                                <th>DESCRIPTION</th>
                                <th>VENUE</th>
                                <th>SPEAKERS</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="calender-view-tab" role="tabpanel">
                    <div id="m_calendar" class="fc fc-unthemed fc-ltr"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade show" id="addNewEvent" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Company Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id='new_event_form' onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                <div class="modal-body" id="event_calendar_body">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-light border rounded p-3 mb-3 position-relative">
                                <label for="" class="form-control-label">FILTERS</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="" class="form-control-label">Company</label>
                                        <select name="company_id[]" class="form-control m-input" id="company" multiple="multiple">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="form-control-label">Department</label>
                                        <select name="department_id[]" class="form-control m-input" id="department" multiple="multiple">
                                            <option></option>
                                        </select>
                                    </div>
                                    <small class="form-text mt-2 d-block col-12 text-danger">
                                        Keep these blank if the event is open to everyone.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="event_title" class="form-control-label required">Event Title</label>
                                <input name="event_title" type="text" class="form-control m-input" placeholder="event title" data-validation="required">
                            </div>
                            <div class="form-group">
                                <label for="event_description" class="form-control-label required">Event description</label>
                                <input name="event_description" type="text" class="form-control m-input" placeholder="event description" data-validation="required">
                            </div>
                            <div class="form-group">
                                <label for="date" class="form-control-label required">Event Schedule</label>
                                <input name="date" type="text" id="event_date" class="form-control m-input" placeholder="Select date" data-validation="required" readonly>
                            </div>
                            <div class="form-group">
                                <label for="event_venue" class="form-control-label required">Event Venue</label>
                                <input name="event_venue" type="text" class="form-control m-input" placeholder="event venue" data-validation="required">
                            </div>
                            <div class="form-group">
                                <label for="" class="form-control-label required">Event Speakers</label>
                                <template v-for="(speaker, index) in speakers" :key="index">
                                    <div class="border rounded p-3 mb-3 position-relative">
                                        <button type="button" class="close" :class="{ 'd-none': speakers.length === 1 }" @click="removeSpeaker(index)">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 mb-2">
                                                <label :for="`speakers[${index}][name]`" class="form-label required">Name</label>
                                                <input :name="`speakers[${index}][name]`" type="text" class="form-control" placeholder="speaker name" data-validation="required">
                                            </div>
                                            <div class="col-md-4 col-sm-12 mb-2">
                                                <label :for="`speakers[${index}][position]`" class="form-label required">Title/Position</label>
                                                <input :name="`speakers[${index}][position]`"  type="text" class="form-control" placeholder="speaker position" data-validation="required">
                                            </div>
                                            <div class="col-md-4 col-sm-12 mb-2">
                                                <label :for="`speakers[${index}][company]`" class="form-label">Company/Organization</label>
                                                <input :name="`speakers[${index}][company]`" type="text" class="form-control" placeholder="speaker company or organization">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-success mt-2 w-100 btnSave" @click="addNewSpeaker()">
                                    <i class="la la-plus"></i> Add Speaker
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>SAVE</button>
                    <button class="btn btn-danger text-white btnBack" data-dismiss="modal"><i class="la la-times mr-2"></i>CANCEL</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade show" id="edit-events-modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Company Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id='edit_event_form' onsubmit="return false;" onkeydown="return event.key !== 'Enter';">
                <div class="modal-body" id="event_calendar_body">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" :value="eventsData.id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="border rounded p-3 mb-3 position-relative">
                                <label for="" class="form-control-label">FILTERS</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="" class="form-control-label">Company</label>
                                        <select name="company_id[]" class="form-control m-input" id="company_edit" multiple="multiple" :disabled="disabled">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="form-control-label">Department</label>
                                        <select name="department_id[]" class="form-control m-input" id="department_edit" multiple="multiple" :disabled="disabled">
                                            <option></option>
                                        </select>
                                    </div>
                                    <small class="form-text mt-2 d-block col-12 text-danger">
                                        Keep these blank if the event is open to everyone.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="event_title" class="form-control-label required">Event Title</label>
                                <input name="event_title" type="text" class="form-control m-input" placeholder="event title" v-model="eventsData.event_title" data-validation="required" :disabled="disabled">
                            </div>
                            <div class="form-group">
                                <label for="event_description" class="form-control-label required">Event description</label>
                                <input name="event_description" type="text" class="form-control m-input" placeholder="event description" v-model="eventsData.description" data-validation="required" :disabled="disabled">
                            </div>
                            <div class="form-group">
                                <label for="date" class="form-control-label required">Event Schedule</label>
                                <input name="date" type="text" id="edit_event_date" class="form-control m-input" placeholder="Select date" :value="formatSchedule(eventsData.event_from,eventsData.event_to)" data-validation="required" :disabled="disabled">
                            </div>
                            <div class="form-group">
                                <label for="event_venue" class="form-control-label required">Event Venue</label>
                                <input name="event_venue" type="text" class="form-control m-input" placeholder="event venue" v-model="eventsData.event_venue" data-validation="required" :disabled="disabled">
                            </div>
                            <div class="form-group">
                                <label for="" class="form-control-label required">Event Speakers</label>
                                <template v-for="(item, index) in eventsData.speakers" :key="index">
                                    <div class="bg-light border rounded p-3 mb-3 position-relative">
                                        <button type="button" class="close" :class="{ 'd-none': eventsData.speakers.length === 1 }" @click="removeSpeaker(index)" v-if="!disabled">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <input type="hidden" :name="`speakers[${index}][id]`" v-model="item.id">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 mb-2 ">
                                                <label :for="`speakers[${index}][name]`" class="form-label required">Name</label>
                                                <input :name="`speakers[${index}][name]`" type="text" class="form-control" placeholder="speaker name" v-model="item.speaker_name" data-validation="required" :disabled="disabled">
                                            </div>
                                            <div class="col-md-4 col-sm-12 mb-2">
                                                <label :for="`speakers[${index}][position]`" class="form-label required">Title/Position</label>
                                                <input :name="`speakers[${index}][position]`"  type="text" class="form-control" placeholder="speaker position"  v-model="item.position" data-validation="required" :disabled="disabled">
                                            </div>
                                            <div class="col-md-4 col-sm-12 mb-2">
                                                <label :for="`speakers[${index}][company]`" class="form-label">Company/Organization</label>
                                                <input :name="`speakers[${index}][company]`" type="text" class="form-control" placeholder="speaker company or organization" v-model="item.company" :disabled="disabled">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-success mt-2 w-100 btnSave" v-if="!disabled" @click="addNewSpeaker()">
                                    <i class="la la-plus"></i> Add Speaker
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave" v-if="!disabled"><i class="la la-check mr-2"></i>SAVE</button>
                    <button id="btnEdit" type="button" class="btn btn-warning text-white btnEdit" @click="!disabled ? disabled = true : disabled = false" :hidden="!disabled"><i class="la la-clipboard mr-2" ></i>EDIT</button>
                    <button class="btn btn-danger text-white btnBack" data-dismiss="modal"><i class="la la-times mr-2"></i>CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>