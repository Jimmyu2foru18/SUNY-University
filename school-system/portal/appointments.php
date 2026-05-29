<?php
// portal/appointments.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config/database.php';
require_once '../src/controllers/StudentController.php';
require_once '../includes/portal_header.php';

$auth = new StudentController($pdo);
$studentID = $_SESSION['user_id'];

// --- Handle AJAX Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'add') {
            $data = json_decode($_POST['appointment'], true);
            $stmt = $pdo->prepare("INSERT INTO Appointment (studentID, appointmentType, appointmentDate, appointmentTime, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$studentID, $data['appointment_type'], $data['appointment_date'], $data['appointment_time'], $data['message']]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } elseif ($_POST['action'] === 'cancel') {
            $stmt = $pdo->prepare("DELETE FROM Appointment WHERE appointmentID = ? AND studentID = ?");
            $stmt->execute([$_POST['appointment_id'], $studentID]);
            echo json_encode(['success' => true]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_all') {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM Appointment WHERE studentID = ? ORDER BY appointmentDate DESC");
    $stmt->execute([$studentID]);
    echo json_encode(['success' => true, 'appointments' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}
?>

<div class="portal-container">
    <?php include '../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Schedule Appointment</h1>
            <div id="appointmentFormSection" class="card p-4 shadow-sm border-0 mb-4">
                <form id="appointmentForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select id="appointmentType" class="form-select" required>
                                <option value="academic">Academic Advising</option>
                                <option value="admissions">Admissions</option>
                                <option value="financial">Financial Aid</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" id="preferredDate" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" id="preferredTime" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea id="message" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                </form>
            </div>

            <h3 class="fw-bold mb-3">My Appointments</h3>
            <div id="appointmentsList" class="card p-4 shadow-sm border-0"></div>
        </div>
    </main>
</div>

<script>
document.getElementById('appointmentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
        appointment_type: document.getElementById('appointmentType').value,
        appointment_date: document.getElementById('preferredDate').value,
        appointment_time: document.getElementById('preferredTime').value,
        message: document.getElementById('message').value
    };
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('appointment', JSON.stringify(data));
    const res = await fetch('appointments.php', { method: 'POST', body: formData }).then(r => r.json());
    if (res.success) { alert('Scheduled!'); location.reload(); }
});

async function loadAppointments() {
    const res = await fetch('appointments.php?action=get_all').then(r => r.json());
    const list = document.getElementById('appointmentsList');
    list.innerHTML = res.appointments.map(apt => `
        <div class="d-flex justify-content-between border-bottom py-2">
            <div><strong>${apt.appointmentType}</strong> - ${apt.appointmentDate} ${apt.appointmentTime}</div>
            <button class="btn btn-sm btn-outline-danger" onclick="cancel(${apt.appointmentID})">Cancel</button>
        </div>
    `).join('');
}

async function cancel(id) {
    if (!confirm('Cancel?')) return;
    const fd = new FormData();
    fd.append('action', 'cancel');
    fd.append('appointment_id', id);
    const res = await fetch('appointments.php', { method: 'POST', body: fd }).then(r => r.json());
    if (res.success) location.reload();
}

loadAppointments();
</script>
<?php require_once '../includes/footer.php'; ?>
