@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @if (Request::is('full_table*'))
        @include('layouts.navbars.auth.topnav', ['title' => 'Barrier Gate Truck Scale'])
    @else
        @include('layouts.navbars.auth.topnav', ['title' => 'Realtime Monitor'])
    @endif

    <div class="row mt-1 px-1">
        <div class="card">
            <div class="card-header p-0">
                @if (!Request::is('full_table*'))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date & Time</label>
                                        <!--digital clock start-->
                                        <div class="datetime">
                                            <div class="date">
                                                <span id="dayname">Day</span>,
                                                <span id="month">Month</span>
                                                <span id="daynum">00</span>,
                                                <span id="year">Year</span>
                                            </div>
                                            <div class="time">
                                                <span id="hour">00</span>:
                                                <span id="minutes">00</span>:
                                                <span id="seconds">00</span>
                                                <span id="period" class="d-none">AM</span>
                                            </div>
                                        </div>
                                        <!--digital clock end-->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Mode Controller</label>
                                        <input type="text" readonly class="form-control text-center text-cm api_val_cm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Controller Connection</label>
                                        <input type="text" readonly class="form-control text-center text-cc api_val_cc">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Weight Indicator</label>
                                        <input type="text" readonly
                                            class="form-control text-center text-weight api_val_w">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Sensor 1 Barrier 1</label>
                                                <input type="text" readonly
                                                    class="form-control text-center text-sensor api_sen1_bar1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Sensor 2 Barrier 1</label>
                                                <input type="text" readonly
                                                    class="form-control text-center text-sensor api_sen2_bar1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Sensor 1 Barrier 2</label>
                                                <input type="text" readonly
                                                    class="form-control text-center text-sensor api_sen1_bar2">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Sensor 2 Barrier 2</label>
                                                <input type="text" readonly
                                                    class="form-control text-center text-sensor api_sen2_bar2">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'spv')
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <a href="javascript:void(0)"
                                                class="btn btn-lg form-control btn-outline-primary btn-open"
                                                data-val="BG1">Open
                                                Barier 1</a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <a href="javascript:void(0)"
                                                class="btn btn-lg form-control btn-outline-primary btn-open"
                                                data-val="BG2">Open
                                                Barier 2</a>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Barrier Gate 1</label>
                                        <input type="text" readonly class="form-control text-center text-cc api_bg_1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Barrier Gate 2</label>
                                        <input type="text" readonly class="form-control text-center text-cc api_bg_2">
                                    </div>
                                </div>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'spv')
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <a href="javascript:void(0)"
                                                class="btn btn-lg form-control btn-outline-danger btn-closebg"
                                                data-val="BG1" data-type="close">Close
                                                Barier 1</a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <a href="javascript:void(0)"
                                                class="btn btn-lg form-control btn-outline-danger btn-closebg"
                                                data-val="BG2" data-type="close">Close
                                                Barier 2</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for=""></label>
                                        <select name="type_scenario" class="ts-bg">
                                            <option value="">Search</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for=""></label>
                                        <select name="status" class="sts-bg">
                                            <option value="">Search</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                @endif
            </div>
            <div class="card-body p-1">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        @if (Request::is('full_table*'))
                            <table class="table my-table my-tableonly my-table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>Orders</th>
                                        <th>Type Scenario</th>
                                        <th>Scales One</th>
                                        <th>Scales Two</th>
                                        <th>Status</th>
                                        <th>Tracking Status</th>
                                    </tr>
                                </thead>
                            </table>
                        @else
                            <table class="table my-table my-tableview my-table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>Orders</th>
                                        <th>Type Scenario</th>
                                        <th>Scales One</th>
                                        <th>Scales Two</th>
                                        <th>Status</th>
                                        <th>Tracking Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <audio id="open-gate-1" src="{{ asset('audio/open-gate-1.wav') }}"></audio>
    <audio id="open-gate-2" src="{{ asset('audio/open-gate-2.wav') }}"></audio>
    <audio id="close-gate-1" src="{{ asset('audio/close-gate-1.wav') }}"></audio>
    <audio id="close-gate-2" src="{{ asset('audio/close-gate-2.wav') }}"></audio>
