<!DOCTYPE html>
<html lang="en">
<head>
    <title>API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css">
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
    <script>
        window.onload = function() {
            SwaggerUIBundle({
                url: "<?php echo base_url('assets/swagger/swagger.json'); ?>",
                dom_id: '#swagger-ui'
            });
        }
    </script>
</body>
</html>
