@echo off
chcp 65001 >nul
title Antigravity Sohbet Geçmişi ve Oturum Kurtarma
cls
echo ========================================================
echo   Antigravity IDE - BPA V3 ve ACMS Sohbet Kurtarma
echo ========================================================
echo.

echo [1/3] Antigravity IDE kapatiliyor (veritabaninin kilitlenmemesi icin)...
taskkill /F /IM "Antigravity IDE.exe" >nul 2>&1
taskkill /F /IM "Antigravity.exe" >nul 2>&1
timeout /t 2 /nobreak >nul

echo [2/3] BPA V3, ACMS Ana Sohbet (105 Mesaj) ve Tum Sohbetler Veritabanina Yaziliyor...
"C:\Users\alper\AppData\Local\Programs\Python\Python313\python.exe" "c:\xampp\htdocs\acms\recover_all_conversations.py"

echo.
echo [3/3] Antigravity IDE yeniden baslatiliyor...
timeout /t 1 /nobreak >nul
start "" "C:\Users\alper\AppData\Local\Programs\Antigravity IDE\Antigravity IDE.exe" "c:\xampp\htdocs\acms"

echo.
echo ========================================================
echo   ISLEM BASARIYLA TAMAMLANDI!
echo   1. Antigravity IDE acildiginda Ctrl + L tusuna basin.
echo   2. Sohbet panelindeki gecmis listesine tiklayin.
echo   3. "BPA V3 - Sports Analytics" ve "ACMS - Canli Onizleme"
echo      dahil tum sohbetleriniz en ustte listelenecektir.
echo ========================================================
echo.
pause
