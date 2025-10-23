FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    nmap \
    mariadb-client-compat \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip intl opcache bcmath exif

# Enable Apache modules (rewrite is still needed)
RUN a2enmod rewrite

# Install wait-for-it.sh
RUN apt-get update && apt-get install -y wget \
    && wget https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -O /usr/local/bin/wait-for-it.sh \
    && chmod +x /usr/local/bin/wait-for-it.sh \
    && rm -rf /var/lib/apt/lists/*

# --- Modify default Apache configuration in-place ---
# Update Apache to listen on 2266
RUN echo "Listen 2266" >> /etc/apache2/ports.conf
# Modify the default virtual host to listen on 2266 and set DirectoryIndex
RUN sed -i -e 's/VirtualHost \*:80/VirtualHost \*:2266/g' /etc/apache2/sites-available/000-default.conf \
    && sed -i -e 's/VirtualHost \*:80/VirtualHost \*:2266/g' /etc/apache2/sites-enabled/000-default.conf \
    && sed -i -e 's/DirectoryIndex index.html/DirectoryIndex index.php index.html/g' /etc/apache2/mods-enabled/dir.conf

# Ensure the uploads directory exists and has correct permissions
RUN mkdir -p /var/www/html/uploads/icons \
    mkdir -p /var/www/html/uploads/map_backgrounds \
    mkdir -p /var/www/html/uploads/backups \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

# Ensure Apache log directory exists and is writable for PHP errors
RUN mkdir -p /var/log/apache2 \
    && chown -R www-data:www-data /var/log/apache2 \
    && chmod -R 775 /var/log/apache2

# Copy application files from the ampnm-app-source directory
COPY ampnm-app-source/ /var/www/html/

# Copy the entrypoint script from the build context root
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set permissions for the web root
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 2266 (or whatever port your app runs on)
EXPOSE 2266

# Use the copied entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]