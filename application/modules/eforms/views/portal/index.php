<div class="m-content">
<?php echo $portal_content; ?>
<style type="text/css">
.im-caret {
	-webkit-animation: 1s blink step-end infinite;
	animation: 1s blink step-end infinite;
}
@keyframes blink {
	from, to {
		border-right-color: black;
	}
	50% {
		border-right-color: transparent;
	}
}
@-webkit-keyframes blink {
	from, to {
		border-right-color: black;
	}
	50% {
		border-right-color: transparent;
	}
}
.im-static {
	color: grey;
}
.icon {
    position: absolute;
    right: 15px;
    top: 0px;
    font-size: 6.5rem;
}
.icon .custom-large_icon {
    font-size: inherit;
    color: rgba(0,0,0,0.15);
}
.row.row-navigation_icon a {
    text-decoration: none;
}
.row.row-navigation_icon .m-portlet__body {
    color: #FFFFFF !important;
}
.m-portlet.m-portlet--fit.m-portlet--skin-dark.m--bg-disabled {
  background-color: #C6C6C6 !important;
}
</style>
</div>