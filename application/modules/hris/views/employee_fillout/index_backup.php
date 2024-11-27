<div class="m-content mt-5">
	<div class="row">
		<div class="col-lg-8 offset-md-2">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                <i class='fa fa-user-plus'></i>
								New Employee Form
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<div class="m-portlet__body">
                <h5 class="m--hide text-center" id="not_found">Personnel request not found!</h5>
                <form id="user_data" class="" action="<?= base_url("hris/employee_fillout/save_data") ?>">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="personnel_request_id" id="personnel_request_id">
                    <div class="row m--margin-bottom-10">
                      <div class="col-sm-6 col-md-6 col-lg-4 col-xl-4">
                        <small class='text-muted'>COMPANY</small>
                        <label class="col-form-label pt-0"><span class='m--font-bold' id="company_label" style='text-transform: uppercase;'></span></label>
                      </div>
                      <div class="col-sm-6 col-md-6 col-lg-4 col-xl-4">
                        <small class='text-muted'>DEPARTMENT</small>
                        <label class="col-form-label pt-0"><span class='m--font-bold' id="department_label" style='text-transform: uppercase;'></span></label>
                      </div>
                      <div class="col-sm-6 col-md-6 col-lg-4 col-xl-4">
                        <small class='text-muted'>POSITION</small><br>
                        <label class="col-form-label pt-0"><span class='m--font-bold' id="position_label" style='text-transform: uppercase;'></span></label>
                      </div>
                    </div>
                    <hr>
                    <div class="row m--margin-bottom-10">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                            <label for="firstname" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">First Name *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="firstname" type="text" name="firstname" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="middlename" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Middle Name</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="middlename" type="text" name="middlename" class="form-control m-input" placeholder="( Optional )" maxlength="25" size="25" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="lastname" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Last Name *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <div class="position-relative search-with-dropdown-container">
                                        <input class="form-control m-input" id="lastname" name="lastname" maxlength="25" size="25" autocomplete="off" data-validation="required"/>
                                        <span class="m-form__help m--hide" id="emp_exist_notif" style="color: red; font-weight: 1000;">* This person already exists in the system</span>
                                        <div class="position-absolute options-container invisible search-with-dropdown-suggestion-list" style='z-index: 1;'>
                                            <ul class="employee-suggestion">
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- <input id="lastname" type="text" name="lastname" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" /> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="suffix" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Suffix</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="suffix" type="text" name="suffix" class="form-control m-input" placeholder="( Optional )" maxlength="10" size="10" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row m--margin-bottom-10">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="tel_no" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Telephone No</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7" style="z-index: 0;">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="la la-chain"></i>
                                        </span>
                                        <input id="tel_no" type="text" name="tel_no" class="form-control m-input" placeholder="( Optional )" maxlength="11" size="11" autocomplete="off"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="mobile_no" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Mobile No</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="la la-chain"></i>
                                        </span>
                                        <input id="mobile_no" type="text" name="mobile_no" class="form-control m-input" placeholder="( Optional )" maxlength="11" size="11" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="email" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Email Address</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="email" type="email" name="email" class="form-control m-input" placeholder="( Optional )" maxlength="100" size="100" autocomplete="off" />
                                    <span class="m-form__help">We'll never share your email with anyone else</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                            <div class="form-group m-form__group row">
                                <label for="curr_addr" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Current Address *</label>
                                <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                                    <input id="curr_addr" type="text" name="curr_addr" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                            <div class="form-group m-form__group row">
                                <label for="prov_addr" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Permanent Address *</label>
                                <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                                    <input id="prov_addr" type="text" name="prov_addr" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                            <div class="form-group m-form__group row">
                                <label for="long_lat_coordinates" class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-form-label">Map Coordinates</label>
                                <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9 col-xl-7">
                                    <input id="long_lat_coordinates" type="text" name="long_lat_coordinates" class="form-control m-input" maxlength="100" size="100" autocomplete="off" />
                                    <div class="m-form__help">latitude and longitude map coordinates</div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="gender" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Gender *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input id="gender" type="radio" name="gender" data-validation="required" value="Male" />
                                            Male<span></span>
                                        </label>
                                        <label class="m-checkbox">
                                            <input id="gender" type="radio" name="gender" data-validation="required" value="Female" />
                                            Female<span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="civil_stat" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Civil Status *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <select id="civil_stat" class="form-control select2" name="civil_stat" data-validation="required">
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Separated">Separated</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Annulled">Annulled</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="citizenship" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Citizenship *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="citizenship" type="text" name="citizenship" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="religion" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Religion *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                <input id="religion" type="text" name="religion" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="bday" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Birth Date *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="la la-calendar"></i>
                                        </span>
                                        <input id="bday" type="text" id="m_datepicker-birthdate" name="bday" class="form-control m-input" maxlength="12" size="12" autocomplete="off" data-validation="required" />
                                    </div>
                                    <span class="m-form__help pull-right">Date Format: YYYY-MM-DD</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row m--margin-bottom-10">
                        <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                            <div class="form-group m-form__group row">
                                <label for="birthplace" class="col-md-3 col-xs-6 col-sm-6 col-lg-3 col-xl-3 col-form-label">Place of Birth *</label>
                                <div class="col-md-9 col-xs-6 col-sm-6 col-lg-9 col-xl-7">
                                <input id="birthplace" type="text" name="birthplace" class="form-control m-input" maxlength="50" size="50" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-sm-10 col-md-12 col-lg-12 col-xl-12">
                            <div class="form-group m-form__group row">
                                <label for="languages" class="col-md-3 col-xs-6 col-sm-6 col-lg-3 col-xl-3 col-form-label">Languages *</label>
                                <div class="col-md-9 col-xs-6 col-sm-6 col-lg-9 col-xl-7">
                                <input id="languages" type="text" name="languages" class="form-control m-input" maxlength="200" size="200" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="height" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Height *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="height" type="text" name="height" class="form-control m-input" maxlength="10" size="10" autocomplete="off" data-validation="required" />
                                    <span class="m-form__help pull-right">Format: FEET INCHES</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="weight" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Weight *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="weight" type="text" name="weight" class="form-control m-input" maxlength="10" size="10" autocomplete="off" data-validation="required" />
                                    <span class="m-form__help pull-right">Format: Kilograms</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-10">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="complexion" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Complexion *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="complexion" type="text" name="complexion" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="hair_color" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Hair Color *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="hair_color" type="text" name="hair_color" class="form-control m-input" maxlength="25" size="25" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                            <div class="form-group m-form__group row">
                                <label for="bloodtype" class="col-sm-6 col-md-6 col-lg-5 col-xl-5 col-form-label">Blood Type *</label>
                                <div class="col-sm-6 col-md-6 col-lg-7 col-xl-7">
                                    <input id="bloodtype" type="text" name="bloodtype" class="form-control m-input" maxlength="30" size="30" autocomplete="off" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                <hr>
                <div class="text-right">
                    <button type='submit' onclick="saveData()" class='btn btn-success'><i class='fa fa-check'></i> SAVE</button>
                    <button type='button' class='btn btn-danger'><i class='fa fa-times'></i> CANCEL</button>
                </div>
                </form>
				</div>
			</div>
			<!--end::Portlet-->
		</div>
	</div>
