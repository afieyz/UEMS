<?php
session_start();
require '../config/db.php';

// User must login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Event System</title>
    <link rel="stylesheet" href="../assets/css/create_event.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Simple toggle section (matches your UI theme) */
        .price-toggle-box {
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        #price-box {
            display: none;
            margin-top: 8px;
        }

        #price-box input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body class="ce-body">

<header class="ce-header">
    <a href="../index.php" class="back-btn">← Back</a>
    <h1>Create New Event</h1>
    <div></div>
</header> 

<main class="ce-main">

    <div class="ce-card">

        <form action="submit_event.php" method="POST" enctype="multipart/form-data" class="ce-form">

            <label>Event Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Location</label>
            <input type="text" name="location" required>

            <div class="ce-row">
                <div>
                    <label>Event Date</label>
                    <input type="date" name="event_date" required>
                </div>
                <div>
                    <label>Start Time</label>
                    <input type="time" name="start_time" required>
                </div>
                <div>
                    <label>End Time</label>
                    <input type="time" name="end_time" required>
                </div>
            </div>

            <label>Category</label>
            <select name="category" required>
                <option value="">-- Select Category --</option>
                <option value="Academic">Academic</option>
                <option value="Sports">Sports</option>
                <option value="Competition">Competition</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Workshop">Workshop</option>
                <option value="Cultural">Cultural</option>
            </select>

            <label>Activity Level</label>
            <select name="activity_level">
                <option value="">-- Select Level --</option>
                <option value="University Level">University Level</option>
                <option value="Faculty Level">Faculty Level</option>
                <option value="Program Level">Program Level</option>
            </select>

            <label>Maximum Participants</label>
            <input type="number" name="num_participants" min="1" required>

            <!-- 🔹 Optional Payment Section -->
            <div class="price-toggle-box">
                <label>
                    <input type="checkbox" id="isPaid" onclick="togglePrice()"> Paid Event?
                </label>
            </div>

            <div id="price-box">
                <label>Ticket Price (RM)</label>
                <input type="number" name="price" step="0.01" min="0" placeholder="Leave empty if free">
            </div>

            <label>Upload Proposal (PDF)</label>
            <input type="file" name="proposal_file" accept="application/pdf">

            <button type="submit" class="ce-btn">Submit Event</button>

        </form>
    </div>

</main>

<footer class="ce-footer">
    &copy; 2025 Universiti Malaysia Sabah | Contact JHEP
</footer>

<script>
function togglePrice() {
    let box = document.getElementById("price-box");
    box.style.display = document.getElementById("isPaid").checked ? "block" : "none";
}
</script>

</body>
</html>
