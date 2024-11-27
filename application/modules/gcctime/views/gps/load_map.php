<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo $map['js']; ?>
	<script src="<?php echo base_url();?>assets/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<script type="text/javascript">
		function save_coordinates()
		{
			//alert($('#lat').val() + ', ' + $('#long').val());
            $.ajax({
                url : "<?php echo site_url('Employees_controller/ajax_save_coordinates')?>",
                type: "POST",
                data: { id : $('#id').val(), lat : $('#lat').val(), long : $('#long').val() },
                dataType: "JSON",
                success: function(data)
                {
                    window.location.replace("<?php echo base_url(); ?>Load_map/index/<?php echo $id; ?>");
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error: "save"');
                }
            });
		}
	</script>
</head>
<body>
	<?php echo $map['html']; ?>
	<input id="id" name="id" type="hidden" value="<?php echo $id; ?>">
	Latitude: <input id="lat" name="lat" type="text" value="<?php echo $lat; ?>">
    Longitude: <input id="long" name="long" type="text" value="<?php echo $long; ?>">
    <button type="button" onclick="save_coordinates()">Save Coordinates</button>
</body>
</html>