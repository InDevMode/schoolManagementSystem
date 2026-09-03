<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Déchiffre un fichier .env.encrypted chiffré avec env:encrypt-password.
 *
 * Lit le sel stocké en tête du fichier, dérive la même clé AES-256 depuis le
 * mot de passe saisi, puis délègue le déchiffrement à env:decrypt de Laravel.
 *
 * Usage :
 *   php artisan env:decrypt-password              (déchiffre vers .env)
 *   php artisan env:decrypt-password --env=staging
 *   php artisan env:decrypt-password --force      (écrase .env si il existe déjà)
 */
class DecryptEnvWithPassword extends Command
{
    protected $signature = 'env:decrypt-password
                            {--env=       : L\'environnement cible (.env.{env})}
                            {--force      : Écraser le fichier .env existant}';

    protected $description = 'Déchiffre le fichier .env.encrypted protégé par mot de passe';

    private const MAGIC  = 'EPWD';
    private const HEADER = 20; // 4 octets magic + 16 octets sel

    public function handle(): int
    {
        $envFile  = $this->resolveEnvFile();
        $srcFile  = $envFile . '.encrypted';

        if (! file_exists($srcFile)) {
            $this->error("Fichier introuvable : {$srcFile}");
            return self::FAILURE;
        }

        // Lire et valider le fichier chiffré
        $raw = file_get_contents($srcFile);

        if (strlen($raw) < self::HEADER) {
            $this->error("Le fichier {$srcFile} est invalide ou trop court.");
            return self::FAILURE;
        }

        $magic = substr($raw, 0, 4);

        if ($magic !== self::MAGIC) {
            $this->error(
                "Ce fichier n'a pas été chiffré avec env:encrypt-password.\n" .
                "Utilisez php artisan env:decrypt --key=<votre_clé> à la place."
            );
            return self::FAILURE;
        }

        // Extraire le sel (octets 4–19) et le contenu chiffré (octet 20+)
        $salt      = substr($raw, 4, 16);
        $encrypted = substr($raw, self::HEADER);

        // Demander le mot de passe
        $password = $this->secret('Entrez le mot de passe de déchiffrement');
        if (empty($password)) {
            $this->error('Le mot de passe ne peut pas être vide.');
            return self::FAILURE;
        }

        // Dériver la même clé AES-256 que lors du chiffrement
        $key    = $this->deriveKey($password, $salt);
        $keyB64 = 'base64:' . base64_encode($key);

        // Remettre temporairement le fichier chiffré sans le préfixe sel
        // pour que env:decrypt de Laravel puisse le lire normalement
        $tmpFile = $srcFile . '.tmp';
        file_put_contents($tmpFile, $encrypted);

        // Renommer temporairement pour que env:decrypt trouve le bon nom
        rename($srcFile, $srcFile . '.bak');
        rename($tmpFile, $srcFile);

        $forceOption = $this->option('force') ? ['--force' => true] : [];
        $envOption   = $this->option('env') ? ['--env' => $this->option('env')] : [];

        try {
            $exitCode = $this->call('env:decrypt', array_merge([
                '--key'    => $keyB64,
                '--cipher' => 'AES-256-CBC',
            ], $forceOption, $envOption));
        } catch (\Exception $e) {
            $exitCode = self::FAILURE;
            $this->error('Erreur lors du déchiffrement : ' . $e->getMessage());
        }

        // Restaurer le fichier chiffré original (avec préfixe sel)
        rename($srcFile, $tmpFile);
        rename($srcFile . '.bak', $srcFile);
        @unlink($tmpFile);

        if ($exitCode === self::SUCCESS || $exitCode === 0) {
            if (file_exists($envFile)) {
                $this->info('');
                $this->info('✔  Fichier déchiffré avec succès : ' . $envFile);
            } else {
                $this->error('Le mot de passe est incorrect ou le fichier est corrompu.');
                return self::FAILURE;
            }
        } else {
            $this->error('Échec du déchiffrement. Vérifiez votre mot de passe.');
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function deriveKey(string $password, string $salt): string
    {
        return hash_pbkdf2('sha256', $password, $salt, 600_000, 32, true);
    }

    private function resolveEnvFile(): string
    {
        $env  = $this->option('env');
        $base = base_path();

        return $env ? "{$base}/.env.{$env}" : "{$base}/.env";
    }
}