@endsection
@section('script')
    <script src="{{ asset('assets/plugins/bootstrap-datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datatable/js/dataTables.buttons.min.js') }}"></script>

    @if (Request::is('full_table*'))
        <script>
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let auto;
                $(".btn-pause").click(function(e) {
                    e.preventDefault();
                    $(".btn-pause").hide();
                    $(".btn-play").show();
                    auto = false;
                });

                $(".btn-play").click(function(e) {
                    e.preventDefault();
                    $(".btn-pause").show();
                    $(".btn-play").hide();
                    auto = true;
                });

                const Http = window.axios;
                const Echo = window.Echo;
                let channel = Echo.channel('channel-barier');
                channel.listen('BarierGateEvent', function(data) {
                    let status = data.message.status;
                    let action = data.message.action;
                    let truck_no = data.message.truck_no != null ? data.message.truck_no.replaceAll(" ", "")
                        .split("") : null;
                    if (action == 'open gate 1' || action == 'open gate 2') {
                        let status_bg;
                        if (action == 'open gate 1') {
                            status_bg = "open-gate-1";
                        } else if (action == 'open gate 2') {
                            status_bg = "open-gate-2";
                        }

                        let audioFiles = [];
                        let currentAudioIndex = 0;
                        if (truck_no) {
                            audioFiles.push(`/audio/plat-nomor.mp3`);
                            for (let i = 0; i < truck_no.length; i++) {
                                audioFiles.push(`/audio/${truck_no[i]}.mp3`);
                            }
                            audioFiles.push(`/audio/${status_bg}.wav`);
                            audioFiles.push(``);
                        } else {
                            audioFiles.push(`/audio/${status_bg}.wav`);
                            audioFiles.push(``);
                        }

                        function playAudio() {
                            const audio = new Audio(audioFiles[currentAudioIndex]);
                            // Move to the next audio file in the array
                            currentAudioIndex = (currentAudioIndex + 1) % audioFiles.length;

                            if (currentAudioIndex == 0) {
                                audio.pause();
                                audio.currentTime = 0;
                            } else {
                                if (auto) {
                                    audio.play();
                                } else {
                                    audio.pause();
                                    audio.currentTime = 0;
                                }

                                // audio.play();
                            }

                            // Listen for the 'ended' event to play the next audio
                            audio.addEventListener('ended', playAudio);
                        }


                        playAudio();
                    }

                    $('.my-table').DataTable().ajax.reload();
                });

                $.fn.DataTable.ext.pager.numbers_length = 5;
                let table = $('.my-table').DataTable({
                    processing: false,
                    serverSide: true,
                    searching: false,
                    info: false,
                    ordering: false,
                    paging: true,
                    pageLength: 5,
                    bLengthChange: false,
                    pagingType: 'full_numbers',
                    scrollX: true,
                    scrollY: "85vh",
                    ajax: '{{ route('full_table') }}',
                    oLanguage: {
                        oPaginate: {
                            sNext: '<span class="fas fa-angle-right pgn-1" style="color: #5e72e4"></span>',
                            sPrevious: '<span class="fas fa-angle-left pgn-2" style="color: #5e72e4"></span>',
                            sFirst: '<span class="fas fa-angle-double-left pgn-3" style="color: #5e72e4"></span>',
                            sLast: '<span class="fas fa-angle-double-right pgn-4" style="color: #5e72e4"></span>',
                        }
                    },
                    columns: [{
                            data: 'orders',
                        },
                        {
                            data: 'scenario',
                        },
                        {
                            data: 'scale_1'
                        },
                        {
                            data: 'scale_2'
                        },
                        {
                            data: 'status_bg'
                        },
                        {
                            data: 'track_status'
                        },

                    ],
                    columnDefs: [{
                        defaultContent: "-",
                        targets: "_all"
                    }],
                });

                // Auto next page
                setInterval(function() {
                    var info = table.page.info();
                    var pageNum = (info.page + 1 < info.pages) ? info.page + 1 : 0;
                    table.page(pageNum).draw(false);
                }, 15000);
            });
        </script>
    @else
        <script>
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let auto;
                $(".btn-pause").click(function(e) {
                    e.preventDefault();
                    $(".btn-pause").hide();
                    $(".btn-play").show();
                    auto = false;
                });

                $(".btn-play").click(function(e) {
                    e.preventDefault();
                    $(".btn-pause").show();
                    $(".btn-play").hide();
                    auto = true;
                });

                let interval = null;
                let type_scenario = "";
                let next_status = "";

                start();

                initClock();

                $(document).on("click", ".btn-delete-bg", function(e) {
                    e.preventDefault();
                    let plant = $(this).data('plant');
                    let sequence = $(this).data('seq');
                    let arrival_date = $(this).data('date');

                    // console.log([plant, sequence, arrival_date]);

                    $.confirm({
                        title: "Confirmation",
                        content: `Are you sure, you will delete the data with plant <strong>${plant}</strong>, sequence <strong>${sequence}</strong> and arrival date <strong>${arrival_date}</strong>?`,
                        theme: 'bootstrap',
                        columnClass: 'medium',
                        typeAnimated: true,
                        buttons: {
                            hapus: {
                                text: 'Submit',
                                btnClass: 'btn-red',
                                action: function() {
                                    $.ajax({
                                        type: "post",
                                        url: '{{ route('delete.bg') }}',
                                        data: {
                                            plant: plant,
                                            sequence: sequence,
                                            arrival_date: arrival_date
                                        },
                                        dataType: "json",
                                        success: function(res) {
                                            if (res.status == 'success') {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success!',
                                                    text: res.message,
                                                }).then(function() {
                                                    $('.my-table').DataTable()
                                                        .ajax
                                                        .reload();
                                                });
                                                return;
                                            } else {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Oops...',
                                                    text: res.message,
                                                }).then(function() {
                                                    $('.my-table').DataTable()
                                                        .ajax
                                                        .reload();
                                                });
                                                return;
                                            }
                                        }
                                    });
                                }
                            },
                            close: function() {}
                        }
                    });
                });

                $(document).on("click", ".btn-plat", function(e) {
                    e.preventDefault();
                    let plat = $(this).data('plat');
                    let truck_no = plat != null ? plat.replaceAll(" ", "").split("") : null;
                    let audioFiles = [];
                    let currentAudioIndex = 0;

                    if (plat) {
                        audioFiles.push(`/audio/plat-nomor.mp3`);
                        for (let i = 0; i < truck_no.length; i++) {
                            audioFiles.push(`/audio/${truck_no[i]}.mp3`);
                        }
                        audioFiles.push(``);

                        function playAudio() {
                            const audio = new Audio(audioFiles[currentAudioIndex]);
                            // Move to the next audio file in the array
                            currentAudioIndex = (currentAudioIndex + 1) % audioFiles.length;

                            if (currentAudioIndex == 0) {
                                audio.pause();
                                audio.currentTime = 0;
                            } else {
                                if (auto) {
                                    audio.play();
                                } else {
                                    audio.pause();
                                    audio.currentTime = 0;
                                }
                            }

                            // Listen for the 'ended' event to play the next audio
                            audio.addEventListener('ended', playAudio);
                        }

                        playAudio();
                    }
                });

                $('.ts-bg').change(function(e) {
                    e.preventDefault();
                    let val = $(this).val();
                    type_scenario = val;
                    let url_ts = '{{ route('full_page', ':id') }}';
                    url_ts = url_ts.replace(':id',
                        `?type_scenario=${type_scenario}&next_status=${next_status}`);
                    $('.my-table').DataTable().ajax.url(url_ts).load();
                });

                $(`.ts-bg`).select2({
                    placeholder: 'Search type scenario',
                    width: "100%",
                    allowClear: true,
                    ajax: {
                        url: '{{ route('get.ts') }}',
                        dataType: 'json',
                        type: 'POST',
                        delay: 0,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.type_scenario.toUpperCase(),
                                        id: item.type_scenario,
                                    }
                                })
                            };
                        },
                        cache: false
                    }
                });

                $('.sts-bg').change(function(e) {
                    e.preventDefault();
                    let val = $(this).val();
                    next_status = val;
                    let url_ts = '{{ route('full_page', ':id') }}';
                    url_ts = url_ts.replace(':id',
                        `?type_scenario=${type_scenario}&next_status=${next_status}`);
                    $('.my-table').DataTable().ajax.url(url_ts).load();
                });

                $(`.sts-bg`).select2({
                    placeholder: 'Search status',
                    width: "100%",
                    allowClear: true,
                    ajax: {
                        url: '{{ route('get.sts') }}',
                        dataType: 'json',
                        type: 'POST',
                        delay: 0,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.next_status.toUpperCase(),
                                        id: item.next_status,
                                    }
                                })
                            };
                        },
                        cache: false
                    }
                });

                $(".btn-closebg").click(function(e) {
                    e.preventDefault();
                    let val = $(this).data('val');
                    let tipe = $(this).data('type');
                    let bg;
                    if (val == 'BG1') {
                        bg = "Barrier Gate 1";
                    } else if (val == 'BG2') {
                        bg = "Barrier Gate 2";
                    }
                    $.confirm({
                        title: "Confirmation",
                        content: `Are you sure, will close ${bg}?`,
                        theme: 'bootstrap',
                        columnClass: 'medium',
                        typeAnimated: true,
                        buttons: {
                            hapus: {
                                text: 'Submit',
                                btnClass: 'btn-red',
                                action: function() {
                                    $.ajax({
                                        type: "post",
                                        url: '{{ route('get_bearier1') }}',
                                        data: {
                                            action: val,
                                            tipe: tipe,
                                            from: "web-btn",
                                        },
                                        dataType: "json",
                                        success: function(res) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: `${bg} is close`,
                                            }).then(function() {
                                                if ('speechSynthesis' in
                                                    window) {
                                                    let status;
                                                    if (val == 'BG1') {
                                                        status = "close-gate-1";
                                                    } else if (val == 'BG2') {
                                                        status = "close-gate-2";
                                                    }

                                                    // let msg =
                                                    //     new SpeechSynthesisUtterance();
                                                    // let voices = speechSynthesis
                                                    //     .getVoices()[2];


                                                    // msg.voice = voices;
                                                    // msg.rate = 1;
                                                    // msg.pitch = 3;
                                                    // msg.text = status;
                                                    // msg.lang = 'id-ID';

                                                    // speechSynthesis.speak(
                                                    //     msg);
                                                    if (auto) {
                                                        document.getElementById(
                                                            status).pause();
                                                        document.getElementById(
                                                                status)
                                                            .currentTime = 0;
                                                        document.getElementById(
                                                            status).play();

                                                        //set delay
                                                        totalWaktu = document
                                                            .getElementById(
                                                                status)
                                                            .duration * 1000;
                                                    }

                                                } else {
                                                    console.log(
                                                        "Browser not support in speech!"
                                                    );
                                                }
                                                start();
                                                $(".api_val_cm").val(res.Mode);
                                                $(".api_bg_1").val(res
                                                    .Status_bg1);
                                                $(".api_bg_2").val(res
                                                    .Status_bg2);
                                                $(".api_val_cc").val(
                                                    `${res.ControllerConnetion}`
                                                );
                                                $(".api_val_w").val(res
                                                    .BeratTimbangan
                                                    .toFixed(2));
                                            });

                                        }
                                    });
                                }
                            },
                            close: function() {}
                        }
                    });

                });

                $(".btn-open").click(function(e) {
                    e.preventDefault();
                    let val = $(this).data('val');
                    let bg;
                    if (val == 'BG1') {
                        bg = "Barrier Gate 1";
                    } else if (val == 'BG2') {
                        bg = "Barrier Gate 2";
                    }
                    $.confirm({
                        title: "Confirmation",
                        content: `Are you sure, will open ${bg}?`,
                        theme: 'bootstrap',
                        columnClass: 'medium',
                        typeAnimated: true,
                        buttons: {
                            hapus: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function() {
                                    $.ajax({
                                        type: "post",
                                        url: '{{ route('get_bearier1') }}',
                                        data: {
                                            action: val,
                                            from: "web-btn",
                                        },
                                        dataType: "json",
                                        success: function(res) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: `${bg} is open`,
                                            }).then(function() {
                                                let status;
                                                if (val == 'BG1') {
                                                    status = "open-gate-1";
                                                } else if (val == 'BG2') {
                                                    status = "open-gate-2";
                                                }

                                                if (auto) {
                                                    document.getElementById(
                                                        status).pause();
                                                    document.getElementById(
                                                            status)
                                                        .currentTime = 0;
                                                    document.getElementById(
                                                        status).play();

                                                    //set delay
                                                    totalWaktu = document
                                                        .getElementById(
                                                            status)
                                                        .duration * 1000;
                                                }



                                                start();
                                                $(".api_val_cm").val(res.Mode);
                                                $(".api_bg_1").val(res
                                                    .Status_bg1);
                                                $(".api_bg_2").val(res
                                                    .Status_bg2);
                                                $(".api_val_cc").val(
                                                    `${res.ControllerConnetion}`
                                                );
                                                $(".api_val_w").val(res
                                                    .BeratTimbangan
                                                    .toFixed(2));
                                            });

                                        }
                                    });
                                }
                            },
                            close: function() {}
                        }
                    });

                });

                const Http = window.axios;
                const Echo = window.Echo;
                let channel = Echo.channel('channel-barier');
                channel.listen('BarierGateEvent', function(data) {
                    let status = data.message.status;
                    let action = data.message.action;
                    let truck_no = data.message.truck_no != null ? data.message.truck_no.replaceAll(" ", "")
                        .split("") : null;
                    if (action == 'open gate 1' || action == 'open gate 2') {
                        let status_bg;
                        if (action == 'open gate 1') {
                            status_bg = "open-gate-1";
                        } else if (action == 'open gate 2') {
                            status_bg = "open-gate-2";
                        }

                        let audioFiles = [];
                        let currentAudioIndex = 0;
                        if (truck_no) {
                            audioFiles.push(`/audio/plat-nomor.mp3`);
                            for (let i = 0; i < truck_no.length; i++) {
                                audioFiles.push(`/audio/${truck_no[i]}.mp3`);
                            }
                            audioFiles.push(`/audio/${status_bg}.wav`);
                            audioFiles.push(``);
                        } else {
                            audioFiles.push(`/audio/${status_bg}.wav`);
                            audioFiles.push(``);
                        }

                        function playAudio() {
                            const audio = new Audio(audioFiles[currentAudioIndex]);
                            // Move to the next audio file in the array
                            currentAudioIndex = (currentAudioIndex + 1) % audioFiles.length;

                            if (currentAudioIndex == 0) {
                                audio.pause();
                                audio.currentTime = 0;
                            } else {
                                if (auto) {
                                    audio.play();
                                } else {
                                    audio.pause();
                                    audio.currentTime = 0;
                                }

                                // audio.play();
                            }

                            // Listen for the 'ended' event to play the next audio
                            audio.addEventListener('ended', playAudio);
                        }


                        playAudio();
                    }

                    $('.my-table').DataTable().ajax.reload();
                });

                let url_page = '{{ route('full_page', ':id') }}';
                url_page = url_page.replace(':id', `?type_scenario=${type_scenario}&next_status=${next_status}`);

                $.fn.DataTable.ext.pager.numbers_length = 5;
                $('.my-table').DataTable({
                    processing: false,
                    serverSide: true,
                    searching: false,
                    info: false,
                    ordering: false,
                    paging: false,
                    pagingType: 'full_numbers',
                    scrollX: true,
                    ajax: url_page,
                    oLanguage: {
                        oPaginate: {
                            sNext: '<span class="fas fa-angle-right pgn-1" style="color: #5e72e4"></span>',
                            sPrevious: '<span class="fas fa-angle-left pgn-2" style="color: #5e72e4"></span>',
                            sFirst: '<span class="fas fa-angle-double-left pgn-3" style="color: #5e72e4"></span>',
                            sLast: '<span class="fas fa-angle-double-right pgn-4" style="color: #5e72e4"></span>',
                        }
                    },
                    columns: [{
                            data: 'orders',
                        },
                        {
                            data: 'scenario',
                        },
                        {
                            data: 'scale_1'
                        },
                        {
                            data: 'scale_2'
                        },
                        {
                            data: 'status_bg'
                        },
                        {
                            data: 'track_status'
                        },
                        {
                            data: 'action'
                        },

                    ],
                    columnDefs: [{
                        defaultContent: "-",
                        targets: "_all"
                    }],


                });
            });

            function start() {
                interval = setInterval(runConsole, 1000);
            }

            function runConsole() {
                $.ajax({
                    type: "post",
                    url: '{{ route('get_bearier1') }}',
                    data: "",
                    cache: false,
                    async: false,
                    dataType: "json",
                    success: function(res) {
                        $(".api_val_cm").val(res.Mode);
                        $(".api_bg_1").val(res.Status_bg1);
                        $(".api_bg_2").val(res.Status_bg2);
                        $(".api_sen1_bar1").val(res.Sen1Bar1);
                        $(".api_sen2_bar1").val(res.Sen2Bar1);
                        $(".api_sen1_bar2").val(res.Sen1Bar2);
                        $(".api_sen2_bar2").val(res.Sen2Bar2);
                        $(".api_val_cc").val(`${res.ControllerConnetion}`);
                        $(".api_val_w").val(res.BeratTimbangan.toFixed(2));
                    },
                    error: function() {
                        clearInterval(interval); // stop the interval
                    },
                });
            }

            function updateClock() {
                var now = new Date();
                var dname = now.getDay(),
                    mo = now.getMonth(),
                    dnum = now.getDate(),
                    yr = now.getFullYear(),
                    hou = now.getHours(),
                    min = now.getMinutes(),
                    sec = now.getSeconds(),
                    pe = "AM";

                if (hou >= 12) {
                    pe = "PM";
                }
                if (hou == 0) {
                    hou = 12;
                }
                // if (hou > 12) {
                //     hou = hou - 12;
                // }

                Number.prototype.pad = function(digits) {
                    for (var n = this.toString(); n.length < digits; n = 0 + n);
                    return n;
                }

                var months = ["January", "February", "March", "April", "May", "June", "July", "Augest", "September", "October",
                    "November", "December"
                ];
                var week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                var ids = ["dayname", "month", "daynum", "year", "hour", "minutes", "seconds"];
                var values = [week[dname], months[mo], dnum.pad(2), yr, hou.pad(2), min.pad(2), sec.pad(2)];
                for (var i = 0; i < ids.length; i++)
                    document.getElementById(ids[i]).firstChild.nodeValue = values[i];
            }

            function initClock() {
                updateClock();
                window.setInterval("updateClock()", 1);
            }
        </script>
    @endif
@endsection
