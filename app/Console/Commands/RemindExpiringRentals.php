<?php

namespace App\Console\Commands;

use App\Mail\RentalExpiringSoonMail;
use App\Models\Sewa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RemindExpiringRentals extends Command
{
    protected $signature = 'rentals:remind-expiring';

    protected $description = 'Kirim email pengingat ke penyewa yang masa sewanya akan berakhir dalam 7 hari';

    public function handle(): int
    {
        $targetDate = now()->addDays(7)->toDateString();

        $rentals = Sewa::query()
            ->where('status', 'active')
            ->whereDate('tanggal_selesai', $targetDate)
            ->whereNull('pengingat_terkirim_pada')
            ->with('reservation.penyewa')
            ->get();

        foreach ($rentals as $rental) {
            $penyewa = $rental->reservation->penyewa;

            if ($penyewa?->email) {
                Mail::to($penyewa->email)->send(new RentalExpiringSoonMail($rental));
            }

            $rental->update(['pengingat_terkirim_pada' => now()]);
        }

        $this->info("Terkirim {$rentals->count()} email pengingat perpanjangan sewa.");

        return self::SUCCESS;
    }
}
