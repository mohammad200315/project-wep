<?php
// delete.php - معالجة طلب الحذف
session_start();
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if ($id > 0) {
        $result = delete_budget($id);
        
        if ($result) {
            // العودة للرئيسية مع رسالة نجاح
            header("Location: budgets.php?success=delete");
            exit();
        } else {
            header("Location: budgets.php?error=delete_failed");
            exit();
        }
    } else {
        header("Location: budgets.php?error=not_found");
        exit();
    }
} else {
    header("Location: budgets.php");
    exit();
}

close_connection();
?>