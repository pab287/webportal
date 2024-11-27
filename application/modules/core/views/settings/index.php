<?php $actions = $this->core_layout->getCurrentActions(); ?>
<div class="m-content">
	<div class="row">
		<div class="col-lg-4">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">Settings</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<form id="frmSettings" method="POST" action="<?php echo site_url("core/settings/update_settings"); ?>">
				<div class="m-portlet__body" id="formSettingsContent">
					<div class="form-group m-form__group row">
						<label for="site_name" class="col-3 col-form-label">Site Name</label>
						<div class="col-9">
							<input class="form-control m-input" type="text" name="site_name" id="site_name" data-validation="required" v-model="items.site_name">
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="site_code" class="col-3 col-form-label">Site Code</label>
						<div class="col-9">
							<input class="form-control m-input" type="text" name="site_code" id="site_code" data-validation="required" v-model="items.site_code">
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="example-search-input" class="col-3 col-form-label">Site Description</label>
						<div class="col-9">
							<textarea id="site_description" name="site_description" class="form-control m-input" v-model="items.site_description"></textarea>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="example-search-input" class="col-3 col-form-label">Site Address</label>
						<div class="col-9">
							<textarea id="site_address" name="site_address" class="form-control m-input" rows="10" v-model="items.site_address"></textarea>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="example-search-input" class="col-3 col-form-label">Default Charged To</label>
						<div class="col-9">
							<input type="text" id="charged_to" name="charged_to" class="form-control m-input" v-model="items.charged_to" />
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label for="example-search-input" class="col-3 col-form-label">Remote Site Url</label>
						<div class="col-9">
							<input type="text" id="site_url" name="site_url" class="form-control m-input" v-model="items.site_url" />
						</div>
					</div>
				</div>
				<div class="m-portlet__foot">
					<div class="m-form__actions text-right">
						<button type="submit" class="btn btn-primary btnSave btn-submit">Save Changes</button>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var _items = { site_name: "", site_description: "", site_code: "", site_address: "", charged_to: "", site_url: "" };

<?php if(isset($items) && $items): ?>
_items = <?php echo json_encode($items); ?>;
<?php endif; ?>

$.validate({
	form : '#frmSettings',
	lang: 'en',
	onSuccess : function(form) {
		var _url = form[0].action;
		var _data = form.serialize();
		
		$.ajax({
			url: form[0].action,
			type: "POST",
			data: _data,
			beforeSend: function(){
				$(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
			},
			success: function(json){
				if(json.response){
					toastr.success(json.toastr_msg, "Settings", 5000);
				}else{
					toastr.error(json.toastr_msg, "Settings", 5000);
				}
				$(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
			}
		});
		
		return false;
	}
});

var vm = new Vue({
	el: "#formSettingsContent",
	data: { items: _items }
});
</script>