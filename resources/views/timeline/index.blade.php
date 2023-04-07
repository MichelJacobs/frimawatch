@extends('layouts.master')

@section('content')
<style>
    svg {
        height: 20px;
    }

    p.leading-5 {
        display: none;
    }

    nav > div:first-of-type span {
        display: none;
    }
    nav > div:nth-child(2) {
        float: right;
    }
</style>
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
                        <h4 class="page-title">タイムライン</h4>
                    </div>
                </div>
            </div>     
            <!-- end page title --> 

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="row table-responsive">
                                
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No</th>
                                            <th style="width:15%;">画像</th>
                                            <th style="width:60%;">商品情報</th>
                                            <th style="width:20%;">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($timelines as $key => $timeline)
                                            <tr>
                                                <td>{{$counts - $key - ($timelines->currentPage() - 1) * 50}}</td>
                                                <td>
                                                    <img src="{{$timeline->itemImageUrl}}" alt="" srcset="" width="100px" height="100px">
                                                </td>
                                                <td style="line-height: 2rem">
                                                    <span class="text-danger">{{number_format($timeline->currentPrice)}}円</span>&nbsp;
                                                    @if(isset($timeline->keyword))
                                                    キーワード : {{$timeline->keyword??''}}
                                                    @endif
                                                    <br>
                                                    {{$timeline->itemName}}<br>
                                                    {{$timeline->created_at?$timeline->created_at->addHours(9)->format('Y-m-d H:i'):''}}
                                                    @switch($timeline->service)
                                                        @case('wowma')
                                                            <span class="text-primary">ブランディア</span><br>
                                                            @break
                                                    
                                                        @case('2ndstreet')
                                                            <span class="text-primary">セカンドストリートオンライン</span><br>
                                                            @break
                                                    
                                                        @case('komehyo')
                                                            <span class="text-primary">コメ兵</span><br>
                                                            @break
                                                    
                                                        @case('mercari')
                                                            <span class="text-primary">メルカリ</span><br>
                                                            @break
                                                    
                                                        @case('yahooflat')
                                                                <span class="text-primary">ヤフオク（定額）</span><br>
                                                            @break
                                                    
                                                        @case('auction')
                                                            <span class="text-primary">ヤフオク（オークション）</span><br>
                                                            @break
                                                    
                                                        @case('netmall')
                                                            <span class="text-primary">中古通販のオフモール</span><br>
                                                            @break
                                                    
                                                        @default
                                                            <span class="text-primary">{{$timeline->service}}</span><br>
                                                            @break
                                                    @endswitch
                                                </td>
                                            
                                                <td>
                                                    <a href="{{ $timeline->url }}" target="_blank" class="btn btn-sm btn-block btn-primary mt-1">商品ページ</a>
                                                    <button type="button" class="btn btn-sm btn-block mt-1 btn-danger btn-delete-user" data-id="{{ $timeline->id }}">削除</button>
                                                    
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7"><h4 class="text-center mt-3">一致するレコードがありません</h4></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                

                            </div>  <!-- end row -->
                            {{ $timelines->onEachSide(1)->links() }}
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
