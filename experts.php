<?php
// experts.php - صفحة الخبراء
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الخبراء - إدارة الميزانية</title>
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
            max-width: 1200px;
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
        
        /* كرت المحتوى */
        .content-card {
            background: var(--white);
            border-radius: 24px;
            padding: 32px;
            border: 1px solid var(--border-color);
        }
        
        /* التنبيهات */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-info {
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            color: #1e40af;
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
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>إعدادات المنظمة</h2>
            <p>Organization Settings</p>
        </div>
        
        <nav class="nav-menu">
            <a href="index.php" class="nav-item">
                <i class="fa-solid fa-gear"></i>
                <span>عام</span>
            </a>
            
            <a href="members.php" class="nav-item">
                <i class="fa-solid fa-users"></i>
                <span>الأعضاء</span>
            </a>
            
            <a href="experts.php" class="nav-item active">
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
                    <h1>إدارة الخبراء</h1>
                    <p>إدارة الخبراء والمستشارين الخارجيين</p>
                </div>
            </div>
            <div class="header-right">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-home"></i> الرئيسية
                </a>
                <a href="#add-expert" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> إضافة خبير جديد
                </a>
            </div>
        </div>

        <div class="content-card">
            <h2>قائمة الخبراء والمستشارين</h2>
            <p style="margin-bottom: 20px; color: var(--text-secondary);">
                إدارة الخبراء والمستشارين الخارجيين المشاركين في المشاريع.
            </p>
            
            <div class="alert alert-info">
                <i class="fa-solid fa-info-circle"></i>
                هذه الصفحة قيد التطوير. ستتم إضافة ميزات إدارة الخبراء قريباً.
            </div>
            
            <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px; border: 2px dashed var(--border-color);">
                <i class="fa-solid fa-tools" style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px;"></i>
                <h3 style="color: var(--text-secondary); margin-bottom: 15px;">قيد التطوير</h3>
                <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto; font-size: 16px;">
                    ميزة إدارة الخبراء والمستشارين قيد التطوير حالياً. ستكون متاحة قريباً لإدارة الخبراء والمستشارين الخارجيين.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // إخفاء التنبيهات بعد 5 ثوانٍ
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
    
    // رابط إضافة خبير جديد (مؤقت)
    const addExpertLinks = document.querySelectorAll('a[href="#add-expert"]');
    addExpertLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            alert('ميزة إضافة الخبراء قيد التطوير حالياً. ستكون متاحة قريباً.');
        });
    });
});
</script>
</body>
</html>
<?php close_connection(); ?>