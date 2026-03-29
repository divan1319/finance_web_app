#!/usr/bin/env bash

# =============================================================================
# init.sh — Bootstrap de proyecto Laravel + Sail
# Requisitos: Docker (con Docker Compose) instalado. NO necesita PHP ni Composer.
# =============================================================================

set -e

# ---------------------------------------------------------------------------
# Colores para output
# ---------------------------------------------------------------------------
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

info()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
success() { echo -e "${GREEN}where.exe php[OK]${NC}    $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

# ---------------------------------------------------------------------------
# 1. Verificar que Docker esté disponible
# ---------------------------------------------------------------------------
info "Verificando Docker..."
command -v docker >/dev/null 2>&1 || error "Docker no está instalado o no está 
en el PATH."
docker info >/dev/null 2>&1     || error "El daemon de Docker no está corriendo."
success "Docker disponible."

# ---------------------------------------------------------------------------
# 2. Variables configurables
# ---------------------------------------------------------------------------
APP_DIR="${APP_DIR:-$(pwd)}"                # Directorio raíz del proyecto (donde está composer.json)
SAIL_SERVICES="${SAIL_SERVICES:-mysql}"     # Servicios de Sail: pgsql, mysql, redis, etc.

info "Directorio del proyecto : $APP_DIR"
info "Servicios Sail           : $SAIL_SERVICES"

cd "$APP_DIR"

# ---------------------------------------------------------------------------
# 3. Copiar .env si no existe
# ---------------------------------------------------------------------------
if [ ! -f ".env" ]; then
  if [ -f ".env.example" ]; then
    cp .env.example .env
    success ".env creado desde .env.example"
  else
    warn "No se encontró .env.example — asegúrate de configurar .env manualmente."
  fi
else
  info ".env ya existe, se omite la copia."
fi

# ---------------------------------------------------------------------------
# 4. Instalar dependencias con un contenedor temporal de PHP+Composer
#    (no requiere PHP ni Composer instalados en el host)
# ---------------------------------------------------------------------------
if [ ! -d "vendor" ]; then
  info "Instalando dependencias PHP con Composer (contenedor temporal)..."
  docker run --rm \
    --pull=missing \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs --no-interaction --prefer-dist
  success "Dependencias instaladas."
else
  info "vendor/ ya existe, se omite composer install."
fi

# ---------------------------------------------------------------------------
# 5. Publicar el compose.yaml de Sail si no existe
# ---------------------------------------------------------------------------
if [ ! -f "compose.yaml" ] && [ ! -f "docker-compose.yml" ]; then
  info "Publicando configuración de Sail (servicios: $SAIL_SERVICES)..."
  docker run --rm \
    --pull=missing \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    php artisan sail:install --with="$SAIL_SERVICES" --no-interaction
  success "compose.yaml de Sail publicado."
else
  info "compose.yaml ya existe, se omite sail:install."
fi

# ---------------------------------------------------------------------------
# 6. Generar APP_KEY si está vacía
# ---------------------------------------------------------------------------
APP_KEY_VALUE=$(grep -E '^APP_KEY=' .env | cut -d '=' -f2)
if [ -z "$APP_KEY_VALUE" ]; then
  info "Generando APP_KEY..."
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    php artisan key:generate --force
  success "APP_KEY generada."
else
  info "APP_KEY ya está configurada, se omite."
fi

# ---------------------------------------------------------------------------
# 7. Levantar Sail
# ---------------------------------------------------------------------------
info "Levantando contenedores con Sail..."
./vendor/bin/sail up -d
success "Contenedores levantados."


# ---------------------------------------------------------------------------
# 10. Resumen final
# ---------------------------------------------------------------------------
echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  Proyecto listo en http://localhost${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "  Detener:    ${CYAN}./vendor/bin/sail stop${NC}"
echo -e "  Reiniciar:  ${CYAN}./vendor/bin/sail up -d${NC}"
echo -e "  Artisan:    ${CYAN}./vendor/bin/sail artisan <comando>${NC}"
echo -e "  Shell:      ${CYAN}./vendor/bin/sail shell${NC}"
echo ""