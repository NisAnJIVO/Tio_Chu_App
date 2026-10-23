@echo off
cd /d "%~dp0"
chcp 65001 > nul
title Tío Chu
 
set "PHP_BIN=php"
if exist "%~dp0php\php.exe" set "PHP_BIN=%~dp0php\php.exe"

echo =====================================================================
echo                   DISCOTECA TÍO CHU - SISTEMA ACTIVO
echo =====================================================================
echo.
echo El sistema se está iniciando correctamente...
echo Tu navegador se abrirá automáticamente en: http://127.0.0.1:8000
echo.
echo IMPORTANTE: Mantén esta ventana ABIERTA mientras uses el sistema.
echo Para apagar el sistema, simplemente cierra esta ventana.
echo =====================================================================
echo.

:: Abrir navegador en segundo plano con 2 segundos de cortesía para que Laravel responda directo
start "" cmd /c "timeout /t 2 /nobreak >nul & start http://127.0.0.1:8000"

"%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000

pause
