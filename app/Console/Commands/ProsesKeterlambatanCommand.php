<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProsesKeterlambatanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abh:proses-keterlambatan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa tagihan pending yang melewati jatuh tempo tanggal 10 (Delegasi ke tagihan:proses-keterlambatan)';

    /**
     * Execute the console command.
     * 
     * @deprecated Gunakan command tagihan:proses-keterlambatan secara langsung.
     */
    public function handle(): int
    {
        $this->warn('Warning: Command ini telah didepresiasi. Mengalihkan eksekusi ke tagihan:proses-keterlambatan.');
        return $this->call('tagihan:proses-keterlambatan');
    }
}
