<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
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
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center"><img class="image" id="image" src="{{ asset('logo_polinema.jpg') }}">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN
                    TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341)
                    404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">Surat Bukti Kompensasi Jurusan Teknologi Informasi</h3>

    <table>
        <tr>
            <th class="text-right">Nama : </th>
            <td>{{ $penerimaan->user->nama }}</td>
        </tr>
        <tr>
            <th class="text-right">NIM : </th>
            <td>{{ $penerimaan->user->username }}</td>
        </tr>
        <tr>
            <th class="text-right">Prodi : </th>
            <td>{{ $penerimaan->user->detailMahasiswa->prodi->prodi_nama }}</td>
        </tr>
        <tr>
            <th class="text-right">Angkatan : </th>
            <td>{{ $penerimaan->user->detailMahasiswa->angkatan }}</td>
        </tr>
        <tr>
            <th class="text-right">Periode : </th>
            <td>{{ $penerimaan->user->detailMahasiswa->periode->periode_nama }}</td>
        </tr>
    </table>

    <p class="text-center">Dengan adanya bukti surat kompensasi dengan pekerjaan kompen, sebagai berikut: </p>

    <table style="margin-bottom: 40px">
        <tr>
            <th class="text-right">Nama Pekerjaan : </th>
            <td>{{ $penerimaan->pekerjaan->pekerjaan_nama }}</td>
        </tr>
        <tr>
            <th class="text-right">Nama Dosen/Tendik : </th>
            <td>{{ $penerimaan->pekerjaan->user->nama }}</td>
        </tr>
        <tr>
            <th class="text-right">Jenis Pekerjaan : </th>
            <td>{{ $penerimaan->pekerjaan->jenis_pekerjaan }}</td>
        </tr>
        <tr>
            <th class="text-right">Nilai Jam Kompen : </th>
            <td>{{ $penerimaan->pekerjaan->jumlah_jam_kompen }}</td>
        </tr>
    </table>

    <table class="border-all">
        <thead>
            <tr>
                <th>Nama Progres</th>
                <th>Nilai Jam per Progres</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penerimaan->pekerjaan->progres as $progres)
                <tr>
                    <td>{{ $progres->judul_progres }}</td>
                    <td>{{ $progres->jam_kompen }}</td>
                    <td>
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

    <p class="text-center-yang">Yang mengetahui, </p>
    <table style="margin-top: 40px">
        <tr>
            <td>Dosen</td>
            <td class="text-kanan" style="margin-bottom: 40px">Kaprodi</td>
        </tr>
        <tr>
            <td class="">{{$penerimaan->pekerjaan->user->nama}}</td>
            <td class="text-kanan">{{$penerimaan->kaprodi->nama}}</td>
        </tr>
    </table>
    <div>
        <img src="{{ asset('storage/qrcodes/' . $penerimaan->t_approve_cetak_id . '.png') }}" alt="QR Code">
    </div>
</body>

</html>
