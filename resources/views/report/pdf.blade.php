<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report Barrier Gate</title>
    <link id="pagestyle" rel="stylesheet" href="{{ public_path('assets/css/bootstrap.min.css') }}" />

    <style>
        body,
        .my-table {
            font-family: 'Times New Roman', Times, sans-serif;
        }

        .my-table {
            page-break-before: avoid;
            /* page-break-after: always; */
        }

        h2 {
            font-size: 18px;
            text-align: center;
        }

        .my-table {
            border-collapse: collapse;
            width: 100%;
        }

        .my-table th {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .my-table td {
            border: 1px solid #000;
            padding: 3px 4px;
        }

        .my-table tr th {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            line-height: 1;
        }

        .my-table tr td:nth-child(1) {
            text-align: center;
        }

        .my-table tr td {
            text-align: justify;
            font-size: 14px;
            word-spacing: -2px;
            line-height: 1.1;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <center>
        <h4>Report Barrier Gate</h4>
    </center>

    <table class="my-table ">
        <thead>
            <tr>
                <th>No</th>
                <th>Arrival Date</th>
                <th>Inbound</th>
                <th>Outbound</th>
                <th>Other</th>
                <th>Open Gate 1</th>
                <th>Open Gate 2</th>
            </tr>
        </thead>
        <tbody>
            @if (count($report) > 0)
                @php
                    $sum_in = 0;
                    $sum_out = 0;
                    $sum_oth = 0;
                    $sum_gate_1 = 0;
                    $sum_gate_2 = 0;
                @endphp
                @foreach ($report as $key => $val)
                    @php
                        $sum_in += $val['count_inbounds'];
                        $sum_out += $val['count_outbounds'];
                        $sum_oth += $val['count_others'];
                        $sum_gate_1 += $val['count_gate_1'];
                        $sum_gate_2 += $val['count_gate_2'];
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $val['arrival_date'] }}</td>
                        <td class="text-right">{{ number_format($val['count_inbounds'], 0, ',', '.') }}
                        </td>
                        <td class="text-right">{{ number_format($val['count_outbounds'], 0, ',', '.') }}
                        </td>
                        <td class="text-right">{{ number_format($val['count_others'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($val['count_gate_1'], 0, ',', '.') }}
                        </td>
                        <td class="text-right">{{ number_format($val['count_gate_2'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-center"><strong>Total</strong></td>
                    <td class="text-right">{{ number_format($sum_in, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sum_out, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sum_oth, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sum_gate_1, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sum_gate_2, 0, ',', '.') }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="7" class="text-center">Data Not Found</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>

</html>
