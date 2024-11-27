<div class="m-content">
<?php //echo $portal_content; ?>
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
<div class="m-content">
	<div class="row row-navigation_icon">
	<?php if($data && isset($data)): foreach($data as $_data): ?>
		<div class="col-xl-3">
			<a href="javascript:void(0);" class="module_redirect" data-id="<?php echo $_data["id"];?>">
			<div class="m-portlet m-portlet--fit m-portlet--skin-dark m--bg-brand">
				<div class="m-portlet__body">
					<h3><?php echo $_data["name"];?></h3>
					<p><?php echo $_data["database"];?></p>
					<div class="icon">
						<i class="flaticon-interface-2 custom-large_icon"></i>
					</div>
				</div>
			</div>
			</a>
		</div>
		
		<?php endforeach; else: ?>
			<div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-brand alert-dismissible fade show" role="alert">
				<div class="m-alert__icon">
					<i class="flaticon-exclamation-1"></i>
					<span></span>
				</div>
				<div class="m-alert__text">
					<strong>
						Notification!
					</strong>
					No warehouse available at the moment.
				</div>
				<div class="m-alert__close">
					<!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button> -->
				</div>
			</div>
		<?php endif;?>
	</div>
</div>

<div class="modal fade" id="iframe_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	<div class="modal-dialog modal-lg" role="document" style="min-width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					New message
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
				<button type="button" class="btn btn-primary">
					Send message
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(".row-navigation_icon").on("click",".module_redirect",function(){
		var data_id = $(this).attr("data-id");
		// $( ".modal-body" ).empty();
		// $('<iframe />');  // Create an iframe element
        //     $('<iframe />', {
        //         name: 'frame1',
        //         id: 'frame1',
        //         src: '<?php ///echo root_url('gccislizares');?>'
        //     }).appendTo($( ".modal-body" )).css({"min-width": "100%", "height": "1000px","border":0});;

		// 	$('#iframe_modal').modal("show");
		window.location.href = "<?php echo site_url('inventory/dashboard');?>"+"/"+data_id
	});
</script>