<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <style>
        body { font-family:Poppins; background:#f5f5f5; }
        .box {
            width:400px; margin:120px auto; background:white;
            padding:25px; text-align:center; border-radius:12px;
            box-shadow:0 6px 20px rgba(0,0,0,0.1);
        }
        a {
            display:block; margin-top:20px;
            padding:12px; border-radius:10px;
            background:linear-gradient(135deg,#ffb347,#ffcc33);
            text-decoration:none; font-weight:600; color:black;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Payment Successful 🎉</h2>
    <p>Your payment has been verified instantly.</p>
    <a href="my_activity.php">Go to My Activity</a>
</div>

</body>
</html>