</div>

<script>
var getUrlParameter = function getUrlParameter(sParam) {
var sPageURL =  (window.location.search.substring(1)),
    sURLVariables = sPageURL.split("/"),
    sParameterName,
    i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split("=");
    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : sParameterName[1];
    }
  }
};

$("#bday").datepicker({
    todayHighlight: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
    format: "yyyy-mm-dd",
    autoclose: true
});

param_id = getUrlParameter("id");
if(param_id == null){
    $("#user_data").addClass('m--hide');
    $("#not_found").removeClass("m--hide");
}
$.ajax({
    url: '<?php echo base_url("hris/employee_fillout/get_personnel_request_data"); ?>',
    type: 'POST',
    dataType: 'JSON',
    data: {
      csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
      id : param_id
    },
    success: function (response) {
        if(response[0] == null){
            $("#user_data").addClass('m--hide');
            $("#not_found").removeClass("m--hide");
        }else{
            $("#position_label").html(response[0].position);
            $("#department_label").html(response[0].department);
            $("#company_label").html(response[0].company);
            $("#personnel_request_id").val(response[0].id);
            $("#not_found").addClass("m--hide");
        }
    },
    error: function(response){
    }
});

// $("#civil_stat").select2({
//     placeholder: "Select an option",
// });

function saveData(){
    $.validate({
            form: "#user_data",
            lang: "en",
            onSuccess: function (form) {
                var currentForm = form[0];
                var formUrl = currentForm.action;
                var formData = $(currentForm).serialize();

                $.ajax({
                    url: formUrl,
                    type: "post",
                    dataType: "json",
                    data: formData,
                    beforeSend: function () {
                        $(currentForm)
                            .find(".btn-submit")
                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                            .prop("disabled", true);
                    },
                    success: function (json) {
                        console.log(json);
                        if(json.alert == 'success'){
                            toastr.success(
                                'Success',
                                "Employee added!",
                                5000
                            );
                        }else{
                            toastr.warning(
                                'Failed',
                                "Position already full!",
                                5000
                            );
                        }
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000)
                    }
                });
                return false;
            }
        });
}
</script>