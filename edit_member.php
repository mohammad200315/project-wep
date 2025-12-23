<?php
// edit_member.php - تعديل بيانات العضو
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

$error = '';
$success = '';

// معالجة النموذج عند الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $member_data = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'position' => $_POST['position'],
            'department' => $_POST['department'],
            'role' => $_POST['role'],
            'salary' => $_POST['salary'] ?? 0,
            'hire_date' => $_POST['hire_date'] ?? '',
            'status' => $_POST['status'],
            'notes' => $_POST['notes'] ?? ''
        ];
        
        if (update_member($id, $member_data)) {
            $success = 'تم تحديث بيانات العضو بنجاح!';
            // تحديث بيانات العضو المحلية
            $member = array_merge($member, $member_data);
        } else {
            $error = 'فشل تحديث بيانات العضو. قد يكون البريد الإلكتروني مستخدماً مسبقاً.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل العضو - نظام الميزانية</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'style.css'; ?>
        
        .edit-member-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .form-container {
            background: var(--white);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid var(--border-color);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-label.required::after {
            content: " *";
            color: var(--progress-red);
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            background: var(--white);
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(0, 122, 90, 0.1);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .member-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .member-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }
        
        .member-header h2 {
            margin: 0;
            font-size: 24px;
        }
        
        .member-header p {
            margin: 5px 0 0;
            color: var(--text-secondary);
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
                    <h1>تعديل بيانات العضو</h1>
                    <p>تعديل معلومات العضو <?php echo htmlspecialchars($member['full_name']); ?></p>
                </div>
            </div>
            <div class="header-right">
                <a href="view_member.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                    <i class="fa-solid fa-eye"></i> عرض التفاصيل
                </a>
                <a href="members.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> العودة للقائمة
                </a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="edit-member-container">
            <div class="member-header">
                <?php 
                $initials = mb_substr($member['full_name'], 0, 1, 'UTF-8');
                ?>
                <div class="member-avatar">
                    <?php echo $initials; ?>
                </div>
                <div>
                    <h2><?php echo htmlspecialchars($member['full_name']); ?></h2>
                    <p><?php echo htmlspecialchars($member['position']); ?></p>
                </div>
            </div>
            
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-grid">
                        <!-- المعلومات الشخصية -->
                        <div class="form-group">
                            <label class="form-label required">الاسم الكامل</label>
                            <input type="text" name="full_name" class="form-input" 
                                   value="<?php echo htmlspecialchars($member['full_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-input" 
                                   value="<?php echo htmlspecialchars($member['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="tel" name="phone" class="form-input" 
                                   value="<?php echo htmlspecialchars($member['phone']); ?>">
                        </div>
                        
                        <!-- معلومات العمل -->
                        <div class="form-group">
                            <label class="form-label required">الوظيفة</label>
                            <input type="text" name="position" class="form-input" 
                                   value="<?php echo htmlspecialchars($member['position']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">القسم</label>
                            <input type="text" name="department" class="form-input" 
                                   value="<?php echo htmlspecialchars($member['department']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">الدور</label>
                            <select name="role" class="form-select" required>
                                <option value="member" <?php echo $member['role'] == 'member' ? 'selected' : ''; ?>>عضو</option>
                                <option value="manager" <?php echo $member['role'] == 'manager' ? 'selected' : ''; ?>>مدير</option>
                                <option value="admin" <?php echo $member['role'] == 'admin' ? 'selected' : ''; ?>>مدير نظام</option>
                                <option value="viewer" <?php echo $member['role'] == 'viewer' ? 'selected' : ''; ?>>مشاهد</option>
                            </select>
                        </div>
                        
                        <!-- الراتب والتواريخ -->
                        <div class="form-group">
                            <label class="form-label">الراتب الشهري ($)</label>
                            <input type="number" name="salary" class="form-input" step="0.01" min="0"
                                   value="<?php echo $member['salary']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">تاريخ التعيين</label>
                            <input type="date" name="hire_date" class="form-input" 
                                   value="<?php echo $member['hire_date']; ?>">
                        </div>
                        
                        <!-- الحالة -->
                        <div class="form-group">
                            <label class="form-label required">الحالة</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?php echo $member['status'] == 'active' ? 'selected' : ''; ?>>نشط</option>
                                <option value="inactive" <?php echo $member['status'] == 'inactive' ? 'selected' : ''; ?>>غير نشط</option>
                                <option value="on_leave" <?php echo $member['status'] == 'on_leave' ? 'selected' : ''; ?>>في إجازة</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- الملاحظات -->
                    <div class="form-group">
                        <label class="form-label">الملاحظات</label>
                        <textarea name="notes" class="form-textarea"><?php echo htmlspecialchars($member['notes']); ?></textarea>
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> حفظ التغييرات
                        </button>
                        <a href="view_member.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                            <i class="fa-solid fa-times"></i> إلغاء
                        </a>
                        <a href="members.php?delete=1&id=<?php echo $id; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                            <i class="fa-solid fa-trash"></i> حذف العضو
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
});
</script>
</body>
</html>
<?php close_connection(); ?>