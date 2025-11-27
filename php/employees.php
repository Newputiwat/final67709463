<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once 'condb.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET - ดึงข้อมูลพนักงาน
if ($method === 'GET') {
    try {
        $stmt = $conn->prepare("SELECT * FROM employees ORDER BY emp_id ASC");
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $employees
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// POST - สำหรับ update และ delete
else if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // DELETE
    if ($action === 'delete') {
        try {
            $emp_id = $_POST['emp_id'];
            
            // ลบรูปภาพ
            $stmt = $conn->prepare("SELECT image FROM employees WHERE emp_id = ?");
            $stmt->execute([$emp_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && !empty($row['image'])) {
                $image_path = "uploads/" . $row['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // ลบข้อมูล
            $stmt = $conn->prepare("DELETE FROM employees WHERE emp_id = ?");
            $stmt->execute([$emp_id]);
            
            echo json_encode(['message' => 'ลบพนักงานสำเร็จ']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
    
    // UPDATE
    else if ($action === 'update') {
        try {
            $emp_id = $_POST['emp_id'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            
            // จัดการรูปภาพ
            $image = $_POST['old_image'] ?? '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $target_dir = "uploads/";
                
                // ลบรูปเก่า
                if (!empty($_POST['old_image'])) {
                    $old_image_path = $target_dir . $_POST['old_image'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                
                // อัปโหลดรูปใหม่
                $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $image = uniqid() . '_' . time() . '.' . $file_extension;
                $target_file = $target_dir . $image;
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            }
            
            if ($image) {
                $sql = "UPDATE employees SET first_name = ?, last_name = ?, address = ?, phone = ?, image = ? WHERE emp_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$first_name, $last_name, $address, $phone, $image, $emp_id]);
            } else {
                $sql = "UPDATE employees SET first_name = ?, last_name = ?, address = ?, phone = ? WHERE emp_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$first_name, $last_name, $address, $phone, $emp_id]);
            }
            
            echo json_encode(['message' => 'อัปเดตพนักงานสำเร็จ']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>