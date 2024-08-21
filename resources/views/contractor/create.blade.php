@extends('layouts.app')

@section('styles')
<style>
    /* カスタムスタイルの追加 */
    .dataTables_wrapper {
        color: #333;
        background-color: #f7fdf4;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 10px;
        border: 1px solid #9acd32;
        padding: 5px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 10px;
        border: 1px solid #9acd32;
        padding: 5px;
    }
    table.dataTable {
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    table.dataTable thead th {
        background-color: #9acd32;
        color: white;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        border: #333;
    }
    table.dataTable tbody td {
        background-color: #fff;
        /* border: 2px solid #9acd32; */
        border-radius: 10px;
        padding: 10px;
        text-align: center;
    }
    table.dataTable tbody tr:hover {
        background-color: #f7fdf4;
    }
    table.dataTable tfoot th {
        background-color: #9acd32;
        color: white;
        border-radius: 0 0 10px 10px;
        padding: 10px;
        text-align: center;
    }
    .btn {
        background-color: #9acd32;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 16px;
    }
    .btn:hover {
        background-color: #8cbf28;
    }
</style>
@endsection

@section('content')
<div class="container">
    <h2>被雇用者管理</h2>
    <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#createEmployeeModal" style="margin-bottom:20px;">新規被雇用者作成</button>
    <a href="{{ route('logout') }}" class="btn">ログアウト</a>
    <a href="{{ route('contractor.index') }}" class="btn">ホーム</a>
    <table id="employeesTable" class="display" style="padding-top: 20px;">
        <thead>
            <tr>
                <th>名前</th>
                <th>役職レベル</th>
                <th>作成日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->position }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        <form action="{{ route('contractor.destroy', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 新規被雇用者作成モーダル -->
    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('contractor.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createEmployeeModalLabel">新規被雇用者作成</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">名前</label>
                            <input type="text" class="form-control" id="name" name="name" required autocomplete="name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">メールアドレス</label>
                            <input type="email" class="form-control" id="email" name="email" required autocomplete="email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                        <button type="submit" class="btn btn-primary">作成</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#employeesTable').DataTable({
            language: {
                "sProcessing": "処理中...",
                "sLengthMenu": "_MENU_ 件表示",
                "sZeroRecords": "データはありません。",
                "sInfo": " _TOTAL_ 件中 _START_ から _END_ まで表示",
                "sInfoEmpty": " 0 件中 0 から 0 まで表示",
                "sInfoFiltered": "（全 _MAX_ 件より抽出）",
                "sInfoPostFix": "",
                "sSearch": "検索:",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "先頭",
                    "sPrevious": "前",
                    "sNext": "次",
                    "sLast": "最終"
                }
            }
        });
    });
</script>
@endsection
