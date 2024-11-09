<div class="email-container">
    <h1>Halo, {{ $data['nama'] }}</h1>

    <p>Selamat! Kami dengan senang hati menginformasikan bahwa registrasi Anda pada Aplikasi Kompensasi Politeknik Negeri Malang telah berhasil dan disetujui.</p>

    <h2>Detail Registrasi Anda:</h2>
    <ul>
        <li><strong>Nama:</strong> {{ $data['nama'] }}</li>
        <li><strong>Program Studi:</strong> {{ $data['prodi_id'] }}</li>
        <li><strong>Angkatan:</strong> {{ $data['angkatan'] }}</li>
    </ul>

    <p>Dengan ini, Anda resmi terdaftar sebagai pengguna aplikasi kompensasi yang kami sediakan untuk mendukung kebutuhan akademik dan administrasi Anda. Silakan gunakan aplikasi ini untuk mengelola tugas dan keperluan akademik Anda dengan lebih mudah.</p>

    <p>Jika Anda memiliki pertanyaan lebih lanjut atau memerlukan bantuan, jangan ragu untuk menghubungi tim support kami.</p>

    <a href="#" class="cta-button">Kunjungi Aplikasi</a>

    <div class="email-footer">
        <p>Politeknik Negeri Malang</p>
    </div>
</div>

<style>
    /* Style umum untuk email */
    body {
        font-family: Arial, sans-serif;
        color: #333333;
        line-height: 1.6;
    }

    /* Gaya utama container email */
    .email-container {
        background-color: #f4f4f4;
        padding: 20px;
        border-radius: 8px;
        max-width: 600px;
        margin: 0 auto;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }

    /* Gaya untuk heading utama */
    h1, h2 {
        color: #004080;
    }

    h1 {
        font-size: 24px;
        margin-bottom: 15px;
        text-align: center;
    }

    h2 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    /* Gaya paragraf */
    p {
        font-size: 16px;
        margin: 15px 0;
    }

    /* Gaya untuk list data detail */
    ul {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 8px;
        list-style-type: none;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    ul li {
        font-size: 16px;
        padding: 5px 0;
        border-bottom: 1px solid #e6e6e6;
    }

    ul li:last-child {
        border-bottom: none;
    }

    /* Tombol untuk tindakan lebih lanjut */
    .cta-button {
        display: inline-block;
        background-color: #004080;
        color: #ffffff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        text-align: center;
    }

    .cta-button:hover {
        background-color: #003366;
    }

    /* Footer gaya email */
    .email-footer {
        font-size: 12px;
        color: #666666;
        margin-top: 20px;
        text-align: center;
        border-top: 1px solid #ddd;
        padding-top: 10px;
    }
</style>

