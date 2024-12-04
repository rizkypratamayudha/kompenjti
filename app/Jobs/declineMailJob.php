<?php

namespace App\Jobs;

use App\Mail\declineMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class declineMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
        public $nama;
        public $prodiNama;
        public $angkatan;
        public $nim;
        public $periodeNama;
        public $email;

        public $alasan;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->nama = $data['nama'];
        $this->prodiNama = $data['prodi_id'];
        $this->angkatan = $data['angkatan'];
        $this->nim = $data['nim'];
        $this->periodeNama = $data['periode'];
        $this->email = $data['email'];
        $this->alasan = $data['alasan'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new declineMail([
            'nama' => $this->nama,
            'prodi_id' => $this->prodiNama,
            'angkatan' => $this->angkatan,
            'nim' => $this->nim,
            'periode' => $this->periodeNama,
            'alasan' => $this->alasan
        ]));
    }
}
