@echo off
REM Set the path to your Laravel project's root directory
set "LaravelPath=C:\wamp64\server\notifier"

REM Set the PHP and Artisan command you want to run
set "ArtisanCommand=task:run"

REM Change to the Laravel project directory
cd /d "%LaravelPath%"

REM Run the Artisan command
echo Running php artisan %ArtisanCommand%...
php artisan %ArtisanCommand%

REM Check if the command was successful
if %ERRORLEVEL% == 0 (
    echo Artisan command %ArtisanCommand% executed successfully.
) else (
    echo Failed to execute artisan command %ArtisanCommand%.
)

REM Pause the script to see the output