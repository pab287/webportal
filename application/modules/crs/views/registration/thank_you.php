<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Submitted</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<script>
(function () {
    function getCookie(name) {
        const cookies = document.cookie.split('; ');
        for (let cookie of cookies) {
            const parts = cookie.split('=');
            if (parts[0] === name) {
                return decodeURIComponent(parts[1]);
            }
        }
        return null;
    }
    const limitCookie = getCookie("gcc_already_submitted");
    if (!limitCookie) {
        const path = window.location.pathname.replace(/\/$/, '');
        const basePath = path.substring(0, path.lastIndexOf('/'));
        window.location.replace(basePath);
    }
})();
</script>
<style>
    body {
        background:#f5f7fa;
        height:100vh;
        display:flex;
        align-items:center;
        justify-content:center;
        font-family: system-ui, sans-serif;
    }

    .thank-you-card {
        max-width:520px;
        border-radius:12px;
        box-shadow:0 10px 30px rgba(0,0,0,.08);
    }

    .check-icon {
        font-size:60px;
        color:#28a745;
    }
</style>
</head>

<body>

<div class="card thank-you-card text-center p-5">
    <div class="check-icon mb-3">✔</div>
    <h3 class="mb-3">Thank You!</h3>
    <p class="text-muted">
        Your job application has been successfully submitted.
        Our HR team will review your application and contact you if you qualify.
    </p>
</div>

</body>
</html>