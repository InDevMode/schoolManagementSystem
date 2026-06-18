@echo off
:: Ce fichier lance le scheduler Laravel toutes les minutes.
:: A configurer dans le Planificateur de taches Windows pour s'executer toutes les minutes.
:: Ou lancer manuellement dans un terminal pour les tests locaux.

:loop
php artisan schedule:run >> storage\logs\scheduler.log 2>&1
timeout /t 60 /nobreak >nul
goto loop
