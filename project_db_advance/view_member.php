<?php
// view_member.php - عرض تفاصيل العضو
session_start();
require_once 'db.php';

// جلب ID العضو
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header('Location: members.php?error=not_found');
    exit();
}

// جلب بيانات العضو
$member = get_member_by_id($id);

if (!$member) {
    header('Location: members.php?error=not_found');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل العضو - نظام الميزانية</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'style.css'; ?>
        
        .member-detail-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .member-profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
        }
        
        .profile-info h1 {
            margin: 0 0 10px;
            font-size: 32px;
        }
        
        .profile-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 18px;
        }
        
        .profile-badges {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .detail-sections {
            display: grid;
            gap: 30px;
        }
        
        .detail-section {
            background: var(--white);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid var(--border-color);
        }
        
        .section-title {
            font-size: 20px;
            margin: 0 0 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .member-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .empty-notes {
            color: var(--text-muted);
            font-style: italic;
            text-align: center;
            padding: 20px;
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
            
            <a href="members.php" class="nav-item active">
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
                    <h1>تفاصيل العضو</h1>
                    <p>معلومات كاملة عن العضو</p>
                </div>
            </div>
            <div class="header-right">
                <a href="members.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> العودة للأعضاء
                </a>
            </div>
        </div>
        
        <div class="member-detail-container">
            <!-- رأس الملف الشخصي -->
            <div class="member-profile-header">
                <?php 
                $initials = mb_substr($member['full_name'], 0, 1, 'UTF-8');
                ?>
                <div class="profile-avatar">
                    <?php echo $initials; ?>
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($member['full_name']); ?></h1>
                    <p><?php echo htmlspecialchars($member['position']); ?></p>
                    <div class="profile-badges">
                        <?php 
                        $role_names = [
                            'admin' => 'مدير نظام',
                            'manager' => 'مدير',
                            'member' => 'عضو',
                            'viewer' => 'مشاهد'
                        ];
                        
                        $status_names = [
                            'active' => 'نشط',
                            'inactive' => 'غير نشط',
                            'on_leave' => 'في إجازة'
                        ];
                        ?>
                        <span class="role-badge role-<?php echo $member['role']; ?>">
                            <?php echo $role_names[$member['role']] ?? $member['role']; ?>
                        </span>
                        <span class="status-badge status-<?php echo $member['status']; ?>">
                            <?php echo $status_names[$member['status']] ?? $member['status']; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- معلومات الاتصال -->
            <div class="detail-section">
                <h2 class="section-title">معلومات الاتصال</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">البريد الإلكتروني</span>
                        <span class="info-value"><?php echo htmlspecialchars($member['email']); ?></span>
                    </div>
                    
                    <?php if (!empty($member['phone'])): ?>
                    <div class="info-item">
                        <span class="info-label">رقم الهاتف</span>
                        <span class="info-value"><?php echo htmlspecialchars($member['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <span class="info-label">القسم</span>
                        <span class="info-value"><?php echo htmlspecialchars($member['department']); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- معلومات العمل -->
            <div class="detail-section">
                <h2 class="section-title">معلومات العمل</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">الوظيفة</span>
                        <span class="info-value"><?php echo htmlspecialchars($member['position']); ?></span>
                    </div>
                    
                    <?php if ($member['salary'] > 0): ?>
                    <div class="info-item">
                        <span class="info-label">الراتب الشهري</span>
                        <span class="info-value">$<?php echo number_format($member['salary'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($member['hire_date'])): ?>
                    <div class="info-item">
                        <span class="info-label">تاريخ التعيين</span>
                        <span class="info-value"><?php echo date('Y/m/d', strtotime($member['hire_date'])); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <span class="info-label">تاريخ الإضافة</span>
                        <span class="info-value"><?php echo date('Y/m/d', strtotime($member['created_at'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">آخر تحديث</span>
                        <span class="info-value"><?php echo date('Y/m/d', strtotime($member['updated_at'])); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- الملاحظات -->
            <div class="detail-section">
                <h2 class="section-title">الملاحظات</h2>
                <?php if (!empty($member['notes'])): ?>
                    <div style="white-space: pre-line; line-height: 1.6;">
                        <?php echo htmlspecialchars($member['notes']); ?>
                    </div>
                <?php else: ?>
                    <div class="empty-notes">لا توجد ملاحظات</div>
                <?php endif; ?>
            </div>
            
            <!-- أزرار الإجراءات -->
            <div class="member-actions">
                <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn btn-edit">
                    <i class="fa-solid fa-edit"></i> تعديل العضو
                </a>
                <a href="members.php?delete=1&id=<?php echo $member['id']; ?>" 
                   class="btn btn-danger"
                   onclick="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                    <i class="fa-solid fa-trash"></i> حذف العضو
                </a>
                <a href="members.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php close_connection(); ?>