@echo off
cd /d "%~dp0"
chcp 65001 > nul
title Tío Chu - Servidor

set "PHP_BIN=php"
if exist "%~dp0php\php.exe" set "PHP_BIN=%~dp0php\php.exe"

echo =====================================================================
echo                   DISCOTECA TÍO CHU - SERVIDOR ACTIVO
echo =====================================================================
echo.
echo Abriendo el navegador en http://127.0.0.1:8000 ...
echo Presiona Ctrl + C para apagar el servidor cuando termines.
echo.

start "" http://127.0.0.1:8000
"%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000

taskkill /F /IM ngrok.exe >nul 2>&1
pause
