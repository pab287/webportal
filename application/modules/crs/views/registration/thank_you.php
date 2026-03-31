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
<script>
(function () {
    const REDIRECT_AFTER = 3 * 60 * 1000; // 3 minutes
    let remaining = REDIRECT_AFTER / 1000;

    const timer = setInterval(() => {
        remaining--;

        const el = document.getElementById('countdown');
        if (el) {
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            el.textContent =
                `${minutes}:${seconds.toString().padStart(2,'0')}`;
        }

        if (remaining <= 0) clearInterval(timer);
    }, 1000);

    setTimeout(() => {
        window.location.replace("https://www.facebook.com/gcandcgroup");
    }, REDIRECT_AFTER);
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
<div class="container-fluid vh-100 p-0">
    <div class="row g-0 h-100">
        <div class="col-md-6 d-none d-md-block">
            <img src="<?= base_url('assets/hiring.jpg'); ?>" alt="" style="width:100%; height:100%; object-fit:cover; display:block;">
        </div>
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center p-5">
            <div class="check-icon mb-4" style="width:70px; height:70px; border-radius:50%; background:#e6f9f0; display:flex; align-items:center; justify-content:center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1aafa0" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h3 class="fw-bold mb-3">Thank You!</h3>
            <p class="text-muted text-center mb-4" style="max-width:360px; line-height:1.7;">
                Your job application has been successfully submitted.
                Our HR team will review your application and contact you if you qualify.
            </p>
            <a href="https://www.facebook.com/gcandcgroup" target="_blank"
               style="display:inline-flex; align-items:center; gap:8px; background:#1877F2; color:#fff; text-decoration:none; padding:10px 22px; border-radius:8px; font-size:14px; font-weight:500;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                </svg>
                Follow us on Facebook
            </a>
        </div>
    </div>
</div>
</body>
</html>