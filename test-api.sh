#!/bin/bash
# API Testing Examples - cURL Commands
# Uso: Ejecutar estos comandos en la terminal para probar la API

API_BASE="http://localhost:8000/api"

echo "🔐 PRUEBAS DE AUTENTICACIÓN - EJEMPLOS cURL"
echo "=============================================="
echo ""

# Variables para reutilizar
USER_EMAIL="testuser@example.com"
USER_PASSWORD="Password123"
USER_NAME="Test User"

echo "📝 1. REGISTRO DE USUARIO"
echo "========================="
echo "Comando:"
echo "curl -X POST $API_BASE/register \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{"
echo "    \"name\": \"$USER_NAME\","
echo "    \"email\": \"$USER_EMAIL\","
echo "    \"password\": \"$USER_PASSWORD\","
echo "    \"password_confirmation\": \"$USER_PASSWORD\""
echo "  }'"
echo ""
echo "Ejecutar:"
curl -X POST $API_BASE/register \
  -H 'Content-Type: application/json' \
  -d "{
    \"name\": \"$USER_NAME\",
    \"email\": \"$USER_EMAIL\",
    \"password\": \"$USER_PASSWORD\",
    \"password_confirmation\": \"$USER_PASSWORD\"
  }" | jq .
echo ""
echo "Guardar token del response en variable:"
echo "TOKEN=<valor de response.token>"
echo ""

read -p "Ingresa el TOKEN recibido (o presiona Enter para continuar): " TOKEN

if [ -z "$TOKEN" ]; then
    echo "⚠️  Sin token. Usar /login para obtener uno."
    echo ""
fi

echo "🔑 2. LOGIN"
echo "==========="
echo "Comando:"
echo "curl -X POST $API_BASE/login \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{"
echo "    \"email\": \"$USER_EMAIL\","
echo "    \"password\": \"$USER_PASSWORD\""
echo "  }'"
echo ""
echo "Ejecutar:"
LOGIN_RESPONSE=$(curl -s -X POST $API_BASE/login \
  -H 'Content-Type: application/json' \
  -d "{
    \"email\": \"$USER_EMAIL\",
    \"password\": \"$USER_PASSWORD\"
  }")
echo "$LOGIN_RESPONSE" | jq .

echo ""
echo "Extraer TOKEN del response:"
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.token')
echo "TOKEN=$TOKEN"
echo ""

echo "👤 3. OBTENER PERFIL DEL USUARIO (Protegido)"
echo "============================================"
echo "Comando:"
echo "curl -X GET $API_BASE/profile \\"
echo "  -H 'Authorization: Bearer $TOKEN' \\"
echo "  -H 'Content-Type: application/json'"
echo ""
echo "Ejecutar:"
if [ ! -z "$TOKEN" ] && [ "$TOKEN" != "null" ]; then
    curl -s -X GET $API_BASE/profile \
      -H "Authorization: Bearer $TOKEN" \
      -H 'Content-Type: application/json' | jq .
else
    echo "❌ No hay TOKEN válido"
fi
echo ""

echo "🚪 4. LOGOUT (Protegido)"
echo "======================="
echo "Comando:"
echo "curl -X POST $API_BASE/logout \\"
echo "  -H 'Authorization: Bearer $TOKEN' \\"
echo "  -H 'Content-Type: application/json'"
echo ""
echo "Ejecutar:"
if [ ! -z "$TOKEN" ] && [ "$TOKEN" != "null" ]; then
    curl -s -X POST $API_BASE/logout \
      -H "Authorization: Bearer $TOKEN" \
      -H 'Content-Type: application/json' | jq .
else
    echo "❌ No hay TOKEN válido"
fi
echo ""

echo "❌ 5. ERRORES COMUNES"
echo "===================="
echo ""

echo "5a. Email duplicado:"
curl -s -X POST $API_BASE/register \
  -H 'Content-Type: application/json' \
  -d "{
    \"name\": \"Another User\",
    \"email\": \"$USER_EMAIL\",
    \"password\": \"$USER_PASSWORD\",
    \"password_confirmation\": \"$USER_PASSWORD\"
  }" | jq .
echo ""

echo "5b. Contraseña muy corta:"
curl -s -X POST $API_BASE/register \
  -H 'Content-Type: application/json' \
  -d "{
    \"name\": \"Short Pass User\",
    \"email\": \"short@example.com\",
    \"password\": \"Pass1\",
    \"password_confirmation\": \"Pass1\"
  }" | jq .
echo ""

echo "5c. Contraseñas no coinciden:"
curl -s -X POST $API_BASE/register \
  -H 'Content-Type: application/json' \
  -d "{
    \"name\": \"No Match User\",
    \"email\": \"nomatch@example.com\",
    \"password\": \"Password123\",
    \"password_confirmation\": \"Password456\"
  }" | jq .
echo ""

echo "5d. Login con credenciales incorrectas:"
curl -s -X POST $API_BASE/login \
  -H 'Content-Type: application/json' \
  -d "{
    \"email\": \"wrong@example.com\",
    \"password\": \"WrongPassword\"
  }" | jq .
echo ""

echo "🔐 6. ENDPOINTS SIN AUTENTICACIÓN (401)"
echo "========================================"
echo "Intentar acceder a /dashboard sin token:"
curl -s -X GET $API_BASE/dashboard | jq .
echo ""

echo "✅ TESTING COMPLETADO"
echo "===================="
echo ""
echo "Próximos pasos:"
echo "1. Abre http://localhost:8000/login en navegador"
echo "2. Usa credenciales: email=$USER_EMAIL, password=$USER_PASSWORD"
echo "3. Verifica que el Dashboard carga con datos reales"
echo "4. Revisa la consola del navegador (F12) para ver requests"
echo "5. Intenta logout y verifica que localStorage se limpie"
echo ""
