<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['level_id' => 1, 'kode_level' => 'ADM', 'level_nama' =>'Admin'],
            ['level_id' => 2, 'kode_level' => 'DSN', 'level_nama' =>'Dosen/Tendik'],
            ['level_id' => 3, 'kode_level' => 'MHS', 'level_nama' =>'Mahasiswa'],
            ['level_id' => 4, 'kode_level' => 'KPD', 'level_nama' =>'Kaprodi'],
        ];
        DB::table('m_level')->insert($data);
    }
}
