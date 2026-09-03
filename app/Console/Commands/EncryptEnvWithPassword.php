<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Chiffre le fichier .env avec un mot de passe mémorable.
 *
 * La clé AES-256 est dérivée du mot de passe via PBKDF2-SHA256 (600 000 itérations)
 * avec un sel stocké en tête du fichier chiffré, ce qui permet de ne jamais stocker
 * la clé elle-même — seul le mot de passe suffit pour déchiffrer.
 *
 * Format du fichier .env.encrypted produit :
 *   [4 octets magic "EPWD"] [16 octets sel] [fichier chiffré avec env:encrypt de Laravel]
 *
 * Usage :
 *   php artisan env:encrypt-password              (demande le mot de passe en interactif)
 *   php artisan env:encrypt-password --env=staging
 *   php artisan env:encrypt-password --force      (écrase si .env.encrypted existe déjà)
 */
class EncryptEnvWithPassword extends Command
{
    protected $signature = 'env:encrypt-password
                            {--env=       : L\'environnement cible (.env.{env})}
                            {--force      : Écraser le fichier chiffré existant}';

    protected $description = 'Chiffre le fichier .env avec un mot de passe (dérivation PBKDF2-SHA256)';

    /** Marqueur magique pour distinguer nos fichiers des fichiers chiffrés avec --key */
    private const MAGIC = 'EPWD';

    public function handle(): int
    {
        $envFile   = $this->resolveEnvFile();
        $outFile   = $envFile . '.encrypted';

        if (! file_exists($envFile)) {
            $this->error("Fichier introuvable : {$envFile}");
            return self::FAILURE;
        }

        if (file_exists($outFile) && ! $this->option('force')) {
            $this->error("Le fichier {$outFile} existe déjà. Utilisez --force pour l'écraser.");
            return self::FAILURE;
        }

        // Demander le mot de passe (deux fois pour confirmation)
        $password = $this->secret('Entrez le mot de passe de chiffrement');
        if (empty($password)) {
            $this->error('Le mot de passe ne peut pas être vide.');
            return self::FAILURE;
        }

        $confirm = $this->secret('Confirmez le mot de passe');
        if ($password !== $confirm) {
            $this->error('Les mots de passe ne correspondent pas.');
            return self::FAILURE;
        }

        // Générer un sel aléatoire de 16 octets
        $salt = random_bytes(16);

        // Dériver la clé AES-256-CBC (32 octets) via PBKDF2-SHA256
        $key = $this->deriveKey($password, $salt);

        // Appeler env:encrypt de Laravel avec la clé dérivée
        // On passe la clé en base64 comme Laravel l'attend
        $keyB64    = 'base64:' . base64_encode($key);
        $envOption = $this->option('env') ? ['--env' => $this->option('env')] : [];

        $this->call('env:encrypt', array_merge([
            '--key'    => $keyB64,
            '--cipher' => 'AES-256-CBC',
            '--force'  => true,
        ], $envOption));

        // Lire le fichier chiffré produit par Laravel
        if (! file_exists($outFile)) {
            $this->error("Laravel n'a pas produit le fichier {$outFile}.");
            return self::FAILURE;
        }

        $encrypted = file_get_contents($outFile);

        // Réécrire avec le préfixe [MAGIC + SEL] pour pouvoir retrouver le sel au déchiffrement
        // Format : "EPWD" (4 octets) + sel (16 octets) + contenu chiffré Laravel
        file_put_contents($outFile, self::MAGIC . $salt . $encrypted);

        $this->info('');
        $this->info('✔  Fichier chiffré avec succès : ' . $outFile);
        $this->warn('⚠  Ne committez PAS votre mot de passe. Conservez-le en lieu sûr.');
        $this->line('   Pour déchiffrer : php artisan env:decrypt-password');

        return self::SUCCESS;
    }

    /**
     * Dérive une clé AES-256 (32 octets) depuis le mot de passe et le sel via PBKDF2-SHA256.
     * 600 000 itérations = recommandation OWASP 2023.
     */
    private function deriveKey(string $password, string $salt): string
    {
        return hash_pbkdf2('sha256', $password, $salt, 600_000, 32, true);
    }

    private function resolveEnvFile(): string
    {
        $env = $this->option('env');
        $base = base_path();

        return $env ? "{$base}/.env.{$env}" : "{$base}/.env";
    }
}
