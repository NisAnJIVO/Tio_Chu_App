@echo off
chcp 65001 > nul
title Tío Chu - Servidor de Operación
color 06
set "PHP_BIN=php"
if exist "%~dp0php\php.exe" set "PHP_BIN=%~dp0php\php.exe"
cd /d "%~dp0"

echo =====================================================================
echo                   DISCOTECA TÍO CHU - SERVIDOR ACTIVO
echo =====================================================================
echo.

if not exist ".env" (
    color 0C
    echo [ERROR] No se ha configurado el archivo .env.
    echo Por favor ejecuta primero "1_INSTALAR_SISTEMA.bat".
    echo.
    pause
    exit /b 1
)

echo Iniciando servidor local en http://127.0.0.1:8000 ...
echo Abriendo el navegador en 2 segundos...
echo.
echo Presiona Ctrl + C en esta ventana cuando desees apagar el servidor.
echo =====================================================================
echo.

start "" cmd /c "timeout /t 2 /nobreak >nul & start http://127.0.0.1:8000"
call "%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000
