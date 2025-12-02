<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$eventId = $_GET['event_id'] ?? null;

if (!$eventId) {
    echo "Invalid event.";
    exit;
}

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    echo "Event not found.";
    exit;
}

$amount = floatval($event['payment_amount'] ?? 0);

if ($amount <= 0) {
    header("Location: event_details.php?event_id=" . $eventId);
    exit;
}

// Check if user already registered
$stmt = $pdo->prepare("SELECT registration_id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$userId, $eventId]);
$registrationId = $stmt->fetchColumn();

// If no registration, create one
if (!$registrationId) {
    $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$userId, $eventId]);
    $registrationId = $pdo->lastInsertId();
}

// If payment submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $method = $_POST['payment_method'] ?? null;

    if (!$method) {
        $error = "Please select a payment method.";
    } else {
        // SIMULATE PAYMENT SUCCESS

        $stmt = $pdo->prepare("
            INSERT INTO payments (registration_id, user_id, event_id, amount, payment_method, payment_status)
            VALUES (?, ?, ?, ?, ?, 'verified')
        ");
        $stmt->execute([$registrationId, $userId, $eventId, $amount, $method]);

        header("Location: payment_success.php?event_id=" . $eventId);
        exit;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment - <?= htmlspecialchars($event['title']) ?></title>

    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #f5f5f5;
        }
        .pay-card {
            width: 450px;
            margin: 120px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        select, button {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        button {
            background: linear-gradient(135deg,#ffb347,#ffcc33);
            font-weight: 600;
            cursor: pointer;
        }
        button:hover {
            background: linear-gradient(135deg,#ffcc33,#ffb347);
        }
        .error {
            background: #f8d7da;
            padding: 10px;
            border-radius: 6px;
            color: #721c24;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

<div class="pay-card">
    <h2>Payment for: <?= htmlspecialchars($event['title']) ?></h2>
    <p><strong>Amount to Pay:</strong> RM <?= number_format($amount, 2) ?></p>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Select Payment Method</label>
        <select name="payment_method" required>
            <option value="">-- Choose Method --</option>
            <option value="FPX Online Banking">FPX Online Banking</option>
            <option value="Debit/Credit Card">Debit/Credit Card</option>
            <option value="eWallet (TNG/Boost/Grab)">eWallet (TNG/Boost/Grab)</option>
        </select>

        <button type="submit">Pay Now</button>
    </form>
</div>

</body>
</html>
