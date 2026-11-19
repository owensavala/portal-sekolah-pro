@echo off
setlocal
cd /d "%~dp0"
title Portal Madrasah Pro - Local Server

:: Auto detect PHP binary
set "PHP_CMD=php"
where php >nul 2>&1
if %errorlevel% neq 0 (
    if exist "C:\xampp\php\php.exe" (
        set "PHP_CMD=C:\xampp\php\php.exe"
    ) else (
        echo [ERROR] PHP tidak ditemukan di PATH atau C:\xampp\php\php.exe!
        echo Pastikan XAMPP atau PHP sudah terinstall.
        pause
        exit /b 1
    )
)

cls
echo ========================================================
echo        PORTAL MADRASAH PRO - LOCAL SERVER AKTIF
echo ========================================================
echo.
echo   [OK] Menggunakan PHP dari: %PHP_CMD%
echo   [OK] Server berjalan di  : http://localhost:8080
echo.
echo   - Web Portal : http://localhost:8080
echo   - Panel Admin: http://localhost:8080/admin/login.php
echo.
echo   Login Default Admin:
echo     Username: admin
echo     Password: admin123
echo.
echo   Tekan CTRL+C di jendela ini jika ingin mematikan server.
echo ========================================================
echo.

"%PHP_CMD%" -S 0.0.0.0:8080 -t "%~dp0"
pause
