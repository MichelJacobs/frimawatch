@extends('layouts.master')

@section('content')
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            
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
                                            <th>除外サービス</th>
                                            <th>スターテス</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($notifications as $notification)
                                        <tr>
                                            <td>{{$notification->keyword}}</td>
                                            <td>{{$notification->lower_price}}</td>
                                            <td>{{$notification->upper_price}}</td>
                                            <td>{{$notification->excluded_word}}</td>
                                            <td>{{$notification->excluded_service}}</td>
                                            <td>{{$notification->item_status}}</td>
                                            <td>
                                                <a href="{{ route('notification.show', $notification->id) }}" class="btn btn-sm btn-block btn-primary mt-1">詳細</a>
                                                <button type="button" class="btn btn-sm btn-block mt-1 btn-danger btn-delete-company" data-id="{{ $notification->id }}">削除</button>
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
