<?php include 'db.php'; 

// 1. ระบบลบห้อง
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM rooms WHERE room_id = $id");
    header("Location: manage_rooms.php");
}

// 2. ระบบเพิ่มห้อง
if(isset($_POST['add_room'])){
    $name = mysqli_real_escape_string($conn, $_POST['room_name']);
    $cap = intval($_POST['capacity']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $conn->query("INSERT INTO rooms (room_name, capacity, location) VALUES ('$name', $cap, '$loc')");
    header("Location: manage_rooms.php");
}

// 3. ระบบแก้ไขห้อง
if(isset($_POST['update_room'])){
    $id = intval($_POST['room_id']);
    $name = mysqli_real_escape_string($conn, $_POST['room_name']);
    $cap = intval($_POST['capacity']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $conn->query("UPDATE rooms SET room_name='$name', capacity=$cap, location='$loc' WHERE room_id=$id");
    header("Location: manage_rooms.php");
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Pakpoon School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Sarabun:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary: #4361ee; --bg: #f8f9fc; }
        body { background-color: var(--bg); font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif; color: #2b2d42; }
        
        /* Navbar Style ถอดแบบมาจากหน้า index */
        .navbar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .form-control { border-radius: 0.75rem; padding: 0.7rem; border: 1px solid #dee2e6; }
        .btn-primary { background: var(--primary); border: none; border-radius: 0.75rem; padding: 0.8rem; font-weight: 600; }
        .btn-action { border-radius: 0.5rem; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border: none; }
    </style>
</head>
<body>

<nav class="navbar sticky-top bg-white border-bottom py-3 mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="bi bi-building-fill-check me-2"></i>PAKPOON BOOKING
        </a>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3 border">
                <i class="bi bi-house-door"></i> หน้าหลัก
            </a>
            <a href="manage_rooms.php" class="btn btn-primary btn-sm rounded-pill px-3 text-white">
                <i class="bi bi-gear-fill"></i> จัดการห้อง
            </a>
            <a href="dashboard.php" class="btn btn-light btn-sm rounded-pill px-3 border">
                <i class="bi bi-pie-chart"></i> สถิติ
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card p-4 animate__animated animate__fadeInLeft shadow-sm">
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-plus-square-fill me-2"></i>เพิ่มห้องประชุมใหม่</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ชื่อห้องประชุม</label>
                        <input type="text" name="room_name" class="form-control" placeholder="ชื่อห้อง" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ความจุ (ที่นั่ง)</label>
                        <input type="number" name="capacity" class="form-control" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">สถานที่ / ชั้น</label>
                        <input type="text" name="location" class="form-control" placeholder="อาคาร/ชั้น">
                    </div>
                    <button type="submit" name="add_room" class="btn btn-primary w-100 shadow-sm">บันทึกข้อมูลห้อง</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card p-4 animate__animated animate__fadeInRight shadow-sm">
                <h5 class="fw-bold mb-4"><i class="bi bi-list-stars me-2 text-primary"></i>รายการห้องทั้งหมด</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th>ชื่อห้อง</th>
                                <th>ความจุ</th>
                                <th>สถานที่</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rooms = $conn->query("SELECT * FROM rooms ORDER BY room_id DESC");
                            while($r = $rooms->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?= $r['room_name'] ?></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-normal rounded-pill"><?= $r['capacity'] ?> ที่นั่ง</span></td>
                                <td class="text-muted small"><?= $r['location'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-light text-warning btn-action shadow-sm me-1" 
                                            onclick="editRoom(<?= $r['room_id'] ?>, '<?= $r['room_name'] ?>', <?= $r['capacity'] ?>, '<?= $r['location'] ?>')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="?delete=<?= $r['room_id'] ?>" class="btn btn-light text-danger btn-action shadow-sm" 
                                       onclick="return confirm('ยืนยันการลบห้อง?')">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-0">
                <h5 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>แก้ไขห้องประชุม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pb-4">
                <input type="hidden" name="room_id" id="edit_room_id">
                <div class="mb-3">
                    <label class="form-label small fw-bold">ชื่อห้องประชุม</label>
                    <input type="text" name="room_name" id="edit_room_name" class="form-control" required>
                </div>
                <div class="mb-2 row">
                    <div class="col-6">
                        <label class="form-label small fw-bold">ความจุ</label>
                        <input type="number" name="capacity" id="edit_capacity" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">สถานที่</label>
                        <input type="text" name="location" id="edit_location" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" name="update_room" class="btn btn-primary rounded-pill px-4">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editRoom(id, name, cap, loc) {
    document.getElementById('edit_room_id').value = id;
    document.getElementById('edit_room_name').value = name;
    document.getElementById('edit_capacity').value = cap;
    document.getElementById('edit_location').value = loc;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

</body>
</html>