<?php $urlRedirect = "crs/crs_files"; ?>
<?php $fileuploadUrl = (isset($itemId) && $itemId)? site_url("{$urlRedirect}/{$itemId}"): site_url($urlRedirect); ?>

<?php $urlSetImage = "crs/set_files"; ?>
<?php $urlSetImageUrl = (isset($itemId) && $itemId)? site_url("{$urlSetImage}/{$itemId}"): site_url($urlSetImage); ?>
<?php 
	$getUserData = $this->core_layout->getUserLoggedIn();
?>
<div id="img_primary" style="margin: 0 auto; text-align: center;">
	<?php $primaryImage = (isset($data["primary"]) && $data["primary"])? $data["primary"]: ""; ?>
	<?php $primaryImageUrl = (isset($data["primary"]) && $data["primary"])? base_url("uploads/module/crs/files/temp_files/temp{$getUserData['id']}/{$data["primary"]}"): base_url("assets/images/ams/images/no_image.jpg"); ?>
	<?php if($primaryImage): ?>
	<input type="hidden" class="primary-image" name="primary_image" value="<?php echo $primaryImage; ?>" />
	<a data-lightbox='roadtrip' data-title='<?php echo $primaryImage; ?>' href='<?php echo base_url("uploads/module/crs/files/temp_files/temp{$getUserData['id']}/{$primaryImage}"); ?>'>
		<img id='img_prim' class='img-responsive' style="max-width: 486px; margin: 0 auto;" src='<?php echo $primaryImageUrl; ?>' />
	</a>
	<?php else: ?>
		<img id='img_prim' class='img-responsive' style="max-width: 486px; margin: 0 auto;" src='<?php echo $primaryImageUrl; ?>' />
	<?php endif; ?>
	<br />
	<button type="button" style="margin: 0 auto; width: auto;" class="btn btn-sm btn-success btnChange" data-toggle="modal" data-target="#modalUploadImage"><i class="fa fa-camera"></i> Change</button>
</div>
<div id="alt_images">

	<?php if(isset($data["name"][$index]) && $data["name"][$index] && $data["name"][$index] !== $primaryImage): ?>
		<a data-lightbox='roadtrip' data-title='<?php echo $data["name"][$index]; ?>' href='<?php echo $data["url"][$index]; ?>'>
			<img src="<?php echo $url; ?>" />
		</a>
		<input type="hidden" class="uploaded-images" name="uploaded_image[]" value="<?php echo $data["name"][$index]; ?>" />
	<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
</div>
<!-- <div class="row fileupload-buttonbar ">
			<div class="col-lg-7">
				<!-- The fileinput-button span is used to style the file input field as button -->
				<span class="btn btn-success fileinput-button">
					<i class="la la-plus"></i>
					Add File
					<input type="file" name="files[]" multiple>
				</span>
				<button type="submit" class="btn btn-primary start btnNew">
					<i class="la la-play"></i>
					Upload All
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
					
				</td>
				<td>
					<p class="name">
						{% if (file.url) { %}
							<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
							
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
						<!-- <button class="btn btn-danger delete btn-sm" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
							<i class="la la-trash"></i>
						</button> -->
						<div><span>Uploaded</span> </div>
					{% } else { %}
						<button class="btn btn-warning cancel btn-sm">
							<i class="la la-ban"></i>
						</button>
					{% } %}
				</td>
			</tr>
		{% } %}
		</script>
                

                    </div> -->
					<!-- <script src="<?php echo base_url("assets/global/plugins/uploadui/js/vendor/jquery.ui.widget.js"); ?>"></script>
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
<script src="<?php echo base_url("assets/global/plugins/uploadui/js/jquery.fileupload-ui.js"); ?>"></script> -->