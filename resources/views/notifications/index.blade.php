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
                        <h4 class="page-title">ウォッチ管理</h4>
                    </div>
                </div>
            </div>     
            <!-- end page title --> 

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="row table-responsive">
                                <div class="button-list text-end">
                                    <a href="{{route('notification.create')}}" class="btn btn-sm btn-primary mr-3 mb-3 btn-done">新しいアラートを作る</a>
                                </div>
                                
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>キーワード</th>
                                            <th>下限価格</th>
                                            <th>上限価格</th>
                                            <th>除外ワード</th>
                                            <th>対象のサービス</th>
                                            <th>商品の状態</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($notifications as $notification)
                                        <tr>
                                            <td>{{$notification->keyword}}</td>
                                            <td>{{$notification->lower_price}}</td>
                                            <td>{{$notification->upper_price}}</td>
                                            <td>{{$notification->excluded_words}}</td>
                                            <td>
                                                @foreach ($notification->services as $service)
                                                    @switch($service->service)
                                                        @case('wowma')
                                                            ブランディア<br>
                                                            @break
                                                    
                                                        @case('2ndstreet')
                                                            セカンドストリートオンライン<br>
                                                            @break
                                                    
                                                        @case('komehyo')
                                                            コメ兵<br>
                                                            @break
                                                    
                                                        @case('mercari')
                                                            メルカリ<br>
                                                            @break
                                                    
                                                        @case('yahooflat')
                                                            ヤフオク（定額）<br>
                                                            @break
                                                    
                                                        @case('auction')
                                                            ヤフオク（オークション）<br>
                                                            @break
                                                    
                                                        @case('netmall')
                                                            中古通販のオフモール<br>
                                                            @break
                                                    
                                                        @default
                                                            @break
                                                    @endswitch
                                                @endforeach
                                            </td>
                                            <td>{{$notification->status}}</td>
                                            <td>
                                                <a href="{{ route('notification.show', $notification->id) }}" class="btn btn-sm btn-block btn-primary mt-1">編集</a>
                                                <form action="{{ route('notification.destroy',$notification)}}" method="post">
                                                    <input type="hidden" name="_method" value="delete" />
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-sm btn-block mt-1 btn-danger btn-delete-company" data-id="{{ $notification->id }}">削除</button>
                                                </form>
                                                
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7"><h4 class="text-center mt-3">一致するレコードがありません</h4></td>
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
