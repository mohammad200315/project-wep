<?php
// add_member.php - معالجة إضافة عضو جديد
session_start();
require_once 'db.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من البيانات المطلوبة
    $required_fields = ['full_name', 'email', 'position', 'department', 'role', 'status'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error = 'جميع الحقول المطلوبة يجب ملؤها: ' . implode(', ', $missing_fields);
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني غير صالح';
    } else {
        // إعداد البيانات
        $member_data = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'position' => $_POST['position'],
            'department' => $_POST['department'],
            'role' => $_POST['role'],
            'salary' => $_POST['salary'] ?? 0,
            'hire_date' => $_POST['hire_date'] ?? date('Y-m-d'),
            'status' => $_POST['status'],
            'notes' => $_POST['notes'] ?? ''
        ];
        
        // محاولة إضافة العضو
        if (add_member($member_data)) {
            $success = true;
        } else {
            $error = 'فشل إضافة العضو. قد يكون البريد الإلكتروني مستخدماً مسبقاً.';
        }
    }
}

if ($success) {
    header('Location: members.php?success=add');
    exit();
} else {
    // عرض صفحة الخطأ أو إعادة التوجيه مع رسالة خطأ
    $_SESSION['error'] = $error;
    $_SESSION['form_data'] = $_POST;
    header('Location: members.php?add=1&error=' . urlencode($error));
    exit();
}
?>