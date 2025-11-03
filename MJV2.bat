REM filepath: c:\Users\User\Desktop\MJ.bat
@echo off
setlocal enabledelayedexpansion

REM === SECURITY CHECK ===
echo Checking security passcode...
set "GITHUB_URL=https://raw.githubusercontent.com/MarkSenpai20/STI-COLLEGE-LAOAG/main/README.md"

REM Use PowerShell to fetch and clean the first line from GitHub README
for /f "delims=" %%p in ('powershell -NoProfile -ExecutionPolicy Bypass -Command "$content = (Invoke-WebRequest '%GITHUB_URL%' -UseBasicParsing).Content; ($content -split '\r?\n' | Select-Object -First 1).Trim()"') do (
    set "REAL_PASSCODE=%%p"
)

if "%REAL_PASSCODE%"=="" (
    echo ERROR: Could not retrieve security passcode from GitHub
    echo Please check your internet connection and try again
    pause
    exit /b 1
)

echo Retrieved passcode from GitHub
set /p USER_PASSCODE="Enter security passcode: "

if not "%USER_PASSCODE%"=="%REAL_PASSCODE%" (
    echo.
    echo ERROR: Invalid security passcode!
    echo Expected: %REAL_PASSCODE%
    echo Got: %USER_PASSCODE%
    echo Access denied.
    timeout /t 3 /nobreak >nul
    exit /b 1
)

echo Security check passed! Starting application...
timeout /t 2 /nobreak >nul
REM === END SECURITY CHECK ===

REM Use PowerShell with STA and no profile so COM SendKeys works reliably
set "PS=powershell -NoProfile -ExecutionPolicy Bypass -STA -Command"

echo Starting automated browser session...
timeout /t 2 /nobreak >nul

REM Close any existing Edge instances
taskkill /f /im msedge.exe >nul 2>&1

REM Step 1: Launch Edge InPrivate and open Google, capture PID so we can target the same window/tabs
for /f "usebackq delims=" %%p in (`%PS% " $p = Start-Process 'msedge.exe' -ArgumentList '-inprivate','https://accounts.google.com' -PassThru; Write-Output $p.Id"`) do set "EDGEPID=%%p"
timeout /t 1 /nobreak >nul

REM --- GOOGLE AUTO-TYPING EXAMPLE ---
REM Auto-type into the active Edge window (assumes Edge is focused). Adjust sleeps if needed.
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; Start-Sleep -Milliseconds 300; $wshell.AppActivate(%EDGEPID%); Start-Sleep -Milliseconds 200; $wshell.SendKeys('jhonangelocruz@gmail.com')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); Start-Sleep -Milliseconds 100; $wshell.SendKeys('{ENTER}')"
timeout /t 3 /nobreak >nul  
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('Angelo#20')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{ENTER}')"
echo [Google page loaded - add auto-typing code above]
timeout /t 1 /nobreak >nul

REM Step 2: Open ELMS in a NEW TAB in the same InPrivate window (Ctrl+T, then navigate)
echo Opening ELMS in a new tab...
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; Start-Sleep -Milliseconds 300; $wshell.AppActivate(%EDGEPID%); Start-Sleep -Milliseconds 150; $wshell.SendKeys('^t'); Start-Sleep -Milliseconds 200; $wshell.SendKeys('^l'); $wshell.SendKeys('https://elms.sti.edu{ENTER}')"
timeout /t 1 /nobreak >nul

REM --- ELMS AUTO-TYPING EXAMPLE ---
timeout /t 2 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); Start-Sleep -Milliseconds 200; $wshell.SendKeys('{TAB}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{ENTER}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{TAB}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{TAB}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{TAB}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{ENTER}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{TAB}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{TAB}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{ENTER}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('2000362890')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{TAB}')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('Llanes#20')"
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); $wshell.SendKeys('{ENTER}')"
echo [ELMS page loaded - add auto-typing code above]
timeout /t 2 /nobreak >nul

REM Step 3: Open GitHub in a NEW TAB (same window)
echo Opening GitHub with Google sign-in in a new tab...
timeout /t 1 /nobreak >nul
%PS% "$wshell = New-Object -ComObject wscript.shell; Start-Sleep -Milliseconds 300; $wshell.AppActivate(%EDGEPID%); Start-Sleep -Milliseconds 150; $wshell.SendKeys('^t'); Start-Sleep -Milliseconds 200; $wshell.SendKeys('^l'); $wshell.SendKeys('https://github.com/login{ENTER}')"
timeout /t 4 /nobreak >nul

REM --- GITHUB WITH GOOGLE SIGN-IN ---
timeout /t 3 /nobreak >nul
REM Use Tab to navigate to "Continue with Google" button and press Enter
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); Start-Sleep -Milliseconds 300; $wshell.SendKeys('{TAB}{TAB}{TAB}{TAB}{ENTER}')"
timeout /t 3 /nobreak >nul
echo [GitHub page loaded - may auto-redirect or click 'Continue with Google']

REM Step 4: Create final new tab about:newtab in same window
echo Creating new tab (about:newtab) in same window...
%PS% "$wshell = New-Object -ComObject wscript.shell; $wshell.AppActivate(%EDGEPID%); Start-Sleep -Milliseconds 200; $wshell.SendKeys('^t'); Start-Sleep -Milliseconds 200; $wshell.SendKeys('about:newtab{ENTER}')"
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo Automated session complete!
echo.
echo FLOW:
echo 1. Single InPrivate Edge window created
echo 2. All pages opened as new tabs (Ctrl+T) in that window
echo ========================================
echo.
pause