<?php $isAsset = (isset($is_asset) && $is_asset)? true: false; var_dump($isAsset);  ?>
<?php $urlRedirect = ($isAsset)? "ams/assets/asset_images/assets":"ams/assets/asset_images/vehicles"; ?>
<?php $fileuploadUrl = (isset($itemId) && $itemId)? site_url("{$urlRedirect}/{$itemId}"): site_url($urlRedirect); ?>

<?php $urlSetImage = ($isAsset)? "ams/assets/set_images/assets":"ams/assets/set_images/vehicles"; ?>
<?php $urlSetImageUrl = (isset($itemId) && $itemId)? site_url("{$urlSetImage}/{$itemId}"): site_url($urlSetImage); ?>

<link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css"); ?>">
<link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload.css"); ?>">
<link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload-ui.css"); ?>">
<noscript><link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload-noscript.css"); ?>"></noscript>
<noscript><link rel="stylesheet" href="<?php echo base_url("assets/global/plugins/uploadui/css/jquery.fileupload-ui-noscript.css"); ?>"></noscript>
<div class="modal fade" id="modalUploadImage" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">
		Upload Image
		</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">
				×
			</span>
		</button>
	</div>
	<div class="modal-body">
		<form id="fileupload" enctype="multipart/form-data">
		<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="row fileupload-buttonbar">
			<div class="col-lg-7">
				<!-- The fileinput-button span is used to style the file input field as button -->
				<span class="btn btn-success fileinput-button">
					<i class="la la-plus"></i>
					Add Image
					<input type="file" name="files[]" multiple>
				</span>
				<button type="submit" class="btn btn-primary start btnNew">
					<i class="la la-play"></i>
					Start upload
				</button>
				<button type="reset" class="btn cancel">
					<i class="la la-ban"></i>
					Cancel
				</button>
				<!-- The global file processing state -->
				<span class="fileupload-process"></span>
			</div>
			<!-- The global progress state -->
			<div class="col-lg-5 fileupload-progress fade">
				<!-- The global progress bar -->
				<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					<div class="progress-bar progress-bar-success" style="width:0%;"></div>
				</div>
				<!-- The extended global progress state -->
				<div class="progress-extended">&nbsp;</div>
			</div>
		</div>
		<table role="presentation" class="table table-striped">
			<col width="5%">
			<col width="65%">
			<col width="15%">
			<col width="15%">
			<tbody class="files"></tbody>
		</table>
		</form>
		<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
			<div class="slides"></div>
			<h3 class="title"></h3>
			<a class="prev">‹</a>
			<a class="next">›</a>
			<a class="close">×</a>
			<a class="play-pause"></a>
			<ol class="indicator"></ol>
		</div>
		<script id="template-upload" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
			<tr class="template-upload">
				<td>
					<span class="preview"></span>
				</td>
				<td>
					<p class="name">{%=file.name%}</p>
				</td>
				<td>
					<p class="size">Processing...</p>
					<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
				</td>
				<td>
					{% if (!i && !o.options.autoUpload) { %}
						<button class="btn btn-primary start btnNew btn-sm" disabled>
							<i class="la la-upload"></i>
						</button>
					{% } %}
					{% if (!i) { %}
						<button class="btn btn-warning btnNew cancel btn-sm">
							<i class="la la-ban"></i>
						</button>
					{% } %}
				</td>
			</tr>
		{% } %}
		</script>
		<!-- The template to display files available for download -->
		<script id="template-download" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
			<tr class="template-download">
				<td>
					<span class="preview">
						{% if (file.thumbnailUrl) { %}
							<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
						{% } %}
					</span>
				</td>
				<td>
					<p class="name">
						{% if (file.url) { %}
							<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
							<div class="radio">
								<label><input type="radio" name="primary" class="primary_image" value="{%=file.uploaded%}" {% if (file.isPrimary) { %} checked {% } %}><small>Set As Primary Image</small></label>
								<input type="hidden" class="image-url" name="url[]" value="{%=file.url%}" />
								<input type="hidden" class="image-thumbnail" name="thumbnail[]" value="{%=file.thumbnailUrl%}" />
								<input type="hidden" class="image-name" name="name[]" value="{%=file.uploaded%}" />
							</div>
						{% } else { %}
							<span>{%=file.name%}</span>
						{% } %}
					</p>
					{% if (file.error) { %}
						<div><span class="label label-danger">Error</span> {%=file.error%} </div>
					{% } %}
				</td>
				<td>
					<span class="size">{%=o.formatFileSize(file.size)%}</span>
				</td>
				<td>
					{% if (file.deleteUrl) { %}
						<button class="btn btn-danger delete btn-sm" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
							<i class="la la-trash"></i>
						</button>
					{% } else { %}
						<button class="btn btn-warning cancel btn-sm">
							<i class="la la-ban"></i>
						</button>
					{% } %}
				</td>
			</tr>
		{% } %}
		</script>
	</div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary btnSave btnSaveChanges" data-id="<?php echo (isset($itemId) && $itemId)? $itemId: 0; ?>">Save Changes</button>
		<button class="btn btn-default btnCloseModal" data-dismiss="modal">Close</button>
	  </div>
	</div>
	<!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/vendor/jquery.ui.widget.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/blueimp/tmpl.min.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/blueimp/load-image.all.min.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/blueimp/canvas-to-blob.min.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/blueimp/jquery.blueimp-gallery.min.js"); ?>"></script>

<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.iframe-transport.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload-process.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload-image.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload-audio.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload-video.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload-validate.js"); ?>"></script>
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload-ui.js"); ?>"></script>
<script>
$(function () {
    'use strict';
	var url = "<?php echo $fileuploadUrl; ?>";
	/* var itemId = <?php echo (isset($itemId) && $itemId)? $itemId: 0; ?>;
	if(itemId){ url = url+"/"+itemId; } */
	
    $('#fileupload').fileupload({
		url: url 
	}).on('fileuploaddone', function (e, data) {
		var _result = data.result;
		console.log(_result);
		
		if(_result.response == false){
			toastr.error(_result.data.error, "Error Uploading File");
		}
    });
	
	 $.ajax({
		url: $('#fileupload').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#fileupload')[0]
	}).always(function () {
		$(this).removeClass('fileupload-processing');
	}).done(function (result) {
		$(this).fileupload('option', 'done')
			.call(this, $.Event('done'), {result: result});
	});
});

lightbox.option({
	'resizeDuration': 200,
	'wrapAround': true
});

$(document).on("click", ".btnSaveChanges", function(){
	var _form = $("#fileupload");
	var _itemId = $(this).data("id");
	var _url = "<?php echo $urlSetImageUrl; ?>";
	/*** if(_itemId !== 0){ _url = _url+"/"+_itemId; } ***/
	
	$.ajax({
		url: _url,
		dataType: "json",
		type: "post",
		data: _form.serialize(),
		success: function(json){
			var _container = $(".custom-image_container");
			if(typeof _container !== "undefined" && _container.length > 0){
				_container.empty().append(json.html);
			}
		}
	}).done(function(){
		$("#modalUploadImage").modal("hide");
		var _modalContainer = $("#fileupload-modal_content");
		if(typeof _modalContainer !== "undefined"){
			_modalContainer.empty();
		}
	});
});
</script>