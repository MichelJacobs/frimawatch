@extends('layouts.master')

@section('content')

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row mt-3">
                <div class="col-12">
                    @foreach (['info', 'success', 'danger', 'warning'] as $msg)
                        @if (Session::has('system.message.' . $msg))
                            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                {{ Session::get('system.message.' . $msg) }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box page-title-box-alt">
                        <h4 class="page-title">設定</h4>
                    </div>
                </div>
            </div>     
            <!-- end page title --> 

            <div class="row">
                <div class="col-12">

                <div class="card">
                        <div class="card-body">
                            <div class="row">
                                
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>名前</th>
                                            <th>メールアドレス</th>
                                            <th>メール数 /メール上限</th>
                                            <th>ステータス</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->email}}</td>
                                            <td>{{$user->mailSent}}回<br>{{$user->mailLimit}}回</td>
                                            @if ($user->active)
                                            <td>
                                                <span class="badge bg-primary p-1">有効</span>
                                            </td>
                                            @else
                                            <td>
                                                <span class="badge bg-danger p-1">無効</span>
                                            </td>
                                            @endif
                                            
                                            <td>
                                                <a href="{{ route('user.show', $user->id) }}" class="btn btn-sm btn-block btn-primary mt-1">編集</a>
                                                @if ($user->active)
                                                <button type="button" class="btn btn-sm btn-block mt-1 btn-danger btn-delete-user" data-id="{{ $user->id }}">停止</button>
                                                @else
                                                <button type="button" class="btn btn-sm btn-block mt-1 btn-primary btn-start-user" data-id="{{ $user->id }}">再開</button>
                                                @endif
                                                
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7"><h4 class="text-center mt-3">データがありません。</h4></td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                            </div>  <!-- end row -->
                        </div> <!-- end card body-->
                    </div> <!-- end card -->

                    <!-- end modal-->
                </div>
                <!-- end col-12 -->
            </div> <!-- end row -->
            
        </div> <!-- container-fluid -->

    </div> <!-- content -->

</div>
@endsection

@section('scripts')

<script>
    $(document).ready(function(){
        $('.btn-delete-user').click(function() {
            toastr.fire({
                html: "この商品を削除してもよろしいでしょうか？",
                showDenyButton: false,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "確認",
                cancelButtonText: "キャンセル",
                confirmButtonColor: "#dc3545",
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: undefined
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('timeline.delete') }}",
                        type: 'POST',
                        data:{
                            _token: '{{csrf_token()}}',
                            itemId: $(this).data('id'),
                        },
                        success: function(result) {
                            location.reload()
                        }
                    });
                    return;
                }
            })
        })
    })
</script>
    
@endsection
