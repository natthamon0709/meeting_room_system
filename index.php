<?php 
include 'db.php'; 
$message = "";
date_default_timezone_set('Asia/Bangkok');

// --- 1. จัดการการบันทึกข้อมูล ---
if (isset($_POST['book'])) {
    $room_id = $_POST['room_id'];
    $name = mysqli_real_escape_string($conn, $_POST['requester_name']);
    $dept = "ฝ่ายวิชาการ"; 
    $obj  = mysqli_real_escape_string($conn, $_POST['objective']);
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];

    if (strtotime($start) >= strtotime($end)) {
        $message = "<div class='alert alert-danger animate__animated animate__shakeX'>⚠️ เวลาเริ่มต้องน้อยกว่าเวลาสิ้นสุด</div>";
    } else {
        $check_sql = "SELECT b.*, r.room_name FROM bookings b JOIN rooms r ON b.room_id = r.room_id 
                      WHERE b.room_id = '$room_id' AND ('$start' < b.end_time AND '$end' > b.start_time)";
        $result = $conn->query($check_sql);
        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-warning animate__animated animate__shakeX'>⚠️ จองซ้ำ! ช่วงเวลานี้ถูกจองแล้ว</div>";
        } else {
            $sql = "INSERT INTO bookings (room_id, requester_name, department, objective, start_time, end_time) 
                    VALUES ('$room_id', '$name', '$dept', '$obj', '$start', '$end')";
            if ($conn->query($sql)) {
                $message = "<div class='alert alert-success animate__animated animate__fadeIn'>🎉 บันทึกการจองสำเร็จ!</div>";
            }
        }
    }
}

// --- 2. ตั้งค่า Filter และ Pagination ---
$filter_room = isset($_GET['f_room']) ? $_GET['f_room'] : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

$where_clauses = [];
if ($filter_room) $where_clauses[] = "b.room_id = '$filter_room'";
if ($search) $where_clauses[] = "(b.requester_name LIKE '%$search%' OR b.objective LIKE '%$search%')";
$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

