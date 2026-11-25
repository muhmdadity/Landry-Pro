FROM dunglas/frankenphp:1-php8.4

# Instal ekstensi PDO MySQL (karena biasanya proyek PHP pakai database)
RUN install-php-extensions pdo_mysql

WORKDIR /app
COPY . /app

# Salin konfigurasi Caddyfile yang sudah kamu buat
COPY Caddyfile /etc/frankenphp/Caddyfile

ENV PORT=80
