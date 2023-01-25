@extends('layouts.master-admin')

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
                                
                                <form method="POST" action="{{route('user.mailLimitStore')}}" class="form-horizontal" role="form">
                                    @csrf
                                    <div class="mb-2 row">
                                        <label class="col-md-2 col-form-label" for="monthlyMailLimit">毎月のメール上限</label>
                                        <div class="col-md-10">
                                            <input type="number" name="monthlyMailLimit" id="monthlyMailLimit" class="form-control" value="{{$mailLimit}}" placeholder="" required>
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
