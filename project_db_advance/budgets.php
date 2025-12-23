<?php
// budgets.php - صفحة عرض جميع الميزانيات
session_start();
require_once 'db.php';

// جلب جميع الميزانيات
$budgets_result = get_all_budgets();

// رسائل النجاح والخطأ
$success_msg = '';
$error_msg = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'insert':
            $success_msg = 'تم إضافة الميزانية الجديدة بنجاح!';
            break;
        case 'update':
            $success_msg = 'تم تحديث الميزانية بنجاح!';
            break;
        case 'delete':
            $success_msg = 'تم حذف الميزانية بنجاح!';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'delete_failed':
            $error_msg = 'فشل حذف الميزانية!';
            break;
        case 'not_found':
            $error_msg = 'الميزانية المطلوبة غير موجودة!';
            break;
    }
}

// حساب الإحصائيات
$total_amount = 0;
$total_spent = 0;
$total_budgets = 0;
$active_budgets = 0;
$completed_budgets = 0;

// استعلام للإحصائيات
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(total_amount) as total_amount,
    SUM(spent_amount) as total_spent,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
    FROM budgets";
    
$stats_result = mysqli_query($conn, $stats_sql);
if ($stats_result && mysqli_num_rows($stats_result) > 0) {
    $stats = mysqli_fetch_assoc($stats_result);
    $total_budgets = $stats['total'] ?? 0;
    $total_amount = $stats['total_amount'] ?? 0;
    $total_spent = $stats['total_spent'] ?? 0;
    $active_budgets = $stats['active'] ?? 0;
    $completed_budgets = $stats['completed'] ?? 0;
}

