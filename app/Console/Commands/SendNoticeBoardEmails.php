<?php

namespace App\Console\Commands;

use App\Mail\NoticeBoardMail;
use App\Models\CommunicateModel;
use App\Models\NoticeBoardMessageModel;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNoticeBoardEmails extends Command
{
    /**
     * Nom et signature de la commande Artisan.
     */
    protected $signature = 'noticeboard:send-emails';

    /**
     * Description affichée dans php artisan list.
     */
    protected $description = 'Active les notifications dont la notice_date est aujourd\'hui ou passée et envoie les emails aux destinataires.';

    public function handle(): int
    {
        $today = now()->toDateString();

        /*
         * On cherche les notifications qui :
         *  - ne sont pas supprimées
         *  - dont la notice_date est <= aujourd'hui  (date d'envoi atteinte)
         *  - dont la publish_date est <= aujourd'hui (date de publication atteinte)
         *  - dont l'email n'a pas encore été envoyé  (email_sent_at IS NULL)
         *  - qui sont actives (is_active = 1)  OU qui ne l'ont pas encore été activées
         *    (le cron les activera lui-même si publish_date est atteinte)
         */
        $notices = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.is_delete', 0)
            ->whereNull('communicates.email_sent_at')
            ->whereDate('communicates.notice_date', '<=', $today)
            ->whereDate('communicates.publish_date', '<=', $today)
            ->get();

        if ($notices->isEmpty()) {
            $this->info('Aucune notification à traiter.');
            return self::SUCCESS;
        }

        $this->info("Traitement de {$notices->count()} notification(s)…");

        foreach ($notices as $notice) {
            // 1. Activer la notification dans l'interface si ce n'est pas encore fait
            if (! $notice->is_active) {
                $notice->is_active = 1;
                $notice->save();
                $this->line("  ✓ Notification #{$notice->id} activée.");
            }

            // 2. Récupérer les groupes destinataires (user_type IDs)
            $recipientTypes = NoticeBoardMessageModel::where('communicates_id', $notice->id)
                ->pluck('message_to')
                ->map(fn ($v) => (int) $v)
                ->unique()
                ->toArray();

            if (empty($recipientTypes)) {
                $this->warn("  ⚠ Notification #{$notice->id} sans destinataires — ignorée pour l'envoi email.");
                // On marque quand même comme envoyé pour ne pas retenter à l'infini
                $notice->email_sent_at = now();
                $notice->save();
                continue;
            }

            // 3. Récupérer tous les utilisateurs des groupes sélectionnés
            $users = User::whereIn('user_type', $recipientTypes)
                ->where('is_delete', 0)
                ->where('status', 1)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            $sent = 0;
            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new NoticeBoardMail($notice, $user));
                    $sent++;
                } catch (\Exception $e) {
                    Log::error("NoticeBoardMail: échec envoi à {$user->email} pour notice #{$notice->id} — " . $e->getMessage());
                    $this->warn("  ✗ Échec email vers {$user->email} : " . $e->getMessage());
                }
            }

            // 4. Marquer comme envoyé
            $notice->email_sent_at = now();
            $notice->save();

            $this->info("  ✓ Notification #{$notice->id} — {$sent} email(s) envoyé(s).");
        }

        $this->info('Traitement terminé.');
        return self::SUCCESS;
    }
}
