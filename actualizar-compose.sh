#!/usr/bin/env bash

set -euo pipefail

# 1. Definir archivo objetivo (por defecto busca compose.yml o docker-compose.yml)
COMPOSE_FILE="${1:-}"

if [[ -z "$COMPOSE_FILE" ]]; then
  if [[ -f "compose.yml" ]]; then
    COMPOSE_FILE="compose.yml"
  elif [[ -f "docker-compose.yml" ]]; then
    COMPOSE_FILE="docker-compose.yml"
  else
    echo "❌ Error: No se encontró compose.yml ni docker-compose.yml en el directorio actual."
    exit 1
  fi
fi

if [[ ! -f "$COMPOSE_FILE" ]]; then
  echo "❌ Error: El archivo '$COMPOSE_FILE' no existe."
  exit 1
fi

echo "📦 Usando archivo: $COMPOSE_FILE"

# 2. Detener y remover los contenedores activos antes de actualizar
echo "🛑 Deteniendo y removiendo contenedores en ejecución..."
docker compose -f "$COMPOSE_FILE" down

# 3. Descargar versiones más recientes de imágenes públicas
echo "⬇️  Descargando imágenes actualizadas..."
docker compose -f "$COMPOSE_FILE" pull

# 4. Reconstruir servicios locales si usan directiva 'build'
echo "🔨 Reconstruyendo servicios locales (si aplican)..."
docker compose -f "$COMPOSE_FILE" build --pull

# 5. Levantar todos los contenedores con las nuevas imágenes
echo "🚀 Iniciando contenedores actualizados..."
docker compose -f "$COMPOSE_FILE" up -d --remove-orphans

# 6. Limpieza opcional de imágenes huérfanas
echo "🧹 Limpiando imágenes antiguas en desuso..."
docker image prune -f

echo "✅ Actualización completada con éxito."