$remaining_amount = $total_amount - $total_spent;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الميزانيات - نظام الميزانية</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #f9fafb;
            --white: #ffffff;
            --sidebar-bg: #ffffff;
            --primary-green: #007a5a;
            --dark-green: #00674d;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border-color: #e5e7eb;
            --light-border: #f3f4f6;
            --progress-green: #10b981;
            --progress-orange: #f59e0b;
            --progress-red: #dc3545;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: var(--primary-bg);
            color: var(--text-primary);
            min-height: 100vh;
        }
        
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* الشريط الجانبي */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            border-left: 1px solid var(--light-border);
            padding: 32px 24px;
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }
        
        .sidebar-header {
            margin-bottom: 40px;
        }
        
        .sidebar-header h2 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        
        .sidebar-header p {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            color: var(--text-secondary);
            border-radius: 12px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 15px;
        }
        
        .nav-item i {
            margin-left: 12px;
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .nav-item.active {
            background: var(--white);
            color: var(--text-primary);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
        
        .nav-item:hover:not(.active) {
            background: #f8f9fa;
            color: var(--text-primary);
        }
        
        /* المحتوى الرئيسي */
        .main-content {
            flex: 1;
            margin-right: 280px;
            padding: 40px;
            max-width: 1400px;
        }
        
        /* رأس الصفحة */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .page-title h1 {
            font-size: 30px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        
        .page-title p {
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            outline: none;
        }
        
        .btn-primary {
            background: var(--primary-green);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--dark-green);
        }
        
        .btn-secondary {
            background: var(--white);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        .btn-secondary:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }
        
        .btn-edit {
            background: var(--progress-orange);
            color: #212529;
        }
        
        .btn-edit:hover {
            background: #e0a800;
        }
        
        .btn-view {
            background: #3b82f6;
            color: white;
        }
        
        .btn-view:hover {
            background: #2563eb;
        }
        
        .btn-danger {
            background: var(--progress-red);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        /* رسائل التنبيه */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        
        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        
        /* ترويسة الميزانية */
        .budget-header {
            background: var(--white);
            border-radius: 24px;
            padding: 32px;
            border: 1px solid var(--border-color);
            margin-bottom: 32px;
        }
        
        .budget-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 24px;
        }
        
        @media (max-width: 1024px) {
            .budget-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 640px) {
            .budget-stats {
                grid-template-columns: 1fr;
            }
        }
        
        .stat-card {
            padding: 24px;
            border-radius: 16px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .stat-total {
            background: linear-gradient(135deg, #10b98115, #10b98105);
            border: 1px solid #10b98130;
        }
        
        .stat-spent {
            background: linear-gradient(135deg, #3b82f615, #3b82f605);
            border: 1px solid #3b82f630;
        }
        
        .stat-remaining {
            background: linear-gradient(135deg, #f59e0b15, #f59e0b05);
            border: 1px solid #f59e0b30;
        }
        
        .stat-count {
            background: linear-gradient(135deg, #8b5cf615, #8b5cf605);
            border: 1px solid #8b5cf630;
        }
        
        /* جدول الميزانيات */
        .budgets-table-container {
            background: var(--white);
            border-radius: 24px;
            padding: 32px;
            border: 1px solid var(--border-color);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .table-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .budgets-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .budgets-table th {
            text-align: right;
            padding: 16px 20px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
            background: var(--primary-bg);
        }
        
        .budgets-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
        }
        
        .budgets-table tr:last-child td {
            border-bottom: none;
        }
        
        .budgets-table tr:hover {
            background: #f9fafb;
        }
        
        .budget-title {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .budget-dates {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .budget-amount {
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .text-muted {
            color: var(--text-muted);
        }
        
        /* شريط التقدم */
        .progress-container {
            width: 100%;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .progress-bar {
            height: 8px;
            background: var(--light-border);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 10px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-completed {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .table-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-icon {
            padding: 8px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            width: 36px;
            height: 36px;
        }
        
        /* حالة فارغة */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        /* فلتر البحث */
        .filter-container {
            background: var(--white);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        .filter-input, .filter-select {
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
        }
        
        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--primary-green);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .app-container {
                flex-direction: column;
            }
            
            .sidebar {
                position: static;
                width: 100%;
                border-left: none;
                border-bottom: 1px solid var(--light-border);
            }
            
            .main-content {
                margin-right: 0;
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .header-left, .header-right {
                width: 100%;
            }
            
            .budgets-table {
                display: block;
                overflow-x: auto;
            }
            
            .table-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>إدارة المنظمة</h2>
            <p>Organization Management</p>
        </div>
        
        <nav class="nav-menu">
            <a href="index.php" class="nav-item">
                <i class="fa-solid fa-gear"></i>
                <span>الرئيسية</span>
            </a>
            
            <a href="members.php" class="nav-item">
                <i class="fa-solid fa-users"></i>
                <span>الأعضاء</span>
            </a>
            
            <a href="experts.php" class="nav-item">
                <i class="fa-solid fa-briefcase"></i>
                <span>الخبراء</span>
            </a>
            
            <a href="budgets.php" class="nav-item active">
                <i class="fa-solid fa-dollar-sign"></i>
                <span>الميزانية</span>
            </a>
            
            <a href="analytics.php" class="nav-item">
                <i class="fa-solid fa-chart-line"></i>
                <span>التحليلات</span>
            </a>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-left">
                <div class="page-title">
                    <h1>إدارة الميزانيات</h1>
                    <p>عرض وإدارة جميع الميزانيات</p>
                </div>
            </div>
            <div class="header-right">
                <a href="insert.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> إضافة ميزانية جديدة
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-home"></i> الرئيسية
                </a>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i>
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- فلتر البحث -->
        <div class="filter-container">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label class="filter-label">بحث بالعنوان</label>
                    <input type="text" name="search" class="filter-input" placeholder="ابحث عن ميزانية..." value="<?php echo $_GET['search'] ?? ''; ?>">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">الحالة</label>
                    <select name="status" class="filter-select">
                        <option value="">جميع الحالات</option>
                        <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : ''; ?>>نشط</option>
                        <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>مكتمل</option>
                        <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>ملغي</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">فرز حسب</label>
                    <select name="sort" class="filter-select">
                        <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>الأحدث أولاً</option>
                        <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>الأقدم أولاً</option>
                        <option value="amount_high" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'amount_high') ? 'selected' : ''; ?>>الأعلى مبلغاً</option>
                        <option value="amount_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'amount_low') ? 'selected' : ''; ?>>الأقل مبلغاً</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <button type="submit" class="btn btn-primary" style="height: 42px;">
                        <i class="fa-solid fa-filter"></i> تصفية
                    </button>
                    <a href="budgets.php" class="btn btn-secondary" style="height: 42px; margin-top: 5px;">
                        <i class="fa-solid fa-times"></i> إعادة الضبط
                    </a>
                </div>
            </form>
        </div>

        <!-- ترويسة الميزانية -->
        <div class="budget-header">
            <h2 class="table-title">نظرة عامة</h2>
            <div class="budget-stats">
                <div class="stat-card stat-total">
                    <div class="stat-value">$<?php echo number_format($total_amount); ?></div>
                    <div class="stat-label">إجمالي الميزانية</div>
                </div>
                
                <div class="stat-card stat-spent">
                    <div class="stat-value">$<?php echo number_format($total_spent); ?></div>
                    <div class="stat-label">إجمالي المنفق</div>
                </div>
                
                <div class="stat-card stat-remaining">
                    <div class="stat-value">$<?php echo number_format($remaining_amount); ?></div>
                    <div class="stat-label">المبلغ المتبقي</div>
                </div>
                
                <div class="stat-card stat-count">
                    <div class="stat-value"><?php echo $total_budgets; ?></div>
                    <div class="stat-label">عدد الميزانيات</div>
                </div>
            </div>
        </div>

        <!-- جدول الميزانيات -->
        <div class="budgets-table-container">
            <div class="table-header">
                <h2 class="table-title">جميع الميزانيات</h2>
                <span class="text-muted"><?php echo $total_budgets; ?> ميزانية</span>
            </div>
            
            <?php 
            // تطبيق الفلتر على الاستعلام
            $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
            $status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
            
            $where_conditions = [];
            if (!empty($search)) {
                $where_conditions[] = "(title LIKE '%$search%' OR description LIKE '%$search%' OR tags LIKE '%$search%')";
            }
            if (!empty($status_filter)) {
                $where_conditions[] = "status = '$status_filter'";
            }
            
            $where_clause = '';
            if (!empty($where_conditions)) {
                $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
            }
            
            $order_by = '';
            switch ($sort) {
                case 'oldest':
                    $order_by = 'ORDER BY created_at ASC';
                    break;
                case 'amount_high':
                    $order_by = 'ORDER BY total_amount DESC';
                    break;
                case 'amount_low':
                    $order_by = 'ORDER BY total_amount ASC';
                    break;
                default:
                    $order_by = 'ORDER BY created_at DESC';
            }
            
            $filtered_sql = "SELECT * FROM budgets $where_clause $order_by";
            $filtered_result = mysqli_query($conn, $filtered_sql);
            $filtered_count = mysqli_num_rows($filtered_result);
            ?>
            
            <?php if ($filtered_count > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="budgets-table">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>المبلغ الإجمالي</th>
                                <th>المبلغ المنفق</th>
                                <th>التقدم</th>
                                <th>الحالة</th>
                                <th>التواريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($budget = mysqli_fetch_assoc($filtered_result)): 
                                $progress_color = $budget['progress'] >= 80 ? '#dc3545' : 
                                                ($budget['progress'] >= 60 ? '#f59e0b' : '#10b981');
                            ?>
                            <tr>
                                <td>
                                    <div class="budget-title"><?php echo htmlspecialchars($budget['title']); ?></div>
                                    <?php if (!empty($budget['description'])): ?>
                                    <div class="text-muted" style="font-size: 13px; margin-top: 4px;">
                                        <?php echo substr(htmlspecialchars($budget['description']), 0, 50); ?>...
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="budget-amount">$<?php echo number_format($budget['total_amount']); ?></div>
                                </td>
                                <td>
                                    <div class="budget-amount">$<?php echo number_format($budget['spent_amount']); ?></div>
                                </td>
                                <td style="width: 150px;">
                                    <div class="progress-container">
                                        <div class="progress-info">
                                            <span class="progress-percent" style="color: <?php echo $progress_color; ?>;">
                                                <?php echo $budget['progress']; ?>%
                                            </span>
                                            <span>$<?php echo number_format($budget['total_amount'] - $budget['spent_amount']); ?></span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $budget['progress']; ?>%; background: <?php echo $progress_color; ?>;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $status_text = [
                                        'active' => 'نشط',
                                        'completed' => 'مكتمل',
                                        'cancelled' => 'ملغي'
                                    ];
                                    $status_class = 'status-' . $budget['status'];
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text[$budget['status']] ?? $budget['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="budget-dates">
                                        <?php echo date('Y/m/d', strtotime($budget['start_date'])); ?> - 
                                        <?php echo date('Y/m/d', strtotime($budget['end_date'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="view.php?id=<?php echo $budget['id']; ?>" class="btn-icon btn-view" title="عرض">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="update.php?id=<?php echo $budget['id']; ?>" class="btn-icon btn-edit" title="تعديل">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $budget['id']; ?>" 
                                           class="btn-icon btn-secondary" 
                                           title="حذف"
                                           onclick="return confirm('هل أنت متأكد من حذف هذه الميزانية؟')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <h3>لا توجد ميزانيات</h3>
                    <p style="margin-bottom: 20px;">
                        <?php if (!empty($search) || !empty($status_filter)): ?>
                            لم يتم العثور على ميزانيات تطابق معايير البحث الخاصة بك.
                        <?php else: ?>
                            لم يتم إضافة أي ميزانيات حتى الآن.
                        <?php endif; ?>
                    </p>
                    <a href="insert.php" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> إضافة أول ميزانية
                    </a>
                    <?php if (!empty($search) || !empty($status_filter)): ?>
                    <a href="budgets.php" class="btn btn-secondary" style="margin-right: 10px;">
                        <i class="fa-solid fa-times"></i> إعادة الضبط
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- ملخص المعلومات -->
        <div class="content-card" style="margin-top: 30px;">
            <h3 style="margin-bottom: 20px;">ملخص الميزانيات</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <h4 style="color: var(--text-secondary); font-size: 14px; margin-bottom: 10px;">حالة الميزانيات</h4>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>نشطة</span>
                            <span style="font-weight: 600;"><?php echo $active_budgets; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>مكتملة</span>
                            <span style="font-weight: 600;"><?php echo $completed_budgets; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>معدل الإنجاز</span>
                            <span style="font-weight: 600;"><?php echo $total_amount > 0 ? round(($total_spent / $total_amount) * 100, 2) : 0; ?>%</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 style="color: var(--text-secondary); font-size: 14px; margin-bottom: 10px;">أسرع الطرق</h4>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="insert.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> إضافة ميزانية جديدة
                        </a>
                        <a href="analytics.php" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-chart-bar"></i> عرض التقارير
                        </a>
                        <a href="index.php" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-home"></i> العودة للرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // إخفاء رسائل التنبيه تلقائياً بعد 5 ثوانٍ
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000);
    });
    
    // تأكيد الحذف
    const deleteLinks = document.querySelectorAll('a[href*="delete.php"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد من حذف هذه الميزانية؟ لا يمكن التراجع عن هذا الإجراء.')) {
                e.preventDefault();
            }
        });
    });
    
    // تحسين تجربة البحث
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        // التركيز على حقل البحث إذا كان به قيمة
        if (searchInput.value.trim() !== '') {
            searchInput.focus();
            searchInput.select();
        }
    }
});
</script>
</body>
</html>
<?php close_connection(); ?>