<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Registrasi</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; }
        .container { max-width: 400px; margin: 20px auto; background: #ffffff; padding: 20px; border: 1px solid #ddd; border-radius: 10px; font-size: 12px; }

        /* Header Section */
        .header { text-align: center; margin-bottom: 10px; }
        .header img { height: 50px; width: auto; max-width: 100px; margin-right: 10px; }
        .header .font-bold { font-weight: bold; font-size: 14px; }
        .header .small-text { font-size: 10px; color: #666; }
        .header-table { width: 100%; margin-bottom: 20px; }
        .header-table td { vertical-align: middle; }

        /* Greeting Section */
        .greeting { font-size: 14px; font-weight: bold; margin-top: 15px; color: #333; }

        /* Notification Section */
        .notification { background-color: #dc2a2a; color: #ffffff; padding: 10px; text-align: center; border-radius: 5px; margin-top: 10px; font-size: 12px; }

        /* Details Section */
        .details { margin-top: 15px; font-size: 12px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 4px; vertical-align: top; }

        /* Message Section */
        .message { font-size: 12px; margin-top: 15px; line-height: 1.4; color: #333; }

        /* Button Section */
        .button { display: flex; justify-content: center; margin-top: 15px; }
        .button a { background-color: #007bff; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 5px; font-size: 12px; }

        /* Footer Section */
        .footer { font-size: 10px; color: #555; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px; display: flex; align-items: center; }
        .footer img { width: 15px; height: 15px; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section with Logo on Left -->
        <table class="header-table">
            <tr>
                <td width="15%" class="text-center">
                    <img src="https://i0.wp.com/www.hpi.or.id/wp-content/uploads/2021/08/Logo-Polinema.png?resize=300%2C300&ssl=1" alt="Polinema Logo" class="image">
                </td>
                <td width="85%">
                    <div class="font-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
                    <div class="font-bold">POLITEKNIK NEGERI MALANG</div>
                    <div class="small-text">Jl. Soekarno-Hatta No. 9 Malang 65141</div>
                    <div class="small-text">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341) 404420</div>
                    <div class="small-text">Laman: www.polinema.ac.id</div>
                </td>
            </tr>
        </table>

        <!-- Greeting Section -->
        <p class="greeting">Halo, {{ $data['nama'] }}</p>

        <!-- Notification Section -->
        <div class="notification">
            Mohon maaf kami menginformasikan bahwa registrasi Anda pada Aplikasi Kompensasi Politeknik Negeri Malang telah ditolak.
        </div>

        <!-- Details Section -->
        <div class="details">
            <table>
                <tr>
                    <td><strong>Nama :</strong></td>
                    <td>{{ $data['nama'] }}</td>
                </tr>
                <tr>
                    <td><strong>NIM / NIP :</strong></td>
                    <td>{{ $data['nim'] }}</td>
                </tr>
                @if ($data['prodi_id'] && $data['angkatan'])
                <tr>
                    <td><strong>Program Studi:</strong></td>
                    <td>{{$data['prodi_id']}}</td>
                </tr>
                <tr>
                    <td><strong>Angkatan:</strong></td>
                    <td>{{$data['angkatan']}}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Message Section -->
        <p class="message">
            Dengan ini, Anda resmi terdaftar sebagai pengguna aplikasi kompensasi kami yang akan mendukung berbagai kebutuhan akademik dan administrasi Anda. Silakan gunakan aplikasi ini untuk mengelola tugas dan keperluan akademik Anda dengan lebih mudah.
        </p>

        <!-- Button Section -->
        <div class="button">
            <a href="#" target="_blank">Kunjungi</a>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <img src="{{ asset('question_icon.png') }}" alt="Question Icon">
            <span>Jika Anda memiliki pertanyaan lebih lanjut atau memerlukan bantuan, jangan ragu untuk menghubungi admin kami dengan nomor berikut: 085606310648.</span>
        </div>
    </div>
</body>
</html>
