<?php
// index.php - الصفحة الرئيسية
require_once 'db.php';

// إضافة بيانات تجريبية عند أول تشغيل
$check = mysqli_query($conn, "SELECT COUNT(*) as count FROM budgets");
$row = mysqli_fetch_assoc($check);
if ($row['count'] == 0) {
    mysqli_query($conn, "INSERT INTO budgets (title, description, total_amount, spent_amount, start_date, end_date, progress, status, tags) 
    VALUES ('Product Designer', 'مصمم واجهات مستخدم ومنتج', 520000, 312000, '2024-01-01', '2024-12-31', 60, 'active', 'UI/UX Designer, Visual Designer, +7 سنوات خبرة')");
    
    mysqli_query($conn, "INSERT INTO budgets (title, description, total_amount, spent_amount, start_date, end_date, progress, status, tags) 
    VALUES ('Frontend Developer', 'مطور واجهات أمامية', 450000, 225000, '2024-02-01', '2024-11-30', 50, 'active', 'React, Vue.js, JavaScript, TypeScript')");
    
    mysqli_query($conn, "INSERT INTO budgets (title, description, total_amount, spent_amount, start_date, end_date, progress, status, tags) 
    VALUES ('Marketing Campaign', 'حملة تسويقية ربع سنوية', 150000, 135000, '2024-03-01', '2024-06-30', 90, 'completed', 'Digital Marketing, Social Media, SEO')");
}

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
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الميزانية</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>إعدادات المنظمة</h2>
            <p>Organization Settings</p>
        </div>
        
        <nav class="nav-menu">
            <a href="index.php" class="nav-item active">
                <i class="fa-solid fa-gear"></i>
                <span>عام</span>
            </a>
            
            <a href="members.php" class="nav-item">
                <i class="fa-solid fa-users"></i>
                <span>الأعضاء</span>
            </a>
            
            <a href="experts.php" class="nav-item">
                <i class="fa-solid fa-briefcase"></i>
                <span>الخبراء</span>
            </a>
            
            <a href="budgets.php" class="nav-item">
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
                    <h1>لوحة التحكم الرئيسية</h1>
                    <p>مرحباً بك في نظام إدارة الميزانية</p>
                </div>
            </div>
            <div class="header-right">
                <a href="insert.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> إضافة ميزانية جديدة
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

        <!-- بطاقات الإحصائيات السريعة -->
        <div class="budget-stats" style="margin-bottom: 40px;">
            <?php
            // جلب إحصائيات سريعة
            $total_query = mysqli_query($conn, "SELECT 
                COUNT(*) as total_count,
                SUM(total_amount) as total_amount,
                SUM(spent_amount) as total_spent,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
                FROM budgets");
            $stats = mysqli_fetch_assoc($total_query);
            
            $total_budgets = $stats['total_count'] ?? 0;
            $total_amount = $stats['total_amount'] ?? 0;
            $total_spent = $stats['total_spent'] ?? 0;
            $active_budgets = $stats['active_count'] ?? 0;
            $remaining_amount = $total_amount - $total_spent;
            ?>
            
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

        <!-- المحتوى الرئيسي -->
        <div class="content-card">
            <div class="welcome-section">
                <h2>مرحباً بك في لوحة تحكم الميزانية</h2>
                <p class="text-muted" style="margin-bottom: 30px;">
                    نظام متكامل لإدارة الميزانيات وتتبع النفقات
                </p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                        <h3>إدارة الميزانيات</h3>
                        <p>إنشاء وتعديل وحذف الميزانيات بسهولة</p>
                        <a href="budgets.php" class="btn btn-secondary btn-sm">استعراض</a>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h3>تقارير وتحليلات</h3>
                        <p>متابعة الإحصائيات والتقارير الشاملة</p>
                        <a href="analytics.php" class="btn btn-secondary btn-sm">استعراض</a>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h3>إدارة الأعضاء</h3>
                        <p>إدارة صلاحيات أعضاء الفريق</p>
                        <a href="members.php" class="btn btn-secondary btn-sm">استعراض</a>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <h3>الخبراء والمستشارين</h3>
                        <p>إدارة قائمة الخبراء والمستشارين</p>
                        <a href="experts.php" class="btn btn-secondary btn-sm">استعراض</a>
                    </div>
                </div>
            </div>
            
            <div class="quick-actions-section" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border-color);">
                <h3 style="margin-bottom: 20px;">إجراءات سريعة</h3>
                <div class="quick-actions">
                    <a href="insert.php" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> إضافة ميزانية جديدة
                    </a>
                    <a href="budgets.php" class="btn btn-secondary">
                        <i class="fa-solid fa-list"></i> عرض جميع الميزانيات
                    </a>
                    <a href="analytics.php" class="btn btn-secondary">
                        <i class="fa-solid fa-chart-bar"></i> التقارير والتحليلات
                    </a>
                </div>
            </div>
        </div>

        <!-- آخر الميزانيات المضافة -->
        <?php
        $recent_budgets = mysqli_query($conn, "SELECT * FROM budgets ORDER BY created_at DESC LIMIT 3");
        if (mysqli_num_rows($recent_budgets) > 0):
        ?>
        <div class="content-card" style="margin-top: 30px;">
            <h3 style="margin-bottom: 20px;">آخر الميزانيات المضافة</h3>
            <div class="recent-budgets">
                <?php while ($budget = mysqli_fetch_assoc($recent_budgets)): 
                    $progress_color = $budget['progress'] >= 80 ? '#dc3545' : 
                                    ($budget['progress'] >= 60 ? '#f59e0b' : '#10b981');
                ?>
                <div class="recent-budget-item">
                    <div class="recent-budget-header">
                        <h4><?php echo htmlspecialchars($budget['title']); ?></h4>
                        <span class="budget-amount">$<?php echo number_format($budget['total_amount']); ?></span>
                    </div>
                    <div class="progress-container" style="margin: 10px 0;">
                        <div class="progress-info">
                            <span class="progress-percent" style="color: <?php echo $progress_color; ?>;">
                                <?php echo $budget['progress']; ?>%
                            </span>
                            <span>$<?php echo number_format($budget['spent_amount']); ?> منفق</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $budget['progress']; ?>%; background: <?php echo $progress_color; ?>;"></div>
                        </div>
                    </div>
                    <div class="recent-budget-footer">
                        <span class="text-muted"><?php echo date('Y/m/d', strtotime($budget['created_at'])); ?></span>
                        <div class="recent-actions">
                            <a href="view.php?id=<?php echo $budget['id']; ?>" class="btn-icon btn-view btn-sm">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="update.php?id=<?php echo $budget['id']; ?>" class="btn-icon btn-edit btn-sm">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="budgets.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> عرض جميع الميزانيات
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="js/script.js"></script>
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
});
</script>
</body>
</html>
<?php close_connection(); ?>