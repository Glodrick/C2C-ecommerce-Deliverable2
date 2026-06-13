<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signed Out | GloMart</title>
    <meta http-equiv="refresh" content="3;url=login.php">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color:#f4f7fa; display:flex; align-items:center; justify-content:center; height:100vh;">
    <div style="background:#fff; padding:50px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.08); text-align:center; max-width:400px; width:100%;">
        <div style="font-size:50px; color:#27ae60; margin-bottom:20px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="font-baloo" style="font-weight:700; color:#333; margin-bottom:10px;">Signed Out</h2>
        <p class="font-rale text-muted" style="margin-bottom:30px;">You have been successfully logged out of your account.</p>
        <a href="login.php" class="btn btn-block color-primary-bg text-white font-baloo" style="border-radius:25px; padding:12px; font-size:16px; font-weight:600;">
            Return to Login
        </a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
