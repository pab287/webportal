<div class="m-content">
	<div class="row">
	<div class="col-xl-3 col-lg-4">
		<div class="m-portlet m-portlet--full-height  ">
			<div class="m-portlet__body">
				<div class="m-card-profile">
					<div class="m-card-profile__title m--hide">Your Profile</div>
					<div class="m-card-profile__pic">
						<div class="m-card-profile__pic-wrapper">	
							<img src="<?php echo (isset($userProfile["pic_filename"]) && $userProfile["pic_filename"])? $userProfile["pic_filename"]: base_url('uploads/papap.jpg'); ?>" alt="">
						</div>
					</div>
					<div class="m-card-profile__details">
						<span class="m-card-profile__name"><?php echo (isset($userProfile["display_name"]) && $userProfile["display_name"])? $userProfile["display_name"]: "No Assigned Name"; ?></span>
						<a href="javascript:void(0);" class="m-card-profile__email m-link color-black"><?php echo (isset($userProfile["email"]) && $userProfile["email"])? strtolower($userProfile["email"]):"noemail@gccph.com"; ?></a>
					</div>
				</div>	
				<ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
					<li class="m-nav__separator m-nav__separator--fit"></li>
					<li class="m-nav__section m--hide">
						<span class="m-nav__section-text">Section</span>
					</li>
					<li class="m-nav__item">
						<a href="javascript:void(0);" class="m-nav__link">
						<i class="m-nav__link-icon flaticon-share"></i>
							<span class="m-nav__link-text">Activity</span>
						</a>
					</li>
				</ul>
			</div>			
		</div>	
	</div>
	<div class="col-xl-9 col-lg-8">
		<div class="m-portlet m-portlet--full-height m-portlet--tabs">
			<div class="m-portlet__head">
				<div class="m-portlet__head-tools">
					<ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary" role="tablist">
						<li class="nav-item m-tabs__item">
							<a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_user_profile_tab_1" role="tab">
								<i class="flaticon-share m--hide"></i>
								Current Profile
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="tab-content">
				<div class="tab-pane active" id="m_user_profile_tab_1" role="tabpanel">
					<form class="m-form m-form--fit m-form--label-align-right">
						<div class="m-portlet__body">
							<div class="form-group m-form__group m--margin-top-10 m--hide">
								<div class="alert m-alert m-alert--default" role="alert">
									The example form below demonstrates common HTML form elements that receive updated styles from Bootstrap with additional classes.
								</div>
							</div>

							<div class="form-group m-form__group row">
								<div class="col-10 ml-auto">
									<h3 class="m-form__section">Personal Details</h3>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Full Name</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["display_name"]) && $userProfile["display_name"])? $userProfile["display_name"]: "No Assigned Name"; ?></span></div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Id Number</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["idno"]) && $userProfile["idno"])? $userProfile["idno"]: "No Id Number"; ?></span></div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Biometric Number</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["biometricno"]) && $userProfile["biometricno"])? $userProfile["biometricno"]: "No Biometric Id"; ?></span></div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Position</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["position"]) && $userProfile["position"])? $userProfile["position"]: "No Assigned Position"; ?></span></div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Status</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["employee_status"]) && $userProfile["employee_status"])? $userProfile["employee_status"]: "Inactive"; ?></span></div>
							</div>
							<div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
							<div class="form-group m-form__group row">
								<div class="col-10 ml-auto">
									<h3 class="m-form__section">Address</h3>
								</div>
							</div>

							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Address</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["curr_addr"]) && $userProfile["curr_addr"])? $userProfile["curr_addr"]: "No Address"; ?></span></div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Street Address</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["str_addr"]) && $userProfile["str_addr"])? $userProfile["str_addr"]: "No Street Address"; ?></span></div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">City</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["city"]) && $userProfile["city"])? $userProfile["city"]: "No City"; ?></span></div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-md-2 col-form-label">Postal Code</label>
								<div class="col-md-7"><span class="form-control m-input"><?php echo (isset($userProfile["postal"]) && $userProfile["postal"])? $userProfile["postal"]: "No Postal Code"; ?></span></div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
<style>
a.m-card-profile__email.m-link {
    color: #000000;
    font-weight: 500;
}
</style>