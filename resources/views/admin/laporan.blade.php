<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Reservasi dan Pemasukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .row.mb-4 {
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        small {
            font-size: 0.9rem;
            color: #666;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: #495057;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td b {
            font-weight: 600;
            color: #007bff;
        }

        .table-footer {
            font-weight: 600;
            font-size: 1.2rem;
            color: #333;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            float: right;
        }

        .btn-print:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h4>Laporan Reservasi dan Pemasukan</h4>
                <small>from <b>{{ date('D, d M Y', strtotime(request('dari'))) }}</b> to <b>{{ date('D, d M Y', strtotime(request('sampai'))) }}</b></small>
            </div>
        </div>
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Reservasi</th>
                        <th>Alat</th>
                        <th>Penyewa</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('D, d M Y', strtotime($item->tanggal)) }}</td>
                        <td>{{ $item->nama_alat }}</td>
                        <td>{{ $item->name }}</td>
                        <td style="text-align: right"><b>@money($item->harga)</b></td>
                    </tr>
                    @endforeach
                    <tr class="table-footer">
                        <td colspan="4" class="text-end"><strong>Total Pemasukan</strong></td>
                        <td style="text-align: right"><b>@money($total)</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="btn-print" onclick="window.print()">Print Laporan</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
