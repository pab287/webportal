<div id="img_primary">
	<?php $primaryImage = (isset($data["primary"]) && $data["primary"])? $data["primary"]: ""; ?>
	<?php $primaryImageUrl = (isset($data["primary"]) && $data["primary"])? base_url("uploads/files/images/{$data["id"]}/{$data["primary"]}"): base_url("uploads/module/ams/images/no_image.jpg"); ?>
	<?php if($primaryImage): ?>
	<input type="hidden" class="primary-image" name="primary_image" value="<?php echo $primaryImage; ?>" />
	<a data-lightbox='roadtrip' data-title='<?php echo $primaryImage; ?>' href='<?php echo base_url("uploads/files/images/{$data["id"]}/{$primaryImage}"); ?>'>
		<img id='img_prim' class='img-responsive' src='<?php echo $primaryImageUrl; ?>' style="width: 100%;" />
	</a>
	<?php else: ?>
		<img id='img_prim' class='img-responsive' src='<?php echo $primaryImageUrl; ?>' />
	<?php endif; ?>
	<button type="button" class="btn btn-sm btn-success" id="btnChangeOpenModal" data-toggle="modal" data-target="#modalUploadImage" style="position: absolute;margin: auto;top: 25px;left: 50%;transform: translate(-50%, -50%);"><i class="fa fa-camera"></i> Change</button>
</div>
<div id="alt_images" style="margin: 20px 0 0 0;">
<?php if(isset($data["thumbnail"]) && $data["thumbnail"]): ?>
<?php foreach($data["thumbnail"] as $index => $url):?>
	<?php if(isset($data["name"][$index]) && $data["name"][$index] && $data["name"][$index] !== $primaryImage): ?>
		<a data-lightbox='roadtrip' data-title='<?php echo $data["name"][$index]; ?>' href='<?php echo $data["url"][$index]; ?>'>
			<img src="<?php echo $url; ?>" />
		</a>
		<input type="hidden" class="uploaded-images" name="uploaded_image[]" value="<?php echo $data["name"][$index]; ?>" />
	<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
</div>