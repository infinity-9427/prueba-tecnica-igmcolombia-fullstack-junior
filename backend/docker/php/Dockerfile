FROM php:8.2-fpm AS builder

# Configure apt with timeouts and minimal downloads
RUN echo 'Acquire::http::Timeout "600";' > /etc/apt/apt.conf.d/99timeout && \
    echo 'Acquire::Retries "5";' >> /etc/apt/apt.conf.d/99timeout

# Install build dependencies and PHP extensions in one layer
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install PHP extensions with retry logic
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Get Composer with retry logic
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Configure Composer for better performance
RUN composer config --global process-timeout 600 && \
    composer config --global cache-dir /tmp/composer-cache

WORKDIR /var/www

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install dependencies with retry logic and optimization
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --optimize-autoloader \
    --prefer-dist \
    --no-progress \
    --no-suggest \
    --classmap-authoritative || \
    (sleep 30 && composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --optimize-autoloader \
    --prefer-dist \
    --no-progress \
    --no-suggest \
    --classmap-authoritative) || \
    (sleep 60 && composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --optimize-autoloader \
    --prefer-dist \
    --no-progress \
    --no-suggest \
    --classmap-authoritative)

# Production stage
FROM php:8.2-fpm AS production

# Install runtime and build dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq5 \
    libpng16-16t64 \
    libzip5 \
    postgresql-client \
    curl \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Clean up development packages
RUN apt-get purge -y --auto-remove \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev

# Create non-root user
RUN groupadd --gid 1000 appuser && \
    useradd --uid 1000 --gid appuser --shell /bin/bash --create-home appuser

WORKDIR /var/www

# Copy vendor from builder stage
COPY --from=builder /var/www/vendor /var/www/vendor

# Copy application code
COPY --chown=appuser:appuser . .

# Generate optimized autoloader
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Set proper permissions
RUN chown -R appuser:appuser /var/www && \
    chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Create health check script
RUN echo '#!/bin/bash\nif [ -f /var/www/artisan ]; then\n  php /var/www/artisan inspire > /dev/null 2>&1 || exit 1\nelse\n  exit 1\nfi' > /usr/local/bin/healthcheck.sh && \
    chmod +x /usr/local/bin/healthcheck.sh

# Add OPcache configuration
RUN echo 'opcache.enable=1\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0\n\
opcache.max_accelerated_files=10000\n\
opcache.memory_consumption=192\n\
opcache.max_wasted_percentage=10\n\
opcache.fast_shutdown=1' > /usr/local/etc/php/conf.d/opcache.ini

# Create entrypoint script
RUN echo '#!/bin/bash\nset -e\n\n# Wait for database\nwhile ! pg_isready -h ${DB_HOST:-db} -p ${DB_PORT:-5432} -U ${DB_USERNAME:-postgres}; do\n  echo "Waiting for database..."\n  sleep 2\ndone\n\n# Run migrations if needed\nif [ "${AUTO_MIGRATE:-true}" = "true" ]; then\n  php artisan migrate --force\nfi\n\n# Cache configuration\nphp artisan config:cache\nphp artisan route:cache\nphp artisan view:cache\n\nexec "$@"' > /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

USER appuser

# Environment variables for optimization
ENV PHP_OPCACHE_ENABLE=1 \
    PHP_OPCACHE_REVALIDATE_FREQ=0 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=10000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192 \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE=10 \
    COMPOSER_CACHE_DIR=/tmp/composer-cache

EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD /usr/local/bin/healthcheck.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]