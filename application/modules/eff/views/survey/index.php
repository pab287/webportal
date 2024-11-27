<div class="m-content">
	<div class="row">
		<div class="col-md-3 col-lg-3">&nbsp;</div>
		<div class="col-md-6 col-lg-6">
			<div class="m-portlet m-portlet--mobile">
				<form id="form-survey" class="m-form m-form--fit m-form--label-align-right" action="<?php echo site_url("eff/survey/add_survey_data"); ?>">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="m-portlet__body">
					<div class="form-group m-form__group row">
						<div class="col-md-4 text-right">
							<img src="<?php echo base_url("assets/images/comp_logos/GCC_inc.png"); ?>" width="125px" />
						</div>
						<div class="col-md-8 text-center">
							<h1 style="margin-top: 20px;">EMPLOYEE FEEDBACK FORM</h1>
							<p>GOC - 613 Rev 0 06/06/19</p>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<div class="col-md-12">
							<p style="font-size: 18px; text-align: justify; padding: 0px 30px;">Thank you for taking this quick survey. We would like to know how you feel about the
							interaction you have experienced with any of our internal department so we can further 
							improve the way we serve you.</p>
						</div>
					</div>
					<div class="form-group m-form__group row" id="survey_details">
						<div class="col-md-12">
							<ol class="custom-list">
								<li>
									<div class="form-group m-form__group">
										<label for="department">What department would you like to give a feedback?</label>
										<select class="form-control m-input m-input--square col-md-6 select2" id="department" name="department" placeholder="" data-validation="required">
											<option selected disabled value="0">&nbsp;</option>
											<option v-for="item in post" v-bind:value="item.id">{{item.description}}</option>
										</select>
									</div>
								</li>
								<li>
									<div class="form-group m-form__group m--margin-top-10">
										<label for="purpose">What was the purpose of interaction?</label>
										<textarea class="form-control m-input m-input--square" name="purpose" id="purpose" placeholder="" rows="2" style="resize:none;" autocomplete=off data-validation="length" data-validation-length="min15"></textarea>
									</div>
								</li>
								<li>
									<div class="form-group m-form__group m--margin-top-10">
										<label for="purpose">Were you supported promptly?</label>
										<div class="m-radio-inline">
											<label class="m-radio">
												<input type="radio" name="supported" value="1">Yes<span></span>
											</label>
											<label class="m-radio">
												<input type="radio" name="supported" value="2">No<span></span>
											</label>
										</div>
									</div>
								</li>
								<li>
									<div class="form-group m-form__group m--margin-top-10">
										<label for="purpose">How satisfied are you with the assistance you have received?</label>
										<div class="rate_uss form-rater"></div>
										<input id="ratings" type="hidden" name="rating">
									</div>
								</li>
								<li>
									<div class="form-group m-form__group m--margin-top-10">
										<label for="purpose">We would love to know what's working and how we can do better.</label>
										<textarea class="form-control m-input m-input--square" name="remarks" id="remarks" placeholder="" rows="7" style="resize:none;" autocomplete=off></textarea>
									</div>
								</li>
							</ol>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary btn-lg btn-submit" type="submit"><i class="la la-check"></i> Submit</button>
						</div>
					</div>
				</div>
				</form>
			</div>
		</div>
		<div class="col-md-3 col-lg-3">&nbsp;</div>
	</div>
<script>
$.validate({
	form : '#form-survey',
	lang: 'en',
	onSuccess : function(form) {
		var _url = form[0].action;
		var _data = jQuery(form[0]).serialize();
		var _btnSubmit = $(form[0]).find(".btn-submit");
		
		$.ajax({
			url: _url,
			type: "POST",
			data: _data,
			beforeSend: function(){
				if(typeof _btnSubmit !== "undefined"){ _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
			},
			success: function(data){
				if(data.response){
					form[0].reset();
					$(".rate_uss").rate("setValue", 0);
					$(".select2").val("0").trigger("change");
					toastr.success(data.toastr_msg, "Added Survey", 5000);
					setTimeout(function(){ window.location.reload(); }, 500);
				}else{
					toastr.error(data.toastr_msg, "Error Survey", 5000);
				}
				if(typeof _btnSubmit !== "undefined"){ _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
			}
		});
		return false;
	}
});

var options = {
	max_value: 5,
	step_size: 1,
	initial_value: 0,
	update_input_field_name: $("#ratings"),
};

jQuery(document).ready(function(){
	_getDepartments();
	$(".rate_uss").rate(options);
	$(".rate_uss").on("change", function(ev, data){
		var _dataTo = data.to;
		var _color = "#353A36";
		
		switch(_dataTo){
			case 5: _color = "#FFA500"; break; 
			case 4: _color = "#22BF5A"; break;
			case 3: _color = "#8A8A35"; break; 
		}
		
		$(".rate-select-layer").css({ "color": _color });
	}).on("updateError", function(ev, jxhr, msg, err){
		console.log("This is a custom error event");
	}).on("updateSuccess", function(ev, data){
		console.log(data);
	});
});

var _getDepartments = function(){
	$.ajax({
		url: "<?php echo site_url('eff/department/get_department_items'); ?>",
		dataType: "json",
		success: function(json){
			if(json.response){
				vm.post = json.data;
				$(".select2").select2({
					width: "100%",
				});
			}
		}
	});
}
var _items = {};
var vm = new Vue({
	el: "#department",
	data: { post: _items },
});
</script>
</div>