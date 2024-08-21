@extends('layouts.app')

@section('content')
<div class="container">
    <div class="user-lists">
        <div>
            {{-- <h3>シフト提出済みユーザ</h3> --}}
            <ul>
                {{-- @foreach ($submittedEmployees as $employee)
                    <li>{{ $employee->name }}</li>
                @endforeach --}}
            </ul>
        </div>
        <div>
            {{-- <h3>シフト未提出ユーザ</h3> --}}
            <ul>
                {{-- @foreach ($notSubmittedEmployees as $employee)
                    <li>{{ $employee->name }}</li>
                @endforeach --}}
            </ul>
        </div>
    </div>
    <a href="{{ route('contractor.create') }}" class="btn">被雇用者管理</a>
    <div id="calendar" class="calendar-container"></div>
</div>
<!-- モーダル -->
<div class="modal fade" id="shiftModal" tabindex="-1" aria-labelledby="shiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="shiftModalLabel">シフト調整</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="availabilityList"></div>
          <form id="shiftForm">
            @csrf
            <div class="mb-3">
              <label for="userSelect" class="form-label">ユーザ</label>
              <select class="form-select" id="userSelect" name="user_id"></select>
            </div>
            <div class="mb-3">
              <label for="startTime" class="form-label">開始時刻</label>
              <input type="time" class="form-control" id="startTime" name="start_time">
            </div>
            <div class="mb-3">
              <label for="endTime" class="form-label">終了時刻</label>
              <input type="time" class="form-control" id="endTime" name="end_time">
            </div>
            <input type="hidden" id="shiftDate" name="date">
            <button type="submit" class="btn btn-primary">シフト確定</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ja',
                events: [
                    @foreach ($shifts as $shift)
                        {
                            title: '{{ $shift->user->name }}: {{ $shift->start_time }} - {{ $shift->end_time }} (確定)',
                            start: '{{ $shift->date }}',
                            color: '#f60219',
                        },
                    @endforeach
                    @foreach ($availabilities as $availability)
                        {
                            title: '{{ $availability->user->name }}: {{ $availability->start_time }} - {{ $availability->end_time }} (希望)',
                            start: '{{ $availability->date }}',
                            color: '#618bf3',
                        },
                    @endforeach
                ],
                height: 'auto',
                firstDay: 1,
                headerToolbar: {
                    left: "dayGridMonth,listMonth",
                    center: "title",
                    right: "today prev,next"
                },
                buttonText: {
                    today: '今月',
                    month: '月',
                    list: 'リスト'
                },
                noEventsContent: '提出されたシフトがありません。',
                dateClick: function(info) {
                    fetchAvailabilities(info.dateStr);
                }
            });
            calendar.render();
            function fetchAvailabilities(date) {
                fetch(`/availabilities/${date}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('shiftDate').value = date;
                        const availabilityList = document.getElementById('availabilityList');
                        const userSelect = document.getElementById('userSelect');
                        availabilityList.innerHTML = '';
                        userSelect.innerHTML = '';

                        data.availabilities.forEach(availability => {
                            availabilityList.innerHTML += `<p>${availability.user.name}: ${availability.start_time} - ${availability.end_time}</p>`;
                            userSelect.innerHTML += `<option value="${availability.user.id}">${availability.user.name}</option>`;
                        });

                        new bootstrap.Modal(document.getElementById('shiftModal')).show();
                    });
            }

            document.getElementById('shiftForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(event.target);

                fetch('/shifts/store', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'シフトの確定に失敗しました。');
                    }
                });
            });
        });
    </script>
@endsection
@section('styles')
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css"> --}}
    <style>
        .calendar-container {
            width: 100%;
            height: calc(100vh - 100px);
        }
    </style>
@endsection