<?php 
include 'db_connect.php'; 
$ip = gethostbyname(gethostname()); 
$current_folder = dirname($_SERVER['PHP_SELF']);

// CHECK LOGIN STATUS
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Console</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="teacher-body">

<?php if (!$is_logged_in): ?>
    <!-- LOGIN SCREEN -->
    <div class="login-wrapper">
        <div class="login-card">
            <h2>🔒 Admin Access</h2>
            <form onsubmit="handleLogin(event)">
                <input type="text" id="db_user" placeholder="DB Username (e.g. admin)" required>
                <input type="password" id="db_pass" placeholder="DB Password" required>
                <button type="submit">Log In</button>
                <p id="loginError" class="login-error"></p>
            </form>
        </div>
    </div>
    <script>
        function handleLogin(e) {
            e.preventDefault();
            const u = document.getElementById('db_user').value;
            const p = document.getElementById('db_pass').value;
            const err = document.getElementById('loginError');

            const fd = new FormData();
            fd.append('username', u);
            fd.append('password', p);

            fetch('db_connect.php?action=login', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    err.innerText = data.message;
                    err.style.display = 'block';
                }
            });
        }
    </script>
<?php else: ?>

    <!-- MAIN DASHBOARD -->
    <div class="main-layout">
        <!-- LEFT PANEL -->
        <div class="control-panel">
            <h2>🎮 Teacher Controls</h2>
            
            <div class="input-group">
                <label>Current Class Name</label>
                <input type="text" id="className" placeholder="e.g. Physics 101" value="Physics 101">
            </div>

            <div class="action-buttons">
                <input type="file" id="csvInput" accept=".csv" style="display:none" onchange="uploadCSV()">
                <button onclick="document.getElementById('csvInput').click()" class="btn-secondary">📂 Import List</button>
                <button onclick="exportCurrent()" class="btn-secondary">💾 Export Current</button>
            </div>

            <hr>

            <div class="control-box">
                <h3>📝 Registration Mode</h3>
                <p>Students enter Name & ID.</p>
                <button id="btnReg" class="btn-start" onclick="setMode('REGISTER')">Start Register</button>
            </div>

            <div class="control-box">
                <h3>✅ Attendance Mode</h3>
                <p>Students enter ID only.</p>
                <button id="btnAtt" class="btn-start" onclick="setMode('ATTENDANCE')">Start Sign In</button>
            </div>

            <button class="btn-stop" onclick="setMode('IDLE')">🛑 Stop Server</button>

            <button class="btn-logout" onclick="handleLogout()">🚪 Log Out</button>

            <div class="ip-box">
                <small>Student Link:</small>
                <h1>http://<?php echo $ip . $current_folder; ?></h1>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="live-panel">
            <div class="live-header">
                <h2 id="liveTitle">Status: Idle</h2>
                <div class="count-badge" id="liveCount">0</div>
            </div>
            
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Student Name</th>
                            <th>ID Number</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="liveTableBody"></tbody>
                </table>
                <!-- FIXED CLEAR BUTTON (FAB) -->
                <div class="btnClr" onclick="clrClick()">🗑️ Clear List</div>
            </div>
        </div>
    </div>

    <!-- HISTORY PANEL -->
    <div class="history-panel">
        <h3>📂 History Logs (Click to View)</h3>
        <div id="historyList" class="history-grid"></div>
    </div>

    <!-- MODAL -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Class Details</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <button id="modalExportBtn" class="btn-secondary" style="margin-bottom: 10px;">💾 Download CSV</button>
                <table class="styled-table">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>ID</th><th>Time</th></tr>
                    </thead>
                    <tbody id="modalTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    let currentMode = 'IDLE';

    // LOGOUT
    function handleLogout() {
        if(confirm("Are you sure you want to log out?")) {
            fetch('db_connect.php?action=logout').then(() => window.location.reload());
        }
    }

    // CLEAR LIST
    function clrClick() {
        if (currentMode === 'IDLE') {
            alert("Server is Idle. Nothing to clear.");
            return;
        }
        let warning = "Are you sure you want to clear this list?\n";
        if (currentMode === 'REGISTER') warning += "⚠️ WARNING: This will DELETE all registered student identities for this class!";
        if (currentMode === 'ATTENDANCE') warning += "This will reset today's attendance log for this class.";

        if (confirm(warning)) {
            fetch('db_connect.php?action=clear_live')
            .then(res => res.json())
            .then(data => {
                alert("List has been cleared.");
                fetchLiveData(); 
            });
        }
    }

    // 1. Set Mode
    function setMode(mode) {
        const className = document.getElementById('className').value;
        if(!className) { alert("Enter Class Name first!"); return; }

        const fd = new FormData();
        fd.append('mode', mode);
        fd.append('class_name', className);

        fetch('db_connect.php?action=set_mode', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            currentMode = mode;
            updateUI(mode, className);
            fetchLiveData();
        });
    }

    function updateUI(mode, className) {
        document.getElementById('btnReg').classList.remove('active');
        document.getElementById('btnAtt').classList.remove('active');
        if(mode === 'REGISTER') document.getElementById('btnReg').classList.add('active');
        if(mode === 'ATTENDANCE') document.getElementById('btnAtt').classList.add('active');
        document.getElementById('liveTitle').innerText = mode === 'IDLE' ? "Server Stopped" : `Live: ${mode} - ${className}`;
    }

    // 2. Fetch Live Data
    function fetchLiveData() {
        if(currentMode === 'IDLE') return;

        fetch('db_connect.php?action=live_list')
        .then(res => res.json())
        .then(data => {
            document.getElementById('liveCount').innerText = data.length;
            const tbody = document.getElementById('liveTableBody');
            let html = '';
            data.forEach((student, index) => {
                html += `<tr><td>${index + 1}.</td><td><strong>${student.student_name}</strong></td><td>${student.student_id}</td><td>${student.timestamp}</td></tr>`;
            });
            tbody.innerHTML = html;
        });
    }

    // 3. History
    function fetchHistory() {
        fetch('db_connect.php?action=get_history')
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(h => {
                html += `<div class="folder" onclick="openHistory('${h.class_name}', '${h.session_date}')"><span class="folder-icon">📁</span><div><b>${h.class_name}</b><br><small>${h.session_date}</small></div><span class="badge">${h.count}</span></div>`;
            });
            document.getElementById('historyList').innerHTML = html;
        });
    }

    function openHistory(className, date) {
        const modal = document.getElementById('historyModal');
        const title = document.getElementById('modalTitle');
        const tbody = document.getElementById('modalTableBody');
        const exportBtn = document.getElementById('modalExportBtn');

        title.innerText = `${className} (${date})`;
        exportBtn.onclick = function() { 
            window.location.href = `db_connect.php?action=export&class=${className}&date=${date}&type=attendance`; 
        };

        fetch(`db_connect.php?action=get_history_details&class=${className}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach((s, i) => { html += `<tr><td>${i+1}</td><td>${s.student_name}</td><td>${s.student_id}</td><td>${s.timestamp}</td></tr>`; });
            tbody.innerHTML = html;
            modal.style.display = "block";
        });
    }

    function closeModal() { document.getElementById('historyModal').style.display = "none"; }

    // 4. Import/Export
    function uploadCSV() {
        const file = document.getElementById('csvInput').files[0];
        const className = document.getElementById('className').value;
        if(!file || !className) return;
        const fd = new FormData();
        fd.append('csv_file', file);
        fd.append('class_name', className);
        fetch('db_connect.php?action=import', { method: 'POST', body: fd }).then(res => res.json()).then(data => { alert("Imported " + data.count + " students!"); });
    }

    function exportCurrent() {
        const className = document.getElementById('className').value;
        if(!className) return alert("Enter Class Name!");

        if(currentMode === 'IDLE') {
            alert("Please START a mode (Register or Sign In) first, so I know which list to export.");
            return;
        }
        let type = (currentMode === 'REGISTER') ? 'registered' : 'attendance';
        window.location.href = `db_connect.php?action=export&class=${className}&type=${type}`;
    }

    setInterval(fetchLiveData, 2000);
    fetchHistory();
    </script>
<?php endif; ?>
</body>
</html>