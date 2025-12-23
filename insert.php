<?php
// insert.php - إضافة ميزانية جديدة
session_start();
require_once 'db.php';

$error = '';

// معالجة النموذج عند الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // تنظيف المدخلات
    $title = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date'] ?? '');
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date'] ?? '');
    $tags = mysqli_real_escape_string($conn, $_POST['tags'] ?? '');
    
    // التحقق من البيانات
    if (empty($title) || $total_amount <= 0 || empty($start_date) || empty($end_date)) {
        $error = 'جميع الحقول المطلوبة يجب ملؤها';
    } elseif ($end_date < $start_date) {
        $error = 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء';
    } else {
        // حساب القيم
        $spent_amount = 0;
        $progress = 0;
        $status = 'active';
        
        // إضافة الميزانية
        $sql = "INSERT INTO budgets (title, description, total_amount, spent_amount, 
                start_date, end_date, progress, status, tags) 
                VALUES ('$title', '$description', $total_amount, $spent_amount,
                '$start_date', '$end_date', $progress, '$status', '$tags')";
        
        if (mysqli_query($conn, $sql)) {
            // إعادة التوجيه مع رسالة نجاح
            header('Location: budgets.php?success=insert');
            exit();
        } else {
            $error = 'فشل إضافة الميزانية: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة ميزانية جديدة - نظام الميزانية</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'style.css'; ?>
        
        .form-page-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-container {
            background: var(--white);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid var(--border-color);
        }
        
        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .form-description {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        
        .form-label.required::after {
            content: " *";
            color: var(--progress-red);
        }
        
        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--white);
            transition: border 0.2s;
        }
        
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(0, 122, 90, 0.1);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }
        
        .date-info {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-container {
                padding: 25px;
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
                    <h1>إضافة ميزانية جديدة</h1>
                    <p>املأ النموذج لإضافة ميزانية جديدة</p>
                </div>
            </div>
            <div class="header-right">
                <a href="budgets.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> العودة للميزانيات
                </a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="form-page-container">
            <div class="form-container">
                <h2 class="form-title">معلومات الميزانية</h2>
                <p class="form-description">املأ جميع الحقول المطلوبة لإضافة ميزانية جديدة</p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="title" class="form-label required">عنوان الميزانية</label>
                        <input type="text" id="title" name="title" class="form-input" required 
                               placeholder="مثال: مصمم منتجات (Product Designer)" 
                               value="<?php echo $_POST['title'] ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea id="description" name="description" class="form-textarea" 
                                  placeholder="وصف تفصيلي للميزانية..."><?php echo $_POST['description'] ?? ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="total_amount" class="form-label required">المبلغ الإجمالي ($)</label>
                        <input type="number" id="total_amount" name="total_amount" class="form-input" 
                               min="0" step="0.01" required 
                               placeholder="مثال: 520000" 
                               value="<?php echo $_POST['total_amount'] ?? ''; ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date" class="form-label required">تاريخ البدء</label>
                            <input type="date" id="start_date" name="start_date" class="form-input" required 
                                   value="<?php echo $_POST['start_date'] ?? date('Y-m-d'); ?>">
                            <div class="date-info">تاريخ بدء الميزانية</div>
                        </div>
                        <div class="form-group">
                            <label for="end_date" class="form-label required">تاريخ الانتهاء</label>
                            <input type="date" id="end_date" name="end_date" class="form-input" required 
                                   value="<?php echo $_POST['end_date'] ?? date('Y-m-d', strtotime('+1 year')); ?>">
                            <div class="date-info">تاريخ انتهاء الميزانية</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tags" class="form-label">الوسوم (مفصولة بفواصل)</label>
                        <input type="text" id="tags" name="tags" class="form-input" 
                               placeholder="مثال: UI/UX Designer, Visual Designer, +7 سنوات خبرة"
                               value="<?php echo $_POST['tags'] ?? ''; ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> حفظ الميزانية
                        </button>
                        <a href="budgets.php" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const totalAmount = document.getElementById('total_amount');
    
    // تعيين القيم الافتراضية إذا كانت فارغة
    if (!startDate.value) {
        startDate.value = new Date().toISOString().split('T')[0];
    }
    
    if (!endDate.value) {
        const nextYear = new Date();
        nextYear.setFullYear(nextYear.getFullYear() + 1);
        endDate.value = nextYear.toISOString().split('T')[0];
    }
    
    // التحقق من التواريخ
    startDate.addEventListener('change', function() {
        if (this.value > endDate.value) {
            endDate.value = this.value;
        }
    });
    
    endDate.addEventListener('change', function() {
        if (this.value < startDate.value) {
            alert('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
            this.value = startDate.value;
        }
    });
    
    // التحقق من المبلغ
    totalAmount.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value < 0) {
            this.value = 0;
        }
    });
    
    // التركيز على أول حقل
    document.getElementById('title').focus();
});
</script>
</body>
</html>
<?php close_connection(); ?>