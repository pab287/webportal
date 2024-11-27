<div class="m-content">
<div class="row">
	<div class="col-lg-12">
		<div class="m-portlet">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">
							Manage Shift Schedule - Calendar
						</h3>
					</div>
				</div>
				<div class="m-portlet__head-tools">
					<ul class="m-portlet__nav">
						<li class="m-portlet__nav-item">
							<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
								<button type="button" class="m-btn btn btn-secondary btnNew btnModalEvent"><i class="m-nav__link-icon flaticon-add"></i> Add Event</button>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="m-portlet__body">
				<!--begin::Section-->
				<div id='m_calendar'></div>
				<!--end::Section-->
			</div>
			<!--end::Form-->
		</div>
	</div>
</div>
<div class="modal" id="modalEvent" role="dialog" aria-labelledby="exampleModalLabel">
<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Add Event</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		</div>
		<div class="modal-body">
			<form id="frmEvent">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="form-group m-form__group row">
					<label for="title" class="col-3 col-form-label">Title</label>
					<div class="col-9">
						<input class="form-control m-input" type="text" name="title" id="title">
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label for="description" class="col-3 col-form-label">
						Description
					</label>
					<div class="col-9">
						<textarea class="form-control" name="description" id="description" rows="5" style="resize: none;"></textarea>
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label for="daterange_from" class="col-3 col-form-label">Date From</label>
					<div class="col-9">
						<input type="text" name="daterange_from" class="form-control" id="daterange_from" readonly="" placeholder="Select DateTime">
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label for="daterange_to" class="col-3 col-form-label">Date To</label>
					<div class="col-9">
						<input type="text" name="daterange_to" class="form-control" id="daterange_to" readonly="" placeholder="Select DateTime">
						<p style="margin-top: 5px; color: #ff0000;"><small><strong>Note:</strong> set the time to 00:00 for a whole day event.</small></p>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btnSave btnSubmitEvent">Save changes</button>
			<button class="btn btn-secondary" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
</div>

<div class="modal" id="modalEventClicked" role="dialog" >
<div class="modal-dialog" role="document">
	<div id="event_clicked" class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">
				<template v-if="prop.event_editable === true">Edit Event</template>
				<template v-else>View Event</template>
			</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		</div>
		<div class="modal-body">
			<template v-if="prop.event_editable === true">
				<form id="frmEventEdit">
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<div class="form-group m-form__group row">
						<label for="title" class="col-3 col-form-label">Title</label>
						<div class="col-9">
							<input class="form-control m-input" type="text" name="title" id="title" v-model="prop.title">
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="description" class="col-3 col-form-label">
							Description
						</label>
						<div class="col-9">
							<textarea class="form-control" name="description" id="description" rows="5" style="resize: none;" v-model="prop.description"></textarea>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="daterange_from" class="col-3 col-form-label">Date From</label>
						<div class="col-9">
							<input type="text" name="daterange_from" class="form-control custom-daterange" id="daterange_from_edit" readonly="" placeholder="Select DateTime" v-model="prop.date_started">
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="daterange_to" class="col-3 col-form-label">Date To</label>
						<div class="col-9">
							<input type="text" name="daterange_to" class="form-control custom-daterange" id="daterange_to_edit" readonly="" placeholder="Select DateTime" v-model="prop.date_ended">
							<p style="margin-top: 5px; color: #ff0000;"><small><strong>Note:</strong> set the time to 00:00 for a whole day event.</small></p>
						</div>
					</div>
				</form>
			</template>
			<template v-else>
				<div class="form-group m-form__group row">
					<label for="title" class="col-3 col-form-label">Title</label>
					<div class="col-9">
						<p class="form-control m-input custom-p" v-text="prop.title"></p>
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label for="description" class="col-3 col-form-label">
						Description
					</label>
					<div class="col-9">
						<p class="form-control m-input custom-p cp-textarea" v-text="prop.description"></p>
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label for="description" class="col-3 col-form-label">Date From</label>
					<div class="col-9">
						<p class="form-control m-input custom-p" v-text="prop.date_started"></p>
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label for="description" class="col-3 col-form-label">Date To</label>
					<div class="col-9">
						<p class="form-control m-input custom-p" v-text="prop.date_ended"></p>
					</div>
				</div>
			</template>
		</div>
		<div class="modal-footer">
			<template v-if="prop.event_editable === true">
			<button type="button" class="btn btn-primary btnSave btnUpdateEvent">Save changes</button>
			</template>
			<button class="btn btn-secondary" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
</div>

<script>
var _items = { event_editable: true };
var _eventVue = new Vue({
	el: "#event_clicked",
	data: { prop: _items }
});


$('#daterange_from').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	format: 'yyyy-mm-dd hh:ii'
});

$('#daterange_to').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	format: 'yyyy-mm-dd hh:ii'
});

$('.custom-daterange').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	format: 'yyyy-mm-dd hh:ii'
});

$(document).on("click", ".btnSubmitEvent", function(){
	var formData = $("form#frmEvent").serialize();
	$.ajax({
		url: "<?php echo site_url("gcctime/shift/set_calendar_schedules"); ?>",
		type: "post",
		dataType: "json",
		data: formData,
		success: function(json){
			if(json.response){
				toastr.success("Success", json.toastr_msg);
				$('#m_calendar').fullCalendar("refetchEvents");
			}else{
				toastr.success("Error", json.toastr_msg);
			}
		}
	});
});

$(document).on("click", ".btnModalEvent", function(){
	$("#modalEvent").modal("show");
});

var CalendarListView = function() {
    return {
        //main function to initiate the module
        init: function() {
            var todayDate = moment().startOf('day');
            var YM = todayDate.format('YYYY-MM');
            var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
            var TODAY = todayDate.format('YYYY-MM-DD');
            var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

            $('#m_calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaDay,listWeek'
                },
                defaultView: 'listWeek',
                eventLimit: true,
                navLinks: true,
                height: 900,
                events: { url: '<?php echo site_url("gcctime/shift/get_calendar_schedules"); ?>' },
                eventRender: function(event, element) {
                    if (element.hasClass('fc-day-grid-event')) {
                        element.data('content', event.description);
                        element.data('placement', 'top');
                        mApp.initPopover(element); 
                    } else if (element.hasClass('fc-time-grid-event')) {
                        element.find('.fc-title').append('<div class="fc-description">' + event.description + '</div>'); 
                    } else if (element.find('.fc-list-item-title').lenght !== 0) {
                        element.find('.fc-list-item-title').append('<div class="fc-description">' + event.description + '</div>'); 
                    }
                },
				eventClick: function(info){
					var eventId = info.id;
					_doEventClick(eventId);
				}
            });
        }
    };
}();

var _doEventClick = function(eventId){
	$.ajax({
		url: "<?php echo site_url('gcctime/shift/get_calendar_data'); ?>",
		type: "post",
		dataType: "json",
		data: { id: eventId },
		success: function(json){
			if(json.response){
				_eventVue.prop = json.data;
				$("#modalEventClicked").modal("show");
			}
		}
	});
}

jQuery(document).ready(function() {
    CalendarListView.init();
});
</script>
</div>