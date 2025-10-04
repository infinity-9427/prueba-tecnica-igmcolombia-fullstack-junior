FROM php:8.3-fpm AS builder

RUN apt-get update && apt-get install --no-install-recommends -y \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \   
    unzip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --optimize-autoloader \
    --prefer-dist \
    --no-progress \
    --classmap-authoritative

FROM php:8.3-fpm AS production

RUN apt-get update && apt-get install --no-install-recommends -y \
    libpq5 \
    libpng16-16t64 \
    libzip5 \
    postgresql-client && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

RUN echo 'opcache.enable=1\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0\n\
opcache.max_accelerated_files=10000\n\
opcache.memory_consumption=192\n\
opcache.max_wasted_percentage=10' > /usr/local/etc/php/conf.d/opcache.ini

RUN apt-get update && apt-get install --no-install-recommends -y libfcgi-bin && rm -rf /var/lib/apt/lists/*

RUN echo '#!/bin/bash\nCGI_PATH=/var/www/public/index.php\nSCRIPT_FILENAME=$CGI_PATH REQUEST_METHOD=GET REQUEST_URI=/ cgi-fcgi -bind -connect 127.0.0.1:9000' > /usr/local/bin/healthcheck.sh && \
    chmod +x /usr/local/bin/healthcheck.sh

RUN echo '#!/bin/bash\nset -euo pipefail\n\n# Colors for output\nRED="\\033[0;31m"\nGREEN="\\033[0;32m"\nYELLOW="\\033[1;33m"\nNC="\\033[0m" # No Color\n\n# Error handling function\nhandle_error() {\n  echo -e "${RED}❌ Error occurred in entrypoint script at line $1${NC}" >&2\n  echo -e "${RED}Command: $2${NC}" >&2\n  exit 1\n}\n\n# Set error trap\ntrap '\''handle_error $LINENO "$BASH_COMMAND"'\'' ERR\n\necho -e "${GREEN}🚀 Starting Laravel application...${NC}"\n\n# Wait for database with timeout\necho -e "${YELLOW}⏳ Waiting for database connection...${NC}"\nmax_attempts=30\nattempt=0\nwhile ! pg_isready -h ${DB_HOST:-db} -p ${DB_PORT:-5432} -U ${DB_USERNAME:-postgres} -d ${DB_DATABASE:-invoice_db} -q; do\n  attempt=$((attempt + 1))\n  if [ $attempt -ge $max_attempts ]; then\n    echo -e "${RED}❌ Database connection timeout after ${max_attempts} attempts${NC}" >&2\n    exit 1\n  fi\n  echo -e "${YELLOW}Database not ready (attempt $attempt/$max_attempts), waiting...${NC}"\n  sleep 2\ndone\necho -e "${GREEN}✅ Database is ready!${NC}"\n\n# Run migrations if AUTO_MIGRATE is true\nif [ "${AUTO_MIGRATE:-true}" = "true" ]; then\n  echo -e "${YELLOW}🔄 Running database migrations...${NC}"\n  if php artisan migrate --force; then\n    echo -e "${GREEN}✅ Migrations completed successfully!${NC}"\n  else\n    echo -e "${RED}❌ Migration failed!${NC}" >&2\n    exit 1\n  fi\nelse\n  echo -e "${YELLOW}⏭️  Auto-migration disabled (AUTO_MIGRATE=false)${NC}"\nfi\n\n# Cache configuration for better performance\necho -e "${YELLOW}⚡ Optimizing Laravel configuration...${NC}"\nif php artisan config:cache && php artisan route:cache && php artisan view:cache; then\n  echo -e "${GREEN}✅ Configuration optimized successfully!${NC}"\nelse\n  echo -e "${YELLOW}⚠️  Configuration caching failed, continuing anyway...${NC}"\nfi\n\necho -e "${GREEN}🎉 Laravel application ready! Starting PHP-FPM...${NC}"\nexec "$@"' > /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

RUN useradd --create-home appuser
USER appuser

WORKDIR /var/www

COPY . .
COPY --from=builder /var/www/vendor vendor

ENV PHP_OPCACHE_ENABLE=1

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]