$total_res = $conn->query("SELECT COUNT(b.id) AS total FROM bookings b $where_sql");
$total_rows = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakpoon School - Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Sarabun:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4361ee; --bg: #f8f9fc; }
        body { background-color: var(--bg); font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif; }
        .card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .navbar { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .status-pill { padding: 0.4rem 0.8rem; border-radius: 50px; font-weight: 700; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 5px; }
        .badge-live { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .badge-upcoming { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-done { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
        .time-box { background: #f1f4f9; border-radius: 10px; padding: 10px; min-width: 210px; border-left: 4px solid var(--primary); }
    </style>
</head>
<body>

<nav class="navbar sticky-top py-3 mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php"><i class="bi bi-building-fill-check me-2"></i>PAKPOON BOOKING</a>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-primary btn-sm rounded-pill px-3 text-white"><i class="bi bi-house-door-fill"></i> หน้าหลัก</a>
            <a href="manage_rooms.php" class="btn btn-light btn-sm rounded-pill px-3 border"><i class="bi bi-gear-fill"></i> จัดการห้อง</a>
            <a href="dashboard.php" class="btn btn-light btn-sm rounded-pill px-3 border"><i class="bi bi-pie-chart-fill"></i> สถิติ</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card p-4 animate__animated animate__fadeInLeft shadow-sm">
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-pencil-square me-2"></i>จองห้องประชุม</h5>
                <?php echo $message; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">เลือกห้องประชุม</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">เลือกห้องประชุม...</option>
                            <?php $rooms = $conn->query("SELECT * FROM rooms"); while($r = $rooms->fetch_assoc()) echo "<option value='{$r['room_id']}'>{$r['room_name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ชื่อผู้จอง</label>
                        <input type="text" name="requester_name" class="form-control" placeholder="ระบุชื่อ-นามสกุล" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">วัตถุประสงค์</label>
                        <textarea name="objective" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold">เริ่ม</label>
                            <input type="datetime-local" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">สิ้นสุด</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" name="book" class="btn btn-primary w-100 shadow-sm fw-bold">ยืนยันการจอง</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card p-3 mb-4 animate__animated animate__fadeInRight shadow-sm border-0">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold">กรองตามห้อง</label>
                        <select name="f_room" class="form-select form-select-sm">
                            <option value="">ทั้งหมด</option>
                            <?php 
                            $rooms = $conn->query("SELECT * FROM rooms");
                            while($r = $rooms->fetch_assoc()) {
                                $selected = ($filter_room == $r['room_id']) ? 'selected' : '';
                                echo "<option value='{$r['room_id']}' $selected>{$r['room_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="small fw-bold">ค้นหาผู้จอง/งาน</label>
                        <input type="text" name="search" class="form-control form-control-sm" value="<?= $search ?>" placeholder="ชื่อหรือวัตถุประสงค์...">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm"><i class="bi bi-search"></i> ค้นหา</button>
                    </div>
                </form>
            </div>

            <div class="card p-4 animate__animated animate__fadeInRight shadow-sm border-0" style="animation-delay: 0.1s">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-table me-2 text-primary"></i>รายการจองและสถานะ Real-time</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th>ห้อง</th>
                                <th>ข้อมูลการจอง</th>
                                <th>กำหนดการ/เวลา</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $now = date('Y-m-d H:i:s');
                            $sql = "SELECT b.*, r.room_name FROM bookings b 
                                    JOIN rooms r ON b.room_id = r.room_id 
                                    $where_sql ORDER BY b.start_time DESC LIMIT $start_from, $limit";
                            $res = $conn->query($sql);
                            while($row = $res->fetch_assoc()): 
                                $t1 = new DateTime($row['start_time']); $t2 = new DateTime($row['end_time']);
                                $diff = $t1->diff($t2);
                                
                                // เช็คสถานะตามเงื่อนไขที่ผู้ใช้ต้องการ
                                if ($now >= $row['start_time'] && $now <= $row['end_time']) {
                                    $st = "กำลังใช้งาน"; $cl = "badge-live"; $ic = "bi-play-circle-fill";
                                } elseif ($now < $row['start_time']) {
                                    $st = "จองล่วงหน้า"; $cl = "badge-upcoming"; $ic = "bi-calendar-check-fill";
                                } else {
                                    $st = "เสร็จสิ้น"; $cl = "badge-done"; $ic = "bi-check-circle-fill";
                                }
                            ?>
                            <tr>
                                <td><span class="fw-bold text-primary"><?= $row['room_name'] ?></span></td>
                                <td>
                                    <div class="fw-bold mb-0"><?= $row['objective'] ?></div>
                                    <small class="text-muted"><?= $row['requester_name'] ?></small>
                                </td>
                                <td>
                                    <div class="time-box shadow-sm">
                                        <div class="small fw-bold"><i class="bi bi-calendar-event me-1"></i> <?= date('d/m/y H:i', strtotime($row['start_time'])) ?></div>
                                        <div class="small fw-bold text-muted"><i class="bi bi-arrow-right-short"></i> <?= date('d/m/y H:i', strtotime($row['end_time'])) ?></div>
                                        <div class="text-primary small mt-1 fw-bold border-top pt-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-clock-history"></i> รวม: <?= $diff->h + ($diff->days * 24) ?> ชม. <?= $diff->i ?> น.
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill <?= $cl ?> animate__animated animate__pulse animate__infinite">
                                        <i class="<?= $ic ?>"></i> <?= $st ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link shadow-sm" href="?page=<?= $i ?>&f_room=<?= $filter_room ?>&search=<?= $search ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small">© 2026 PAKPOON SCHOOL SYSTEM | พัฒนาโดย คณะผู้จัดทำ</footer>

</body>
</html>