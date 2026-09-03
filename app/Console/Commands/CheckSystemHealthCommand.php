<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class CheckSystemHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abh:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validasi real-time status cache, storage views, dan integritas rute ekspor.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 1. VALIDASI CACHE FISIK (Membaca status cache bawaan Laravel)
        $isConfigCached = app()->configurationIsCached();
        $isRouteCached = app()->routesAreCached();

        // 2. VALIDASI STORAGE VIEWS (Kompilasi Blade)
        $viewsPath = storage_path('framework/views');
        $viewFiles = glob($viewsPath . '/*.php');
        $viewCount = is_array($viewFiles) ? count($viewFiles) : 0;

        // 3. VERIFIKASI INTEGRITAS RUTE EKSPOR (Simulasi pencocokan rute riil)
        $exportPdfSafe = $this->isRouteResolutionCorrect('/admin/penyewa/export-pdf', 'admin.penyewa.exportPdf')
            || $this->isRouteResolutionCorrect('/admin/penyewa/exportPdf', 'admin.penyewa.exportPdf');
        $exportCsvSafe = $this->isRouteResolutionCorrect('/admin/penyewa/export-csv', 'admin.penyewa.exportCsv')
            || $this->isRouteResolutionCorrect('/admin/penyewa/exportCsv', 'admin.penyewa.exportCsv');

        $integrityPassed = $exportPdfSafe && $exportCsvSafe;
        $integrityMessage = $integrityPassed 
            ? "<fg=green>✔ INTEGRITY: Rute Ekspor Penyewa berada di posisi aman (Anti-Wildcard Collision Check Passed)</>"
            : "<fg=red>❌ COLLISION: Rute Ekspor Penyewa terancam bertabrakan dengan wildcard resource atau tidak terdaftar!</>";

        // 4. OUTPUT DASHBOARD TERMINAL DEKORATIF
        $this->line("");
        $this->line(" <fg=cyan;options=bold>========================================================================</>");
        $this->line(" <fg=white;options=bold>               ASRI BOARDING HOUSE - SYSTEM HEALTH CHECK                </>");
        $this->line(" <fg=cyan;options=bold>========================================================================</>");
        
        // Panel 1: Validasi Cache Fisik (Melihat lingkungan saat ini)
        $env = strtoupper(app()->environment());
        $this->line(" <fg=yellow;options=bold>[1] VALIDASI STATUS CACHE SISTEM (Env: {$env})</>");
        $this->line(" ------------------------------------------------------------------------");
        
        if ($isConfigCached) {
            $status = app()->environment('local') 
                ? "<fg=yellow>⚠ WARNING: Cache konfigurasi aktif di lokal (Perubahan .env mungkin terabaikan!)</>"
                : "<fg=green>✔ OPTIMIZED: Cache konfigurasi aktif (Sesuai anjuran produksi)</>";
            $this->line("  {$status}");
        } else {
            $status = app()->environment('production')
                ? "<fg=yellow>⚠ WARNING: Cache konfigurasi mati di produksi (Performa suboptimal)</>"
                : "<fg=green>✔ CLEAN: Lapisan cache konfigurasi bersih (Membaca Live Code/Env)</>";
            $this->line("  {$status}");
        }

        if ($isRouteCached) {
            $status = app()->environment('local')
                ? "<fg=yellow>⚠ WARNING: Cache rute aktif di lokal (Perubahan rute web.php mungkin terabaikan!)</>"
                : "<fg=green>✔ OPTIMIZED: Cache rute aktif (Sesuai anjuran produksi)</>";
            $this->line("  {$status}");
        } else {
            $status = app()->environment('production')
                ? "<fg=yellow>⚠ WARNING: Cache rute mati di produksi (Performa suboptimal)</>"
                : "<fg=green>✔ CLEAN: Lapisan cache rute bersih (Membaca Live routes/web.php)</>";
            $this->line("  {$status}");
        }
        $this->line("");

        // Panel 2: Validasi Storage Views
        $this->line(" <fg=yellow;options=bold>[2] VALIDASI STORAGE VIEWS (Kompilasi Blade)</>");
        $this->line(" ------------------------------------------------------------------------");
        if ($viewCount > 0) {
            $this->line("  <fg=blue>ℹ INFO: Terdapat {$viewCount} file render Blade di dalam storage views</>");
        } else {
            $this->line("  <fg=green>✔ CLEAN: Storage views kosong bersih</>");
        }
        $this->line("");

        // Panel 3: Verifikasi Integritas Rute Ekspor
        $this->line(" <fg=yellow;options=bold>[3] VERIFIKASI INTEGRITAS RUTE EKSPOR (Simulasi Pencocokan URL)</>");
        $this->line(" ------------------------------------------------------------------------");
        $this->line("  {$integrityMessage}");
        
        $this->line(" <fg=cyan;options=bold>========================================================================</>");
        $this->line("");

        return Command::SUCCESS;
    }

    /**
     * Memverifikasi apakah router benar-benar mengarahkan URI ke nama rute yang benar.
     */
    private function isRouteResolutionCorrect(string $uri, string $expectedRouteName): bool
    {
        try {
            $request = app('request')->create($uri, 'GET');
            $route = Route::getRoutes()->match($request);
            return $route->getName() === $expectedRouteName;
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
