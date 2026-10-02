@echo off
chcp 65001 > nul
title Tío Chu - Instalador y Configurador Inicial
color 0E

echo =====================================================================
echo                DISCOTECA TÍO CHU - INSTALADOR INICIAL
echo =====================================================================
echo.
echo [1/6] Verificando entorno PHP...
where php >nul 2>nul
if %ERRORLEVEL% neq 0 (
    color 0C
    echo [ERROR] No se encontró PHP instalado o no está en el PATH del sistema.
    echo Por favor asegúrate de tener PHP 8.2 o superior instalado.
    echo.
    pause
    exit /b 1
)

echo [2/6] Configurando archivo de entorno (.env)...
if not exist ".env" (
    copy .env.example .env > nul
    echo       - Archivo .env creado a partir de .env.example.
) else (
    echo       - Archivo .env ya existe.
)

echo [3/6] Verificando dependencias (Composer)...
if not exist "vendor" (
    where composer >nul 2>nul
    if %ERRORLEVEL% equ 0 (
        echo       - Instalando paquetes de Composer...
        call composer install --no-interaction --prefer-dist --optimize-autoloader
    ) else (
        echo       - [AVISO] Composer no está en PATH, pero si ya tienes vendor se continuará.
    )
) else (
    echo       - Carpeta vendor detectada correctamente.
)

echo [4/6] Generando clave de seguridad de la aplicación...
call php artisan key:generate --force

echo [5/6] Preparando Base de Datos SQLite y Datos Oficiales...
if not exist "database\database.sqlite" (
    type nul > "database\database.sqlite"
    echo       - Archivo database.sqlite creado.
)

echo       - Ejecutando migraciones y cargando datos oficiales (Usuarios, Bebidas, Personal)...
call php artisan migrate:fresh --seed --force

echo [6/6] Optimizando enlaces y caché...
call php artisan storage:link 2>nul
call php artisan optimize:clear

echo.
color 0A
echo =====================================================================
echo              ¡INSTALACIÓN COMPLETADA EXITOSAMENTE!
echo =====================================================================
echo.
echo Credenciales de acceso predeterminadas:
echo   - Correo: DonLudo@gmail.chu  (o DonLudo@gmail.com)
echo   - Clave:  tiochu123
echo.
echo Para abrir el sistema cada día, haz doble clic en:
echo   "2_INICIAR_TIO_CHU.bat"
echo.
echo =====================================================================
echo Presiona cualquier tecla para cerrar este instalador...
pause > nul
