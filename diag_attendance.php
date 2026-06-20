<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$typeMap = ['present' => 'present', 'late' => 'late', 'absent' => 'absent', 'half_day' => 'halfday'];

echo "=== Présences par type (string) ===\n";
foreach (['present','late','absent','half_day'] as $t) {
    $c = DB::table('attendances')->where('attendance_type', $t)->where('is_delete', 0)->count();
    echo "  $t: $c\n";
}

echo "\n=== Présences par école ===\n";
$rows = DB::table('attendances')
    ->join('users', 'users.id', '=', 'attendances.student_id')
    ->join('schools', 'schools.id', '=', 'users.school_id')
    ->where('attendances.is_delete', 0)
    ->where('users.user_type', 3)
    ->where('schools.is_delete', 0)
    ->selectRaw('schools.school_name, attendances.attendance_type as type, COUNT(*) as total')
    ->groupBy('schools.school_name', 'attendances.attendance_type')
    ->get();
foreach ($rows as $r) {
    echo "  [{$r->school_name}] {$r->type}: {$r->total}\n";
}

echo "\n=== Présences par mois (année courante) ===\n";
$rows2 = DB::table('attendances')
    ->where('is_delete', 0)
    ->whereYear('attendance_date', date('Y'))
    ->selectRaw('attendance_type, MONTH(attendance_date) as month, COUNT(*) as total')
    ->groupBy('attendance_type', DB::raw('MONTH(attendance_date)'))
    ->get();
foreach ($rows2 as $r) {
    echo "  {$r->attendance_type} mois={$r->month}: {$r->total}\n";
}
