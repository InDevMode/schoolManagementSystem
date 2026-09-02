<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Seeder
{
    public const LMC_ID = 'a1b2c3d4-aa01-4000-8000-000000000001';
    public const CSM_ID = 'a1b2c3d4-aa02-4000-8000-000000000002';
    public const EPE_ID = 'a1b2c3d4-aa03-4000-8000-000000000003';

    public function run(): void
    {
        $schools = [
            [
                'id'              => self::LMC_ID,
                'school_name'     => 'Lycée Moderne de Cotonou',
                'school_type'     => 'Lycée',
                'school_code'     => 'lmc-cotonou',
                'address'         => 'Avenue Jean-Paul II, Cotonou, Bénin',
                'phone'           => '+229 97 11 22 33',
                'email'           => 'contact@lmc.bj',
                'uai_number'      => 'BJ-LMC-001',
                'logo'            => 'logo_lmc.png',
                'favicon'         => 'favicon_lmc.png',
                'academic_year'   => '2025-2026',
                'period_type'     => 'trimestre',
                'auth_bg_type'    => 'gradient',
                'auth_bg_value'   => 'linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%)',
                'auth_bg_label'   => 'Lycée Moderne de Cotonou',
                'auth_bg_overlay' => 'rgba(0,0,0,0.3)',
                'paypal_email'    => 'paypal@lmc.bj',
                'paypal_client_id'=> 'paypal-client-lmc-demo',
                'paypal_secret'   => 'paypal-secret-lmc-demo',
                'paypal_mode'     => 'sandbox',
                'stripe_public_key'=> 'pk_test_lmc_demo',
                'stripe_secret_key'=> 'sk_test_lmc_demo',
                'fedapay_public_key'=> 'pk_sandbox_lmc_demo',
                'fedapay_secret_key'=> 'sk_sandbox_lmc_demo',
                'kkiapay_public_key'=> 'tpk_lmc_demo',
                'kkiapay_private_key'=> 'tpvk_lmc_demo',
                'kkiapay_secret_key'=> 'sk_lmc_demo',
                'status'          => 1,
                'is_delete'       => 0,
                'created_by'      => SuperAdminSeeder::SUPER_ADMIN_ID,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => self::CSM_ID,
                'school_name'     => 'Collège Saint-Michel',
                'school_type'     => 'Collège',
                'school_code'     => 'csm-abomey',
                'address'         => 'Rue du Commerce, Abomey-Calavi, Bénin',
                'phone'           => '+229 97 44 55 66',
                'email'           => 'contact@csm.bj',
                'uai_number'      => 'BJ-CSM-002',
                'logo'            => 'logo_csm.png',
                'favicon'         => 'favicon_csm.png',
                'academic_year'   => '2025-2026',
                'period_type'     => 'trimestre',
                'auth_bg_type'    => 'gradient',
                'auth_bg_value'   => 'linear-gradient(135deg, #064e3b 0%, #10b981 100%)',
                'auth_bg_label'   => 'Collège Saint-Michel',
                'auth_bg_overlay' => 'rgba(0,0,0,0.3)',
                'paypal_email'    => 'paypal@csm.bj',
                'paypal_client_id'=> 'paypal-client-csm-demo',
                'paypal_secret'   => 'paypal-secret-csm-demo',
                'paypal_mode'     => 'sandbox',
                'stripe_public_key'=> 'pk_test_csm_demo',
                'stripe_secret_key'=> 'sk_test_csm_demo',
                'fedapay_public_key'=> 'pk_sandbox_csm_demo',
                'fedapay_secret_key'=> 'sk_sandbox_csm_demo',
                'kkiapay_public_key'=> 'tpk_csm_demo',
                'kkiapay_private_key'=> 'tpvk_csm_demo',
                'kkiapay_secret_key'=> 'sk_csm_demo',
                'status'          => 1,
                'is_delete'       => 0,
                'created_by'      => SuperAdminSeeder::SUPER_ADMIN_ID,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => self::EPE_ID,
                'school_name'     => 'École Primaire Les Étoiles',
                'school_type'     => 'École Primaire',
                'school_code'     => 'epe-parakou',
                'address'         => 'Quartier Zongo, Parakou, Bénin',
                'phone'           => '+229 97 77 88 99',
                'email'           => 'contact@epe.bj',
                'uai_number'      => 'BJ-EPE-003',
                'logo'            => 'logo_epe.png',
                'favicon'         => 'favicon_epe.png',
                'academic_year'   => '2025-2026',
                'period_type'     => 'trimestre',
                'auth_bg_type'    => 'gradient',
                'auth_bg_value'   => 'linear-gradient(135deg, #7c2d12 0%, #f97316 100%)',
                'auth_bg_label'   => 'École Primaire Les Étoiles',
                'auth_bg_overlay' => 'rgba(0,0,0,0.3)',
                'paypal_email'    => 'paypal@epe.bj',
                'paypal_client_id'=> 'paypal-client-epe-demo',
                'paypal_secret'   => 'paypal-secret-epe-demo',
                'paypal_mode'     => 'sandbox',
                'stripe_public_key'=> 'pk_test_epe_demo',
                'stripe_secret_key'=> 'sk_test_epe_demo',
                'fedapay_public_key'=> 'pk_sandbox_epe_demo',
                'fedapay_secret_key'=> 'sk_sandbox_epe_demo',
                'kkiapay_public_key'=> 'tpk_epe_demo',
                'kkiapay_private_key'=> 'tpvk_epe_demo',
                'kkiapay_secret_key'=> 'sk_epe_demo',
                'status'          => 1,
                'is_delete'       => 0,
                'created_by'      => SuperAdminSeeder::SUPER_ADMIN_ID,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ];

        foreach ($schools as $school) {
            if (!DB::table('schools')->where('id', $school['id'])->exists()) {
                DB::table('schools')->insert($school);
                $this->command->info("  ✅ {$school['school_name']}");
            } else {
                $this->command->info("  ⏭  {$school['school_name']} déjà présente.");
            }
        }
    }
}
