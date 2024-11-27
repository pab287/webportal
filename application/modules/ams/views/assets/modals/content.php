<?php 
	$getUserData = $this->core_layout->getUserLoggedIn();
?>
<div id="img_primary" style="margin: 0 auto; text-align: center;">
	<?php $primaryImage = (isset($data["primary"]) && $data["primary"])? $data["primary"]: ""; ?>
	<?php $primaryImageUrl = (isset($data["primary"]) && $data["primary"])? base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$data["primary"]}"): base_url("assets/images/ams/images/no_image.jpg"); ?>
	<?php if($primaryImage): ?>
	<input type="hidden" class="primary-image" name="primary_image" value="<?php echo $primaryImage; ?>" />
	<a data-lightbox='roadtrip' data-title='<?php echo $primaryImage; ?>' href='<?php echo base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$primaryImage}"); ?>'>
		<img id='img_prim' class='img-responsive' style="max-width: 486px; margin: 0 auto;" src='<?php echo $primaryImageUrl; ?>' />
	</a>
	<?php else: ?>
		<img id='img_prim' class='img-responsive' style="max-width: 486px; margin: 0 auto;" src='<?php echo $primaryImageUrl; ?>' />
	<?php endif; ?>
	<br />
	<button type="button" style="margin: 0 auto; width: auto;" class="btn btn-sm btn-success btnChange" data-toggle="modal" data-target="#modalUploadImage"><i class="fa fa-camera"></i> Change</button>
</div>
<div id="alt_images">
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