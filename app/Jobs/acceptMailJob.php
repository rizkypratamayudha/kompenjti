<?php
namespace App\Jobs;

use App\Mail\kirimEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class acceptMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $nama;
    public $prodiNama;
    public $angkatan;
    public $nim;
    public $periodeNama;
    public $email;

    /**
     * Create a new job instance.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->nama = $data['nama'];
        $this->prodiNama = $data['prodi_id'];
        $this->angkatan = $data['angkatan'];
        $this->nim = $data['nim'];
        $this->periodeNama = $data['periode'];
        $this->email = $data['email'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Mengirim email
        Mail::to($this->email)->send(new kirimEmail([
            'nama' => $this->nama,
            'prodi_id' => $this->prodiNama,
            'angkatan' => $this->angkatan,
            'nim' => $this->nim,
            'periode' => $this->periodeNama,
        ]));
    }
}

