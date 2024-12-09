<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .info-box {
            background-color: #002f6c; /* Warna biru gelap */
            color: white; /* Warna teks putih */
            padding: 10px;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            width: 100%; /* Lebar tabel */
            margin: 10px auto; /* Memusatkan tabel */
            line-height: 1; /* Spasi antarbaris */
            font-size: 9pt;
        }

        .info-box table {
            width: 100%; /* Lebar penuh di dalam kotak */
            border-collapse: collapse;
        }

        .info-box th {
            text-align: left;
            padding-right: 5px;
        }

        .info-box td {
            text-align: left;
        }

        .info-box b {
            font-weight: bold;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-nama {
            text-align: left;
        }

        .text-right th,
        {
        text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-center-yang {
            text-align: left;
            margin-top: 40px
        }

        .text-center h3 {
            text-align: center;
            margin-top: 40px
        }

         /* Align label (first column) consistently across all tables */
         td:nth-child(1), th:nth-child(1) {
            width: 35%; /* Set fixed width for label columns */
            padding-right: 10px; /* Add space between label and value */
        }

        td:nth-child(2), th:nth-child(2) {
            width: 110%; /* Set flexible width for the value columns */
        }

        .p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }

        .flex-container {
            display: flex;
            /* Mengatur elemen dalam satu baris */
            justify-content: space-between;
            /* Menempatkan elemen di kiri dan kanan */
            align-items: center;
            /* Menjaga elemen sejajar secara vertikal */
            margin-top: 20px;
            /* Memberi jarak atas */
        }

        .flex-container div {
            text-align: center;
            /* Teks di dalam elemen dipusatkan */
            flex: 1;
            /* Membagi ruang elemen secara proporsional */
        }

        .flex-container .text-left,
        .flex-container .text-right {
            width: 90%;
        }

        .text-kanan{
            text-align: right
        }

        .text-kiri{
            text-align: left
        }
        
        .status-column {
            width: 30%; /* Adjust width as needed */
        }
    </style>
</head>

<body>
    <div class="info-box">
        <p>Keterangan:</p>
        <p style="margin-bottom: 5px;">Tanda Tangan Elektronik berupa QRCode ini dikeluarkan oleh Jurusan Teknologi Informasi Politeknik Negeri Malang </p>
        <table>
            <tr>
                <th>Penandatanganan oleh</th>
                <td>: {{$penerimaan->kaprodi->nama}}</td>
            </tr>
            <tr>
                <th>Kepala Program Studi</th>
                <td>: {{ $penerimaan->user->detailMahasiswa->prodi->prodi_nama }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>: {{ $penerimaan->created_at->format('M j Y h:i:s:000A') }}</td>
            </tr>
            <tr>
                <th>Isi Dokumen</th>
                <td>: Berita Acara Kompensasi Presensi Mahasiswa atas nama <b>{{ $penerimaan->user->nama }}</b></td>
            </tr>
            <tr>
                <th>Periode</th>
                <td>: {{ $penerimaan->user->detailMahasiswa->periode->periode_nama }}</td>
            </tr>
        </table>
    </div>

    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center"><img class="image" id="image" src="{{ asset('logo_polinema.jpg') }}">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-12 font-bold mb-1"><b>POLITEKNIK NEGERI MALANG</b></span>
                <span class="text-center d-block font-13 font-bold mb-1"><b>JURUSAN TEKNOLOGI INFORMASI</b></span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">https://www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center" style="margin-bottom: 30px;"> BERITA ACARA KOMPENSASI PRESENSI </h3>

    <table>
        <tr>
            <th>Nama Pengajar</th>
            <td>: {{ $penerimaan->pekerjaan->user->nama }}</td>
        </tr>
        <tr>
            <th>NIP</th>
            <td>: {{ $penerimaan->pekerjaan->user->username }}</td>
        </tr>
    </table>
    

    <p>Memberikan rekomendasi kompensasi kepada: </p>
    <table>
        <tr>
            <th>Nama Mahasiswa</th>
            <td>: {{ $penerimaan->user->nama }}</td>
        </tr>
        <tr>
            <th>NIM</th>
            <td>: {{ $penerimaan->user->username }}</td>
        </tr>
        <tr>
            <th>Program Studi</th>
            <td>: {{ $penerimaan->user->detailMahasiswa->prodi->prodi_nama }}</td>
        </tr>
        <tr>
            <th>Angkatan</th>
            <td>: {{ $penerimaan->user->detailMahasiswa->angkatan }}</td>
        </tr>
        <tr>
            <th>Periode</th>
            <td>: {{ $penerimaan->user->detailMahasiswa->periode->periode_nama }}</td>
        </tr>
    </table>

    <p>Dengan adanya bukti kompensasi dengan pekerjaan kompen, sebagai berikut: </p>

    <table>
        <tr>
            <th>Nama Pekerjaan</th>
            <td>: {{ $penerimaan->pekerjaan->pekerjaan_nama }}</td>
        </tr>
        <tr>
            <th>Jenis Pekerjaan</th>
            <td>: {{ $penerimaan->pekerjaan->jenis_pekerjaan }}</td>
        </tr>
        <tr>
            <th>Nilai Jam Kompen</th>
            <td>: {{ $penerimaan->pekerjaan->jumlah_jam_kompen }}</td>
        </tr>
    </table>

    <p>Detail Progress Pengerjaan: </p>
    <table class="border-all">
        <thead>
            <tr>
                <th>Nama Progres</th>
                <th>Nilai Jam per Progres</th>
                <th class="status-column">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penerimaan->pekerjaan->progres as $progres)
                <tr>
                    <td>{{ $progres->judul_progres }}</td>
                    <td>{{ $progres->jam_kompen }}</td>
                    <td class="status-column">
                        @php
                            // Cari pengumpulan yang terkait dengan progres ini
                            $status = $pengumpulan->firstWhere('progres_id', $progres->progres_id);
                        @endphp
                        @if ($status && $status->status == 'accept')
                            Selesai
                        @elseif ($status && $status->status == 'decline')
                            Tidak selesai
                        @else
                            Belum Ada
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 20px">
        <tr>
            <td></td>
            <td class="text-kanan" >Malang, {{ $penerimaan->created_at->translatedFormat('d F Y') }} </td>
        </tr>
        <tr>
            <td class="text-kiri" >Yang Memberikan Rekomendasi, </td>
            <td class="text-kanan" >Kepala Program Studi</td>
        </tr>
    </table>
    <table style="margin-top: 50px">
        <tr>
            <td class="text-kiri">{{ $penerimaan->pekerjaan->user->nama }}</td>
            <td class="text-kanan">{{ $penerimaan->kaprodi->nama }}</td>
        </tr>
        <tr>
            <td class="text-kiri">NIP. {{ $penerimaan->pekerjaan->user->username }}</td>
            <td class="text-kanan">NIP. {{ $penerimaan->kaprodi->username }}</td>
        </tr>
    </table>

</body>

</html>
