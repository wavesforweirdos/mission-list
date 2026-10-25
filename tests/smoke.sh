#!/usr/bin/env bash
# Prueba de humo: arranca el servidor de PHP y comprueba el login, la protección
# de las rutas privadas y el rechazo de nombres de usuario peligrosos.
# Uso: tests/smoke.sh   (necesita php y curl)
set -u
cd "$(dirname "$0")/.."

PORT=${PORT:-8765}
BASE="http://localhost:$PORT"
JAR=$(mktemp)
php -S "localhost:$PORT" -t web web/index.php >/dev/null 2>&1 &
SERVER=$!
trap 'kill $SERVER 2>/dev/null; rm -f "$JAR"' EXIT
for _ in $(seq 1 30); do curl -s -o /dev/null "$BASE/" && break; sleep 0.2; done

fail=0
check() { # descripción, esperado, obtenido
  if [ "$2" = "$3" ]; then echo "ok   $1"; else echo "FAIL $1 (esperado '$2', obtenido '$3')"; fail=1; fi
}
status() { curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "$@"; }

check "la página de login carga" "200 " "$(status "$BASE/")"
check "el logo se sirve" "200 " "$(status "$BASE/images/wf-logo-white.png")"
check "las misiones piden iniciar sesión" "302 $BASE/index" "$(status "$BASE/mission")"
check "un nombre de usuario con ../ se rechaza" "302 $BASE/signin" \
  "$(status -d 'signin=1&username=../../web/pwn&name=x&lastname=y&email=a@b.co&password=pw' "$BASE/signin")"
check "no se crea ningún fichero fuera de config/database" "" "$(ls web | grep -- '-missions.json' || true)"
check "registrarse con un usuario existente no da acceso a sus misiones" "302 $BASE/index"   "$(status -c "$JAR" -d 'signin=1&username=demo&name=x&lastname=y&email=a@b.co&password=pw' "$BASE/signin" >/dev/null; status -b "$JAR" "$BASE/mission")"
check "con la contraseña incorrecta se queda en el login" "200 "   "$(status -c "$JAR" -d 'login=1&username=demo&password=incorrecta' "$BASE/login")"
check "y tampoco ve las misiones" "302 $BASE/index" "$(status -b "$JAR" "$BASE/mission")"
check "el usuario de prueba entra" "302 $BASE/mission" \
  "$(status -c "$JAR" -d 'login=1&username=demo&password=demo1234' "$BASE/login")"
check "y ve sus misiones" "1" "$(curl -s -b "$JAR" "$BASE/mission" | grep -q 'Defend the north tower' && echo 1)"

exit $fail
