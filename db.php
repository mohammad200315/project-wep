<?php
// db.php - الملف الأساسي للاتصال
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$host = "localhost";
$username = "root";
$password = "";
$database = "project_db_advance";

$conn = mysqli_connect($host, $username, $password);

if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

// إنشاء القاعدة والجدول تلقائياً إذا لم يوجدا
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, $database);

// جدول الميزانيات
$table_sql = "CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    total_amount DECIMAL(15,2) DEFAULT 0.00,
    spent_amount DECIMAL(15,2) DEFAULT 0.00,
    start_date DATE,
    end_date DATE,
    progress INT DEFAULT 0,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    tags TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $table_sql);

// جدول الأعضاء
$members_table = "CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    position VARCHAR(100),
    department VARCHAR(100),
    role ENUM('admin', 'manager', 'member', 'viewer') DEFAULT 'member',
    salary DECIMAL(15,2) DEFAULT 0.00,
    hire_date DATE,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    profile_image VARCHAR(500),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $members_table);

// إضافة بيانات تجريبية للأعضاء إذا كان الجدول فارغاً
$check_members = mysqli_query($conn, "SELECT COUNT(*) as count FROM members");
$member_count = mysqli_fetch_assoc($check_members);
if ($member_count['count'] == 0) {
    mysqli_query($conn, "INSERT INTO members (full_name, email, phone, position, department, role, salary, hire_date, status) VALUES 
    ('أحمد محمد', 'ahmed@example.com', '0501234567', 'مدير الميزانية', 'المالية', 'admin', 25000, '2023-01-15', 'active'),
    ('سارة خالد', 'sara@example.com', '0507654321', 'محلل مالي', 'المالية', 'manager', 18000, '2023-03-10', 'active'),
    ('محمد علي', 'mohammed@example.com', '0501112233', 'مشرف مشاريع', 'المشاريع', 'member', 15000, '2023-05-20', 'active'),
    ('فاطمة عبدالله', 'fatima@example.com', '0504445566', 'مصممة واجهات', 'التصميم', 'member', 12000, '2023-07-05', 'active')
    ");
}

// دالة جلب جميع الأعضاء
function get_all_members() {
    global $conn;
    return mysqli_query($conn, "SELECT * FROM members ORDER BY created_at DESC");
}

// دالة جلب عضو محدد
function get_member_by_id($id) {
    global $conn;
    $id = intval($id);
    $result = mysqli_query($conn, "SELECT * FROM members WHERE id = $id");
    return mysqli_fetch_assoc($result);
}

// دالة إضافة عضو جديد
function add_member($data) {
    global $conn;
    
    $full_name = mysqli_real_escape_string($conn, $data['full_name']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $phone = mysqli_real_escape_string($conn, $data['phone']);
    $position = mysqli_real_escape_string($conn, $data['position']);
    $department = mysqli_real_escape_string($conn, $data['department']);
    $role = mysqli_real_escape_string($conn, $data['role']);
    $salary = floatval($data['salary']);
    $hire_date = mysqli_real_escape_string($conn, $data['hire_date']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    $notes = mysqli_real_escape_string($conn, $data['notes']);
    
    $sql = "INSERT INTO members (full_name, email, phone, position, department, role, salary, hire_date, status, notes)
            VALUES ('$full_name', '$email', '$phone', '$position', '$department', '$role', $salary, '$hire_date', '$status', '$notes')";
    
    return mysqli_query($conn, $sql);
}

// دالة تحديث بيانات عضو
function update_member($id, $data) {
    global $conn;
    $id = intval($id);
    
    $full_name = mysqli_real_escape_string($conn, $data['full_name']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $phone = mysqli_real_escape_string($conn, $data['phone']);
    $position = mysqli_real_escape_string($conn, $data['position']);
    $department = mysqli_real_escape_string($conn, $data['department']);
    $role = mysqli_real_escape_string($conn, $data['role']);
    $salary = floatval($data['salary']);
    $hire_date = mysqli_real_escape_string($conn, $data['hire_date']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    $notes = mysqli_real_escape_string($conn, $data['notes']);
    
    $sql = "UPDATE members SET 
            full_name = '$full_name',
            email = '$email',
            phone = '$phone',
            position = '$position',
            department = '$department',
            role = '$role',
            salary = $salary,
            hire_date = '$hire_date',
            status = '$status',
            notes = '$notes',
            updated_at = CURRENT_TIMESTAMP
            WHERE id = $id";
    
    return mysqli_query($conn, $sql);
}

// دالة حذف عضو
function delete_member($id) {
    global $conn;
    $id = intval($id);
    return mysqli_query($conn, "DELETE FROM members WHERE id = $id");
}

// الدوال القديمة للميزانيات
function get_all_budgets() {
    global $conn;
    return mysqli_query($conn, "SELECT * FROM budgets ORDER BY created_at DESC");
}

function delete_budget($id) {
    global $conn;
    $id = intval($id);
    return mysqli_query($conn, "DELETE FROM budgets WHERE id = $id");
}

function close_connection() {
    global $conn;
    mysqli_close($conn);
}
?>