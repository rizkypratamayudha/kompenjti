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
        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-table .image {
            height: 60px;
            width: auto;
        }

        .header-table .font-bold {
            font-weight: bold;
            font-size: 14px;
            font-family: 'Times New Roman', Times, serif;
        }

        .header-table .small-text {
            font-size: 12px;
            color: #666;
            font-family: 'Times New Roman', Times, serif;
        }

        /* Greeting Section */
        .greeting { font-size: 14px; font-weight: bold; margin-top: 15px; color: #333; }

        /* Notification Section */
        .notification { background-color: #dc3545; color: #ffffff; padding: 10px; text-align: center; border-radius: 5px; margin-top: 10px; font-size: 12px; }

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

        /* Responsive Styling */
        @media (max-width: 480px) {
            .container { padding: 15px; font-size: 11px; }
            .header-table .image { height: 50px; }
            .header-table .font-bold { font-size: 12px; }
            .header-table .small-text { font-size: 10px; }
            .greeting { font-size: 12px; }
            .notification { font-size: 11px; padding: 8px; }
            .message { font-size: 11px; }
            .button a { padding: 6px 15px; font-size: 11px; }
            .footer { font-size: 9px; }
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="15%" style="text-align: center; padding-right: 10px;">
                <img src="https://i0.wp.com/www.hpi.or.id/wp-content/uploads/2021/08/Logo-Polinema.png?resize=300%2C300&ssl=1" alt="Polinema Logo" class="image">
            </td>
            <td width="85%" style="text-align: center;">
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
        Maaf, kami harus memberitahukan bahwa registrasi Anda pada Aplikasi Kompensasi Politeknik Negeri Malang tidak disetujui.
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
        
        <!-- Logika untuk menampilkan Program Studi dan Angkatan -->
        @if ($data['prodi_id'] && $data['angkatan'])
        <tr>
            <td><strong>Program Studi:</strong></td>
            <td>{{ $data['prodi_id'] }}</td>
        </tr>
        <tr>
            <td><strong>Angkatan:</strong></td>
            <td>{{ $data['angkatan'] }}</td>
        </tr>
        
        <!-- Jika hanya Program Studi yang ada, tampilkan Nama, NIM/NIP dan Program Studi -->
        @elseif ($data['prodi_id'])
        <tr>
            <td><strong>Program Studi:</strong></td>
            <td>{{ $data['prodi_id'] }}</td>
        </tr>
        @endif
    </table>
</div>


    <!-- Message Section -->
    <p class="message">
        Mohon maaf atas ketidaknyamanan ini. Jika Anda merasa ini adalah kesalahan atau memiliki pertanyaan lebih lanjut, Anda dapat menghubungi tim admin kami.
    </p>

    <!-- Button Section -->
    <div class="button">
        <a href="#" target="_blank">Hubungi Kami</a>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <img src="{{ asset('https://i.pinimg.com/474x/99/06/48/9906489d84b3238c8e79a18a93c13410.jpg') }}" alt="Question Icon">
        <span>Jika Anda memiliki pertanyaan lebih lanjut atau memerlukan bantuan, jangan ragu untuk menghubungi admin kami dengan nomor berikut: 085606310648.</span>
    </div>
</body>
</html>
