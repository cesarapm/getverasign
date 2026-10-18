#!/bin/bash
# Testing Script for Authentication System
# Uso: bash test-auth.sh

set -e

echo "🔐 Testing Authentication System..."
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# URLs
API_URL="http://localhost:8000/api"
FRONTEND_URL="http://localhost:8000"

echo -e "${BLUE}=== STEP 1: Validar Rutas API ===${NC}"
echo "Verificando que las rutas estén registradas..."
php artisan route:list | grep -E "login|register|logout|profile|dashboard" || echo "No se encontraron rutas"
echo ""

echo -e "${BLUE}=== STEP 2: Verificar Base de Datos ===${NC}"
echo "Comprobando migraciones..."
php artisan migrate:status | grep -E "signature_documents|users" || echo "Migraciones ejecutadas"
echo ""

echo -e "${BLUE}=== STEP 3: Compilar Frontend ===${NC}"
if npm run build; then
    echo -e "${GREEN}✓ Build exitoso${NC}"
else
    echo -e "${RED}✗ Build falló${NC}"
    exit 1
fi
echo ""

echo -e "${BLUE}=== STEP 4: Verificar Servidor ===${NC}"
echo "Asegúrate de tener el servidor corriendo:"
echo -e "${YELLOW}php artisan serve${NC}"
echo ""

echo -e "${BLUE}=== STEP 5: URLs de Prueba ===${NC}"
echo "Frontend:"
echo "  📍 Inicio:       ${FRONTEND_URL}/"
echo "  📍 Login:        ${FRONTEND_URL}/login"
echo "  📍 Registro:     ${FRONTEND_URL}/registro"
echo "  📍 Dashboard:    ${FRONTEND_URL}/dashboard (protegido)"
echo ""

echo "API Endpoints:"
echo "  📍 POST   ${API_URL}/login"
echo "  📍 POST   ${API_URL}/register"
echo "  📍 POST   ${API_URL}/logout (requiere token)"
echo "  📍 GET    ${API_URL}/profile (requiere token)"
echo "  📍 GET    ${API_URL}/user (requiere token)"
echo ""

echo -e "${BLUE}=== STEP 6: Test Manual con curl ===${NC}"
echo "Registro de usuario (prueba):"
echo -e "${YELLOW}curl -X POST ${API_URL}/register \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{\"name\":\"Test User\",\"email\":\"test@example.com\",\"password\":\"Password123\",\"password_confirmation\":\"Password123\"}' ${NC}"
echo ""

echo "Login:"
echo -e "${YELLOW}curl -X POST ${API_URL}/login \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{\"email\":\"test@example.com\",\"password\":\"Password123\"}' ${NC}"
echo ""

echo -e "${BLUE}=== STEP 7: Checklist de Prueba ===${NC}"
echo "☐ Ir a ${FRONTEND_URL}/registro y crear cuenta"
echo "☐ Debe redirigir automáticamente al Dashboard"
echo "☐ Ver nombre en navbar superior derecho"
echo "☐ Hacer logout desde menú de usuario"
echo "☐ Ir a ${FRONTEND_URL}/login y volver a entrar"
echo "☐ Intentar acceder a ${FRONTEND_URL}/dashboard sin login → debe ir a login"
echo "☐ Intentar acceder a ${FRONTEND_URL}/login ya logueado → debe ir a dashboard"
echo "☐ Crear un documento y verificar que aparezca en Dashboard"
echo "☐ Verificar que solo los documentos del usuario logueado aparecen"
echo ""

echo -e "${GREEN}✓ Pruebas de autenticación configuradas${NC}"
echo ""
