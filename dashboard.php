<?php 
include 'db.php'; 

// 1. ดึงสถิติจำนวนครั้งการจอง แบ่งตามกลุ่มงาน (สำหรับกราฟวงกลม)
$dept_query = $conn->query("SELECT department, COUNT(*) as count FROM bookings GROUP BY department");
$depts = []; $dept_counts = [];
while($r = $dept_query->fetch_assoc()){
    $depts[] = $r['department'];
    $dept_counts[] = $r['count'];
}

// 2. ดึงสถิติจำนวนชั่วโมงการใช้งาน แบ่งตามชื่อห้อง (สำหรับกราฟแท่ง)
$room_stats = $conn->query("SELECT r.room_name, SUM(TIMESTAMPDIFF(HOUR, b.start_time, b.end_time)) as total_hours 
                            FROM bookings b 
                            JOIN rooms r ON b.room_id = r.room_id 
                            GROUP BY r.room_id");
$room_names = []; $usage_hours = [];
while($row = $room_stats->fetch_assoc()){
    $room_names[] = $row['room_name'];
    $usage_hours[] = $row['total_hours'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Dashboard - Pakpoon School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Sarabun:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { --primary: #4361ee; --bg: #f8f9fc; }
        body { background-color: var(--bg); font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif; color: #2b2d42; }
        .navbar { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); backdrop-filter: blur(10px); }
        .card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .stat-card { background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white; }
    </style>
</head>
<body>

<nav class="navbar sticky-top py-3 mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="bi bi-building-fill-check me-2"></i>PAKPOON BOOKING
        </a>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3 border"><i class="bi bi-house-door"></i> หน้าหลัก</a>
            <a href="manage_rooms.php" class="btn btn-light btn-sm rounded-pill px-3 border"><i class="bi bi-gear"></i> จัดการห้อง</a>
            <a href="dashboard.php" class="btn btn-primary btn-sm rounded-pill px-3 text-white"><i class="bi bi-pie-chart-fill"></i> สถิติ</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold animate__animated animate__fadeIn"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>แดชบอร์ดสรุปข้อมูลการวิจัย</h2>
            <p class="text-muted small">ข้อมูลวิเคราะห์การใช้งานห้องประชุมออนไลน์ โรงเรียนปากพูน</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-4 stat-card animate__animated animate__zoomIn">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 opacity-75">จำนวนการจองทั้งหมด</p>
                        <h2 class="fw-bold m-0"><?php echo $conn->query("SELECT id FROM bookings")->num_rows; ?> ครั้ง</h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 animate__animated animate__zoomIn" style="animation-delay: 0.1s">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">จำนวนห้องประชุม</p>
                        <h2 class="fw-bold m-0 text-primary"><?php echo $conn->query("SELECT room_id FROM rooms")->num_rows; ?> ห้อง</h2>
                    </div>
                    <i class="bi bi-door-open fs-1 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card p-4 h-100 animate__animated animate__fadeInUp">
                <h5 class="fw-bold mb-4 text-center">สัดส่วนการจองตามกลุ่มงาน</h5>
                <canvas id="deptPieChart"></canvas>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card p-4 h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <h5 class="fw-bold mb-4 text-center">สรุปชั่วโมงการใช้งานแต่ละห้อง (ชม.)</h5>
                <canvas id="roomBarChart"></canvas>
            </div>
        </div>
    </div>
</div>



<script>
    // 1. Pie Chart (กลุ่มงาน)
    const deptCtx = document.getElementById('deptPieChart').getContext('2d');
    new Chart(deptCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($depts); ?>,
            datasets: [{
                data: <?php echo json_encode($dept_counts); ?>,
                backgroundColor: ['#4361ee', '#4cc9f0', '#4895ef', '#3f37c9', '#b5179e'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. Bar Chart (ชั่วโมงการใช้งาน)
    const roomCtx = document.getElementById('roomBarChart').getContext('2d');
    new Chart(roomCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($room_names); ?>,
            datasets: [{
                label: 'จำนวนชั่วโมงรวม',
                data: <?php echo json_encode($usage_hours); ?>,
                backgroundColor: '#4361ee',
                borderRadius: 10
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<footer class="text-center py-4 text-muted small">© 2026 PAKPOON SCHOOL - RESEARCH DATA</footer>
</body>
</html>