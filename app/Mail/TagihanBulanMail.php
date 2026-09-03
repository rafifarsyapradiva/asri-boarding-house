<?php

namespace App\Mail;

use App\Models\Tagihan;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TagihanBulanMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    /**
     * Create a new message instance using PHP 8 Property Promotion.
     */
    public function __construct(
        protected Tagihan $tagihan
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Tagihan Sewa Kost Bulan Berjalan - :orderId', [
                'orderId' => $this->tagihan->order_id ?? '-'
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Pastikan relasi penting dimuat saat di-dequeue oleh Queue Worker hanya jika model tersimpan di database
        if ($this->tagihan->exists) {
            $this->tagihan->loadMissing(['penyewa.user', 'penyewa.kamar']);
        }

        $penyewa = $this->tagihan->penyewa;
        
        $nominalPokok = (float) ($this->tagihan->nominal_pokok ?? 0);
        $nominalDenda = (float) ($this->tagihan->nominal_denda ?? 0);
        $nominalTotal = (float) ($this->tagihan->nominal_total ?? 0);
        
        // Kalkulasi nominal deposit secara terisolasi dengan garansi batas bawah nol (non-negative)
        $nominalDeposit = max(0.0, $nominalTotal - $nominalPokok - $nominalDenda);

        return new Content(
            view: 'emails.tagihan-baru',
            with: [
                'namaPenyewa' => $penyewa?->user?->nama ?? 'Penyewa',
                'periodeSewa' => sprintf('%02d/%d', $this->tagihan->periode_bulan ?? 0, $this->tagihan->periode_tahun ?? 0),
                'orderId' => $this->tagihan->order_id ?? '-',
                'nomorKamar' => $penyewa?->kamar?->nomor_kamar ?? '-',
                'nominalPokok' => $nominalPokok,
                'nominalPokokFormatted' => $this->formatRupiah($nominalPokok),
                'nominalDeposit' => $nominalDeposit,
                'nominalDepositFormatted' => $this->formatRupiah($nominalDeposit),
                'nominalDenda' => $nominalDenda,
                'nominalDendaFormatted' => $this->formatRupiah($nominalDenda),
                'nominalTotalFormatted' => $this->formatRupiah($nominalTotal),
                'tanggalJatuhTempoFormatted' => $this->formatTanggal($this->tagihan->tanggal_jatuh_tempo),
                'urlLoginPenyewa' => route('penyewa.login'),
            ],
        );
    }

    /**
     * Helper privat untuk memformat nominal ke mata uang Rupiah.
     */
    private function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Helper privat untuk memformat tanggal jatuh tempo secara aman.
     */
    private function formatTanggal(mixed $date): string
    {
        if ($date instanceof CarbonInterface) {
            return $date->format('d M Y');
        }

        if (is_string($date) && filled($date)) {
            return Carbon::parse($date)->format('d M Y');
        }

        return '-';
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Getter untuk memudahkan pengujian unit (Testability).
     */
    public function getTagihan(): Tagihan
    {
        return $this->tagihan;
    }
}


