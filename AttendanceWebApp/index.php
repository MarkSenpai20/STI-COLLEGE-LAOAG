<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="student-container">
    
    <!-- LOADING / IDLE STATE -->
    <div id="idle-screen" class="state-screen">
        <div class="pulse-icon">📡</div>
        <h2>Waiting for Teacher...</h2>
        <p>Please wait for the class to start.</p>
    </div>

    <!-- REGISTER FORM -->
    <div id="register-screen" class="state-screen" style="display:none;">
        <h2>📝 Class Registration</h2>
        <p>Enter your details to join the class list.</p>
        <form onsubmit="submitForm(event, 'REGISTER')">
            <input type="text" id="reg_name" placeholder="Full Name (e.g. Mark Cruz)" required>
            <input type="text" id="reg_id" placeholder="Student ID (e.g. 2025-01)" required>
            <button type="submit">Register Identity</button>
        </form>
    </div>

    <!-- ATTENDANCE FORM (No Name Input) -->
    <div id="attendance-screen" class="state-screen" style="display:none;">
        <h2>✅ Mark Present</h2>
        <p>Enter your ID to sign in.</p>
        <form onsubmit="submitForm(event, 'ATTENDANCE')">
            <input type="text" id="att_id" placeholder="Student ID" required>
            <button type="submit" class="btn-green">PRESENT!</button>
        </form>
    </div>

    <!-- SUCCESS MESSAGE -->
    <div id="success-screen" class="state-screen" style="display:none;">
        <div class="icon">🎉</div>
        <h2 id="success-msg">Success!</h2>
        <button onclick="location.reload()">Done</button>
    </div>

</div>

<script>
let currentMode = 'IDLE';

// 1. Check Status every 3 seconds
function checkStatus() {
    fetch('db_connect.php?action=get_status')
    .then(res => res.json())
    .then(data => {
        if (data.mode !== currentMode) {
            currentMode = data.mode;
            updateUI(data.mode);
        }
    });
}

function updateUI(mode) {
    document.querySelectorAll('.state-screen').forEach(el => el.style.display = 'none');
    
    if (mode === 'REGISTER') document.getElementById('register-screen').style.display = 'block';
    else if (mode === 'ATTENDANCE') document.getElementById('attendance-screen').style.display = 'block';
    else document.getElementById('idle-screen').style.display = 'block';
}

// 2. Handle Submits
function submitForm(e, type) {
    e.preventDefault();
    const formData = new FormData();
    
    if (type === 'REGISTER') {
        formData.append('student_name', document.getElementById('reg_name').value);
        formData.append('student_id', document.getElementById('reg_id').value);
    } else {
        formData.append('student_id', document.getElementById('att_id').value);
    }

    fetch('db_connect.php?action=submit', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.querySelectorAll('.state-screen').forEach(el => el.style.display = 'none');
            document.getElementById('success-screen').style.display = 'block';
            document.getElementById('success-msg').innerText = data.msg;
        } else {
            alert(data.message);
        }
    });
}

setInterval(checkStatus, 3000);
checkStatus(); // Run immediately
</script>
</body>
</html>


