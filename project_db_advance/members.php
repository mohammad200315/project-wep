<?php
// members.php - صفحة الأعضاء الكاملة
session_start();
require_once 'db.php';

// رسائل النجاح والخطأ
$success_msg = '';
$error_msg = '';

// معالجة حذف عضو
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (delete_member($id)) {
        $success_msg = 'تم حذف العضو بنجاح!';
    } else {
        $error_msg = 'فشل حذف العضو!';
    }
}

// جلب جميع الأعضاء
$members_result = get_all_members();
$total_members = mysqli_num_rows($members_result);

// إحصائيات الأعضاء
$stats_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
    SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as on_leave,
    SUM(salary) as total_salary,
    AVG(salary) as avg_salary
    FROM members");
$stats = mysqli_fetch_assoc($stats_query);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأعضاء - نظام الميزانية</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'style.css'; ?>
        
        /* تنسيقات إضافية للأعضاء */
        .members-container {
            margin-top: 30px;
        }
        
        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .member-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .member-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .member-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: bold;
        }
        
        .member-info h3 {
            margin: 0;
            font-size: 18px;
            color: var(--text-primary);
        }
        
        .member-info p {
            margin: 5px 0 0;
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .member-details {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .detail-label {
            color: var(--text-secondary);
        }
        
        .detail-value {
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .role-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .role-admin {
            background: #dc3545;
            color: white;
        }
        
        .role-manager {
            background: #fd7e14;
            color: white;
        }
        
        .role-member {
            background: #20c997;
            color: white;
        }
        
        .role-viewer {
            background: #6c757d;
            color: white;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-on_leave {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .member-actions {
            display: flex;
            gap: 8px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .add-member-form {
            background: var(--white);
            border-radius: 16px;
            padding: 30px;
            margin-top: 30px;
            border: 1px solid var(--border-color);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
        }
        
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
                    <h1>إدارة الأعضاء</h1>
                    <p>إضافة وتعديل وحذف أعضاء الفريق</p>
                </div>
            </div>
            <div class="header-right">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-home"></i> الرئيسية
                </a>
                <button onclick="showAddMemberForm()" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> إضافة عضو جديد
                </button>
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

        <!-- إحصائيات الأعضاء -->
        <div class="budget-stats">
            <div class="stat-card stat-total">
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">إجمالي الأعضاء</div>
            </div>
            
            <div class="stat-card stat-spent">
                <div class="stat-value"><?php echo $stats['active']; ?></div>
                <div class="stat-label">أعضاء نشطين</div>
            </div>
            
            <div class="stat-card stat-remaining">
                <div class="stat-value"><?php echo $stats['inactive']; ?></div>
                <div class="stat-label">أعضاء غير نشطين</div>
            </div>
            
            <div class="stat-card stat-count">
                <div class="stat-value">$<?php echo number_format($stats['total_salary']); ?></div>
                <div class="stat-label">إجمالي الرواتب</div>
            </div>
        </div>

        <!-- نموذج إضافة عضو جديد (مخفي افتراضياً) -->
        <div id="addMemberForm" class="add-member-form" style="display: none;">
            <h3 style="margin-bottom: 20px;">إضافة عضو جديد</h3>
            <form action="add_member.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">الاسم الكامل *</label>
                        <input type="text" name="full_name" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني *</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="tel" name="phone" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">الوظيفة *</label>
                        <input type="text" name="position" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">القسم *</label>
                        <input type="text" name="department" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">الدور *</label>
                        <select name="role" class="form-input" required>
                            <option value="member">عضو</option>
                            <option value="manager">مدير</option>
                            <option value="admin">مدير نظام</option>
                            <option value="viewer">مشاهد</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">الراتب</label>
                        <input type="number" name="salary" class="form-input" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">تاريخ التعيين</label>
                        <input type="date" name="hire_date" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">الحالة *</label>
                        <select name="status" class="form-input" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                            <option value="on_leave">في إجازة</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-input" rows="3"></textarea>
                </div>
                
                <div style="margin-top: 30px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> حفظ العضو
                    </button>
                    <button type="button" onclick="hideAddMemberForm()" class="btn btn-secondary">
                        <i class="fa-solid fa-times"></i> إلغاء
                    </button>
                </div>
            </form>
        </div>

        <!-- قائمة الأعضاء -->
        <div class="content-card">
            <div class="table-header">
                <h2 class="table-title">قائمة الأعضاء</h2>
                <span class="text-muted"><?php echo $total_members; ?> عضو</span>
            </div>
            
            <?php if ($total_members > 0): ?>
                <div class="members-grid">
                    <?php while ($member = mysqli_fetch_assoc($members_result)): 
                        // الحرف الأول من الاسم للأفاتار
                        $initials = mb_substr($member['full_name'], 0, 1, 'UTF-8');
                    ?>
                    <div class="member-card">
                        <div class="member-header">
                            <div class="member-avatar">
                                <?php echo $initials; ?>
                            </div>
                            <div class="member-info">
                                <h3><?php echo htmlspecialchars($member['full_name']); ?></h3>
                                <p><?php echo htmlspecialchars($member['position']); ?></p>
                                <span class="role-badge role-<?php echo $member['role']; ?>">
                                    <?php 
                                    $role_names = [
                                        'admin' => 'مدير نظام',
                                        'manager' => 'مدير',
                                        'member' => 'عضو',
                                        'viewer' => 'مشاهد'
                                    ];
                                    echo $role_names[$member['role']] ?? $member['role'];
                                    ?>
                                </span>
                                <span class="status-badge status-<?php echo $member['status']; ?>" style="margin-right: 5px;">
                                    <?php 
                                    $status_names = [
                                        'active' => 'نشط',
                                        'inactive' => 'غير نشط',
                                        'on_leave' => 'في إجازة'
                                    ];
                                    echo $status_names[$member['status']] ?? $member['status'];
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="member-details">
                            <div class="detail-row">
                                <span class="detail-label">البريد الإلكتروني:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($member['email']); ?></span>
                            </div>
                            
                            <?php if (!empty($member['phone'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">رقم الهاتف:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($member['phone']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="detail-row">
                                <span class="detail-label">القسم:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($member['department']); ?></span>
                            </div>
                            
                            <?php if ($member['salary'] > 0): ?>
                            <div class="detail-row">
                                <span class="detail-label">الراتب:</span>
                                <span class="detail-value">$<?php echo number_format($member['salary'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($member['hire_date'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">تاريخ التعيين:</span>
                                <span class="detail-value"><?php echo date('Y/m/d', strtotime($member['hire_date'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="member-actions">
                            <a href="view_member.php?id=<?php echo $member['id']; ?>" class="btn btn-view btn-sm">
                                <i class="fa-solid fa-eye"></i> عرض
                            </a>
                            <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn btn-edit btn-sm">
                                <i class="fa-solid fa-edit"></i> تعديل
                            </a>
                            <a href="members.php?delete=1&id=<?php echo $member['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                                <i class="fa-solid fa-trash"></i> حذف
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-users"></i>
                    <h3>لا توجد أعضاء حالياً</h3>
                    <p style="margin-bottom: 20px;">لم يتم إضافة أي أعضاء حتى الآن.</p>
                    <button onclick="showAddMemberForm()" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus"></i> إضافة أول عضو
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showAddMemberForm() {
    document.getElementById('addMemberForm').style.display = 'block';
    window.scrollTo({ top: document.getElementById('addMemberForm').offsetTop - 100, behavior: 'smooth' });
}

function hideAddMemberForm() {
    document.getElementById('addMemberForm').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    // إخفاء رسائل التنبيه بعد 5 ثوانٍ
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
    
    // إظهار نموذج الإضافة إذا كان هناك خطأ في الرابط
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('add')) {
        showAddMemberForm();
    }
});
</script>
</body>
</html>
<?php close_connection(); ?>