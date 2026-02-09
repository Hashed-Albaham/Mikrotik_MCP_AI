@echo off
echo STARTING GITHUB UPLOAD PROCESS...
echo ---------------------------------

:: Get current date and time for unique commit message
for /f "tokens=1-4 delims=/ " %%a in ('date /t') do set mydate=%%c-%%b-%%a
for /f "tokens=1-2 delims=: " %%a in ('time /t') do set mytime=%%a-%%b

:: Check if git is installed
where git >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Git is not installed or not in your PATH.
    echo Please install Git from https://git-scm.com/downloads and try again.
    pause
    exit /b
)

:: Check if already a git repo
if exist ".git" (
    echo [INFO] Git repository already exists, creating new version...
    goto :add_files
)

echo [1/5] Initializing Git repository...
git init

:add_files
echo [2/5] Adding files (this might take a moment)...
git add .

echo [3/5] Committing files with timestamp...
set COMMIT_MSG=Update: Mikrotik_MCP Platform - %mydate% %mytime%
echo Commit message: %COMMIT_MSG%
git commit -m "%COMMIT_MSG%"

:: Only set branch and remote if first time
git rev-parse --verify main >nul 2>nul
if %errorlevel% neq 0 (
    echo [4/5] Setting up branch and remote...
    git branch -M main
    git remote remove origin 2>nul
    git remote add origin https://github.com/Hashed-Albaham/Mikrotik_MCP_AI.git
) else (
    echo [4/5] Branch already configured, skipping...
)

echo.
echo ---------------------------------
echo READY TO PUSH!
echo.
echo Repository: https://github.com/Hashed-Albaham/Mikrotik_MCP_AI
echo This will create a NEW version (commit) without overwriting previous ones.
echo.
echo Press any key to start uploading...
pause

echo [5/5] Pushing to GitHub...
git push -u origin main

echo.
echo ---------------------------------
echo DONE! Your new version has been uploaded.
echo All previous versions are preserved in Git history.
echo.
echo To view history: git log --oneline
pause
