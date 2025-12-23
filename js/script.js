// js/script.js - ملف جافا سكريبت للوحة التحكم

document.addEventListener('DOMContentLoaded', function() {
    
    // تأكيد الحذف
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد من الحذف؟ لا يمكن التراجع عن هذا الإجراء.')) {
                e.preventDefault();
            }
        });
    });
    
    // حساب التاريخ الافتراضي (اليوم)
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput && !startDateInput.value) {
        startDateInput.value = today;
    }
    
    if (endDateInput && !endDateInput.value) {
        const nextYear = new Date();
        nextYear.setFullYear(nextYear.getFullYear() + 1);
        endDateInput.value = nextYear.toISOString().split('T')[0];
    }
    
    // التحقق من صحة التواريخ
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            if (this.value > endDateInput.value) {
                alert('تاريخ البدء يجب أن يكون قبل تاريخ الانتهاء');
                this.value = endDateInput.value;
            }
        });
        
        endDateInput.addEventListener('change', function() {
            if (this.value < startDateInput.value) {
                alert('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
                this.value = startDateInput.value;
            }
        });
    }
    
    // حساب المبلغ المتبقي تلقائياً
    const totalAmountInput = document.getElementById('total_amount');
    const spentAmountInput = document.getElementById('spent_amount');
    
    if (totalAmountInput && spentAmountInput) {
        function calculateRemaining() {
            const total = parseFloat(totalAmountInput.value) || 0;
            const spent = parseFloat(spentAmountInput.value) || 0;
            
            if (spent > total) {
                alert('المبلغ المنفق لا يمكن أن يكون أكبر من المبلغ الإجمالي');
                spentAmountInput.value = total;
            }
        }
        
        totalAmountInput.addEventListener('input', calculateRemaining);
        spentAmountInput.addEventListener('input', calculateRemaining);
    }
    
    // عرض/إخفاء رسائل التنبيه تلقائياً
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // تغيير لون شريط التقدم بناءً على النسبة
    const progressFills = document.querySelectorAll('.progress-fill');
    progressFills.forEach(fill => {
        const width = parseInt(fill.style.width);
        if (width >= 80) {
            fill.style.backgroundColor = '#dc3545';
        } else if (width >= 60) {
            fill.style.backgroundColor = '#f59e0b';
        } else {
            fill.style.backgroundColor = '#10b981';
        }
    });
});