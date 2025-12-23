<?php
// includes/budgets_list.php
global $budgets_result;
?>

<div class="budgets-grid">
    <?php if (mysqli_num_rows($budgets_result) > 0): ?>
        <?php while ($budget = mysqli_fetch_assoc($budgets_result)): 
            $progress = $budget['progress'];
            
            // تحديد لون شريط التقدم
            if ($progress >= 80) {
                $progress_class = 'high';
                $progress_color = '#dc3545';
            } elseif ($progress >= 60) {
                $progress_class = 'medium';
                $progress_color = '#f59e0b';
            } else {
                $progress_class = 'low';
                $progress_color = '#10b981';
            }
            
            // تنسيق المبلغ
            $formatted_amount = '$' . number_format($budget['total_amount']);
            
            // تنسيق التاريخ
            $start_date_formatted = date('M d, Y', strtotime($budget['start_date']));
            $end_date_formatted = date('M d, Y', strtotime($budget['end_date']));
        ?>
        <div class="budget-card">
            <div class="budget-header">
                <div class="budget-title">
                    <div class="budget-amount"><?php echo $formatted_amount; ?></div>
                    <div class="budget-name"><?php echo htmlspecialchars($budget['title']); ?></div>
                </div>
                <div class="budget-menu">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </div>
            </div>
            
            <div class="progress-section">
                <div class="progress-labels">
                    <span class="progress-percent" style="color: <?php echo $progress_color; ?>;">
                        <?php echo $progress; ?>%
                    </span>
                    <span><?php echo 100 - $progress; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill <?php echo $progress_class; ?>" style="width: <?php echo $progress; ?>%;"></div>
                </div>
            </div>
            
            <div class="budget-details">
                <div class="detail-row">
                    <span class="detail-label">المبلغ الإجمالي</span>
                    <span class="detail-value">$<?php echo number_format($budget['total_amount']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">تاريخ البدء</span>
                    <span class="detail-value"><?php echo $start_date_formatted; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">تاريخ الانتهاء</span>
                    <span class="detail-value"><?php echo $end_date_formatted; ?></span>
                </div>
            </div>
            
            <?php if (!empty($budget['tags'])): 
                $tags = explode(',', $budget['tags']);
            ?>
            <div class="tags-container">
                <?php foreach ($tags as $tag): 
                    $tag_trimmed = trim($tag);
                    if (!empty($tag_trimmed)):
                ?>
                <span class="tag"><?php echo htmlspecialchars($tag_trimmed); ?></span>
                <?php endif; endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="form-actions">
                <a href="operations/update.php?id=<?php echo $budget['id']; ?>" class="btn btn-sm btn-edit">
                    <i class="fa-solid fa-edit"></i> تعديل
                </a>
                <a href="operations/view.php?id=<?php echo $budget['id']; ?>" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-eye"></i> عرض التفاصيل
                </a>
                <a href="operations/delete.php?id=<?php echo $budget['id']; ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('هل أنت متأكد من حذف هذه الميزانية؟')">
                    <i class="fa-solid fa-trash"></i> حذف
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-wallet"></i>
            <h3>لا توجد ميزانيات</h3>
            <p>ابدأ بإضافة أول ميزانية لك</p>
            <a href="operations/insert.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> إضافة ميزانية جديدة
            </a>
        </div>
    <?php endif; ?>
</div>