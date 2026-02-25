# استخدام PHP مع Apache
FROM php:8.1-apache

# نسخ جميع ملفات المشروع إلى مجلد Apache
COPY . /var/www/html/

# تثبيت ملحقات MySQL (إذا كان مشروعك يحتاج قاعدة بيانات)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# ضبط الأذونات
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# تمكين mod_rewrite لدعم العناية النظيفة
RUN a2enmod rewrite

# فتح المنفذ 80
EXPOSE 80
