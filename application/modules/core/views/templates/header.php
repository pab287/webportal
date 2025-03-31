<!DOCTYPE html>
<?php $session = $this->session;
$logged_in = $session->userdata("logged_in");
if(!$logged_in){
	$this->session->sess_destroy();
	$redirect = base_url(); header("location: {$redirect}");
}
$temp_template_css = false;
if(isset($has_template_css) && $has_template_css) $temp_template_css = true;
$temp_template_js = false;
if(isset($has_template_js) && $has_template_js) $temp_template_js = true;
?>
<html lang="en" >
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title><?php echo ($this->core_layout->getPageTitle())? "GC&amp;C Portal | ".$this->core_layout->getPageTitle(): "GC&amp;C Portal | Dashboard"; ?></title>
		<meta name="description" content="Latest updates and statistic charts">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url("assets/favicon.ico"); ?>">
		<script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>
		
		<!--begin::Base Scripts -->
		<script src="<?php echo base_url("assets/vendors/base/vendors.bundle.js"); ?>" type="text/javascript"></script>
		<?php if($temp_template_js == false): ?>
		<script src="<?php echo base_url("assets/demo/demo3/base/scripts.bundle.js"); ?>" type="text/javascript"></script>
		<?php endif; ?>
		<!--end::Base Scripts -->  
		
		<!-- to be remove -- script src="<?php echo base_url('assets/plugins/bootstrap/bootstrap.min.js'); ?>"></script -->
		<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/dataTables.rowGroup.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/dataTables.bootstrap4.min.js'); ?>"></script>
		
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
		<script src="<?php echo base_url('assets/js/vue.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/currency.js'); ?>"></script>
		
		<!-- Summernote JS Text Editor -->
  		<script type="text/javascript" language="javascript" src="<?php echo base_url('assets/js/summernote.min.js'); ?>"></script>
		
		<!-- end::Summernote JS -->
		<?php echo $this->core_layout->getStoredJs(); ?>
		
		<script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>

		<!-- dt btn export to be remove -->
		<script type="text/javascript" language="javascript" src="<?php echo base_url('assets/js/external/dataTables.buttons.min.js'); ?>"></script>
		<script type="text/javascript" language="javascript" src="<?php echo base_url('assets/js/external/jszip.min.js'); ?>"></script>
		<script type="text/javascript" language="javascript" src="<?php echo base_url('assets/js/external/buttons.html5.min.js'); ?>"></script>
		<script type="text/javascript" language="javascript" src="<?php echo base_url('assets/js/external/pdfmake.min.js'); ?>"></script>
		<script type="text/javascript" language="javascript" src="<?php echo base_url('assets/js/external/vfs_fonts.js'); ?>"></script>
		<!-- <link rel="stylesheet" href="https://openlayers.org/en/v3.20.1/css/ol.css" type="text/css">
		<script src="https://openlayers.org/en/v3.20.1/build/ol.js"></script> -->

		<!-- <script type="text/javascript" language="javascript" src="https://cdn.ckeditor.com/ckeditor5/12.3.1/classic/ckeditor.js"></script>	 -->
		<!--end::Web font -->
        <!--begin::Base Styles -->

		<!-- Summernote CSS Text Editor -->
		<link href="<?php echo base_url("assets/css/hris/summernote.min.css"); ?>" rel="stylesheet" type="text/css" />
		<!-- end::Summernote CSS -->

		<link href="<?php echo base_url("assets/fonts/montserrat/montserrat.css"); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("assets/fonts/roboto/roboto.css"); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("assets/plugins/bootstrap/bootstrap.min.css"); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("assets/vendors/custom/datatables/datatables.bundle.css"); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("assets/vendors/base/vendors.bundle.css"); ?>" rel="stylesheet" type="text/css" />
		<?php if($temp_template_css == false): ?>
			<link href="<?php echo base_url("assets/demo/demo3/base/style.bundle.css"); ?>" rel="stylesheet" type="text/css" />
		<?php endif; ?>
		<?php echo $this->core_layout->getStoredCss(); ?>
		
		<link href="<?php echo base_url("assets/css/custom.css"); ?>" rel="stylesheet" type="text/css" />
		<!--end::Base Styles -->
		<!-- lightbox start -->
		<script type="text/javascript" language="javascript" src="<?php echo base_url('assets/plugins/lightbox/js/lightbox.js'); ?>"></script>
		<script>
			setBaseUrl("<?php echo base_url(); ?>");
			setSiteUrl("<?php echo site_url(); ?>");
			setCrfSecurityToken("<?php echo $this->security->get_csrf_token_name(); ?>", "<?php echo $this->security->get_csrf_hash(); ?>");
			var currentModule = <?php echo (isset($logged_in["module_id"]) && $logged_in["module_id"])? intval($logged_in["module_id"]): 0; ?>;
			const idleTimerState = <?php echo json_encode($this->core_layout->getIdleTimerState()); ?>;
		</script>
		<link href="<?php echo base_url("assets/plugins/lightbox/css/lightbox.css"); ?>" rel="stylesheet" type="text/css" media="screen" />
		<!-- lightbox end -->
	</head>
	<!-- end::Head -->
	<!-- start::Body -->
	<?php
	$tempData = array();
	if(isset($no_sidenav) && $no_sidenav){ $tempData["no_sidenav"] = $no_sidenav; }
	$defaultBodyClass = "m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile 
	m-aside-left--enabled m-aside-left--fixed m-aside-left--offcanvas 
	m-footer--push m-aside--offcanvas-default ";
	$tempBodyClassRendered = "m-aside-left--skin-dark";
	$tempBodyClassRendered = (isset($template_body_class) && $template_body_class)? $template_body_class: $tempBodyClassRendered;
	
	$tempBodyClass = (isset($template_bodyclass) && $template_bodyclass)? $template_bodyclass: "{$this->core_layout->getBodyClass()} {$defaultBodyClass} {$tempBodyClassRendered}";
	
	$bodyClassTemplate = (isset($no_sidenav) && $no_sidenav)? "{$this->core_layout->getBodyClass()} m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside--offcanvas-default" : $tempBodyClass; ?>
	
	<body class="<?php echo $bodyClassTemplate; ?>">
	<?php if(isset($template_nav) && $template_nav): ?>
		<?php echo $template_nav ?>
	<?php $this->load->view("core/templates/{$template_nav}/nav", $tempData); ?>
	<?php else: ?>
	<?php $this->load->view('core/templates/nav', $tempData); ?>
	<?php endif; ?>
	<!-- BEGIN: Subheader -->
	<?php $this->load->view('core/templates/breadcrumbs'); ?>
	<!-- END: Subheader -->