# Dockerfile para NFC-e com PHP 8.2 e Apache

FROM php:8.2.12-apache

# Instala dependências do sistema
RUN apt-get update && \
    apt-get install -y \
    default-mysql-client \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    zip

# Habilita módulos do Apache
RUN a2enmod rewrite headers

# Configurações recomendadas para produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Define variáveis de ambiente para MySQL
ENV MYSQL_DATABASE=joalheria
ENV MYSQL_USER=root
ENV MYSQL_PASSWORD=root
ENV MYSQL_ROOT_PASSWORD=root

# Diretório de trabalho
WORKDIR /var/www/html

# Recomendado: Copiar seus arquivos de aplicação (substitua pelo seu método preferido)
# COPY . /var/www/html/

# Permissões (ajuste conforme necessário)
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Porta exposta
EXPOSE 80

# Comando padrão
CMD ["apache2-foreground"]