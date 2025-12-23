<?php
// includes/sidebar.php
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>إعدادات المنظمة</h2>
        <p>Organization Settings</p>
    </div>
    
    <nav class="nav-menu">
        <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-gear"></i>
            <span>عام</span>
        </a>
        
        <a href="#" class="nav-item">
            <i class="fa-solid fa-users"></i>
            <span>الأعضاء</span>
        </a>
        
        <a href="#" class="nav-item">
            <i class="fa-solid fa-briefcase"></i>
            <span>الخبراء</span>
        </a>
        
        <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-dollar-sign"></i>
            <span>الميزانية</span>
        </a>
        
        <a href="#" class="nav-item">
            <i class="fa-solid fa-chart-line"></i>
            <span>التحليلات</span>
        </a>
    </nav>
</div>