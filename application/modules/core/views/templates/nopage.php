<div class="m-grid__item m-grid__item--fluid m-grid  m-error-3" style="background-image: url('<?php echo base_url("assets/images/page_not_found/bg_404.jpg");?>');">
    <div class="m-error_container">
        <span class="m-error_number">
            <h1>404</h1>
        </span>
        <p class="m-error_title m--font-light">
            How did you get here
        </p>
        <p class="m-error_subtitle">
            Sorry we can't seem to find the page you're looking for.
        </p>
        <p class="m-error_description">
            There may be amisspelling in the URL entered,
            <br>
            or the page you are looking for may no longer exist.
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