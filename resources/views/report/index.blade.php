@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Report Barrier Gate'])
    <div class="row mt-1 px-1">
        <div class="card">
            <div class="card-header p-0">
                <form action="{{ route('report.index') }}" method="get">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mt-2 overflow-auto">
                            <div class="input-group mb-3">
                                <input type="text" name="date_start" value="{{ $date_start }}" placeholder="Start Date"
                                    autocomplete="off" class="daterangepicker-field form-control text-center">
                                <span class="input-group-text"><i class="fa fa-calendar-days"></i></span>
                                <input type="text" name="date_end" value="{{ $date_end }}" placeholder="End Date"
                                    autocomplete="off" class="daterangepicker-field form-control text-center">
                            </div>
                        </div>

                        <div class="col-md-12 my-2">
                            <a href="{{ route('report.index') }}" class="btn btn-md btn-outline-warning">Refresh</a>
                            <button type="submit" class="btn btn-md btn-outline-primary">Search</button>
                            @if (!empty($date_start) || !empty($date_end))
                                <a href="{{ route('report.index', ['download' => 'pdf', 'date_start' => $date_start, 'date_end' => $date_end]) }}"
                                    class="btn btn-md btn-outline-success" target="_blank">Export
                                    to PDF</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-1">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table my-table my-tablelog my-table-striped w-100">
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
                                            <td class="text-end">{{ number_format($val['count_inbounds'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">{{ number_format($val['count_outbounds'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">{{ number_format($val['count_others'], 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($val['count_gate_1'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">{{ number_format($val['count_gate_2'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-center"><strong>Total</strong></td>
                                        <td class="text-end">{{ number_format($sum_in, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($sum_out, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($sum_oth, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($sum_gate_1, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($sum_gate_2, 0, ',', '.') }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">Data Not Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/plugins/bootstrap-datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datatable/js/dataTables.buttons.min.js') }}"></script>
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.fn.DataTable.ext.pager.numbers_length = 5;

            $("input[name=date_start]").daterangepicker({
                forceUpdate: false,
                single: true,
                timeZone: 'Asia/Jakarta',
                startDate: moment().subtract(1, 'days'),
                periods: ['day', 'week', 'month', 'year'],
                // standalone: true,
                callback: function(start, period) {

                    var title = start.format('YYYY-MM-DD');
                    $(this).val(title)
                }
            });
            $("input[name=date_end]").daterangepicker({
                forceUpdate: false,
                single: true,
                timeZone: 'Asia/Jakarta',
                startDate: moment().subtract(0, 'days'),
                periods: ['day', 'week', 'month', 'year'],
                // standalone: true,
                callback: function(start, period) {

                    var title = start.format('YYYY-MM-DD');
                    $(this).val(title)
                }
            });
        });
    </script>
@endsection
