@echo off
cd /d C:\OSPanel\domains\helpdesk

git add .

git diff --cached --quiet
if %errorlevel%==0 (
    echo Нет изменений для отправки
    pause
    exit
)

git commit -m "update"
git push origin main

echo Успешно отправлено на GitHub
pause