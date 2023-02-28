@extends('layouts.master-admin')

@section('content')
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">
           
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box page-title-box-alt">
                        <h4 class="page-title">ユーザー編集</h4>
                    </div>
                </div>
            </div>     
            <!-- end page title --> 

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                
                                <form method="POST" action="{{route('user.update', $user->id)}}" class="form-horizontal" role="form">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT">

                                    <div class="mb-2 row">
                                        <label class="col-md-2 col-form-label" for="email">メールアドレス</label>
                                        <div class="col-md-10">
                                        <input type="text" name="email" id="email" class="form-control" value="{{$user->email}}" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label class="col-md-2 col-form-label" for="email">メール上限</label>
                                        <div class="col-md-10">
                                            <input type="number" name="monthlyMailLimit" id="monthlyMailLimit" class="form-control" value="{{$user->mailLimit}}" placeholder="" required>
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="button-list text-end">
                                            <input type="submit" value="設定する" class="btn btn-primary pl-4 pr-4 mr-3">
                                        </div>
                                    </div>
                                </form>

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
                html: "このユーザーを停止してもよろしいでしょうか？",
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
                        url: "{{ route('user.disable') }}",
                        type: 'POST',
                        data:{
                            _token: '{{csrf_token()}}',
                            userId: $(this).data('id'),
                        },
                        success: function(result) {
                            location.reload()
                        }
                    });
                    return;
                }
            })
        })
        $('.btn-start-user').click(function() {
            toastr.fire({
                html: "このユーザーを再開してもよろしいですか？",
                showDenyButton: false,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "確認",
                cancelButtonText: "キャンセル",
                confirmButtonColor: "#02a8b5",
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: undefined
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user.enable') }}",
                        type: 'POST',
                        data:{
                            _token: '{{csrf_token()}}',
                            userId: $(this).data('id'),
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
