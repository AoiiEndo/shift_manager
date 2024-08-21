@extends('layouts.app')

{{-- @section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
@endsection --}}

@section('content')

<div class="container mt-5">
    <div class="alert alert-success" style="display: none;">シフト希望が正常に更新されました。</div>
    <div id="calendar" class="calendar-container"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="shiftModal" tabindex="-1" aria-labelledby="shiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shiftModalLabel">希望シフトの入力</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="shiftForm">
                    <div class="mb-3">
                        <label for="start_time" class="form-label">開始時間</label>
                        <input type="time" class="form-control" id="start_time" name="start_time">
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">終了時間</label>
                        <input type="time" class="form-control" id="end_time" name="end_time">
                    </div>
                    <input type="hidden" id="shift_date" name="shift_date">
                    <input type="hidden" id="existingShiftId">
                    <button type="submit" class="btn btn-primary">保存</button>
                    <button type="button" class="btn btn-danger" id="deleteShiftBtn">削除</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Pm0SmHG4mTJT8VhZUANU1p3ed1z4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        var calendar;
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var daysOfWeek = ['日', '月', '火', '水', '木', '金', '土'];

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                views: {
                    dayGridMonth: { buttonText: '月' },
                    timeGridWeek: { buttonText: '週' },
                    timeGridDay: { buttonText: '日' },
                    listWeek: { buttonText: 'リスト' }
                },
                locale: 'ja',
                height: 'auto',
                timeZone: 'Asia/Tokyo',
                businessHours: true,
                dateClick: function(info) {
                    document.getElementById('shift_date').value = info.dateStr;
                    document.getElementById('shiftModalLabel').innerText = 'シフト希望を入力: ' + info.dateStr + ' (' + info.date.toLocaleDateString('ja-JP', { weekday: 'long' }) + ')';
                    
                    // Ajaxリクエストで既存のシフトを取得
                    $.ajax({
                        url: '{{ route("employee.checkShift") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            date: info.dateStr,
                        },
                        success: function(response) {
                            if (response.exists) {
                                $('#start_time').val(response.availability.start_time);
                                $('#end_time').val(response.availability.end_time);
                                $('#existingShiftId').val(response.availability.id);
                                $('#deleteShiftBtn').show(); // 削除ボタンを表示
                            } else {
                                $('#start_time').val('');
                                $('#end_time').val('');
                                $('#existingShiftId').val('');
                                $('#deleteShiftBtn').hide(); // 削除ボタンを非表示
                            }
                            var shiftModal = new bootstrap.Modal(document.getElementById('shiftModal'));
                            shiftModal.show();
                        }
                    });
                },
                events: [
                    @foreach ($shifts as $shift)
                    {
                        title: '{{ $shift->start_time }} - {{ $shift->end_time }}',
                        start: '{{ $shift->date }}',
                        color: '#f60219',
                    },
                    @endforeach
                    @foreach ($availabilities as $availability)
                    {
                        title: '{{ $availability->start_time }} - {{ $availability->end_time }}',
                        start: '{{ $availability->date }}',
                        color: '#618bf3',
                    },
                    @endforeach
                ],
                buttonText: {
                    today: '今月',
                    month: '月',
                    list: 'リスト'
                },
                noEventsContent: '提出されたシフトがありません。',
            });

            calendar.render();
            if (document.querySelector('.alert-success')) {
                setTimeout(function() {
                    document.querySelector('.alert-success').classList.add('fade-out');
                }, 3000);
            }
        });

        // フォーム送信ハンドラ
        $('#shiftForm').submit(function(event) {
            event.preventDefault();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            var shiftDate = $('#shift_date').val();
            var shiftId = $('#existingShiftId').val();

            // 日付と時間を組み合わせて送信
            var startDatetime = shiftDate + 'T' + startTime;
            var endDatetime = shiftDate + 'T' + endTime;

            var ajaxUrl = shiftId ? '{{ route("employee.update") }}' : '{{ route("employee.store") }}';

            // Ajaxリクエストを送信
            $.ajax({
                url: ajaxUrl,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    start_datetime: startDatetime,
                    end_datetime: endDatetime,
                    date: shiftDate,
                    id: shiftId
                },
                success: function(response) {
                    $('.alert-success').fadeIn("slow", function () {
                        $(this).delay(3000).fadeOut("slow");
                    });
                    var events = response.availabilities.concat(response.shifts).map(function(event) {
                        return {
                            title: event.start_time + ' - ' + event.end_time,
                            start: event.date,
                            color: event.type === 'availability' ? '#f60219' : '#618bf3'
                        };
                    });
                    calendar.removeAllEvents();
                    calendar.addEventSource(events);
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    alert('エラー: ' + JSON.stringify(errors));
                }
            });

            $('#shiftModal').modal('hide');
        });

        // 論理削除処理
        $('#deleteShiftBtn').click(function() {
            var shiftId = $('#existingShiftId').val();
            $.ajax({
                url: '{{ route("employee.delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: shiftId
                },
                success: function(response) {
                    $('.alert-success').fadeIn("slow", function () {
                        $(this).delay(3000).fadeOut("slow");
                    });
                    var events = response.availabilities.concat(response.shifts).map(function(event) {
                        return {
                            title: event.start_time + ' - ' + event.end_time,
                            start: event.date,
                            color: event.type === 'availability' ? '#f60219' : '#618bf3'
                        };
                    });
                    calendar.removeAllEvents();
                    calendar.addEventSource(events);

                    $('#shiftModal').modal('hide');
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    alert('エラー: ' + JSON.stringify(errors));
                }
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .alert {
            opacity: 1;
            transition: opacity 1s ease-out;
        }
        .fade-out {
            opacity: 0;
        }
    </style>
@endsection
