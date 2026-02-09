<?php $data = $this->session->flashdata('error_data'); ?>
<div class="m-grid__item m-grid__item--fluid m-grid  m-error-3" style="background-image: url('<?php echo base_url("assets/images/page_not_found/bg_404.jpg");?>');">
    <div class="m-error_container">
        <span class="m-error_number">
            <h1><?php echo $data['$error_code'] ?? "403"; ?></h1>
        </span>
        <p class="m-error_title m--font-light">
            <?php echo $data['error_title'] ?? "Access Denied"; ?>
        </p>
        <p class="m-error_subtitle">
            <?php echo $data['error_subtitle'] ?? "No Permission"; ?>
        </p>
        <p class="m-error_description">
            <?php echo $data['error_description'] ?? "You don't have permission to access this page."; ?>
        </p>
    </div>
</div>
<script>
$(".m-grid__item.m-grid__item--fluid.m-wrapper .m-subheader").remove();
var _container = $(".m-grid__item.m-grid__item--fluid.m-wrapper");
if(_container.hasClass("m-grid__item m-grid__item--fluid m-wrapper") == true){
    _container.removeClass("m-grid__item m-grid__item--fluid m-wrapper");
    _container.addClass("m-grid m-grid--hor m-grid--root m-page");
}
</script>
