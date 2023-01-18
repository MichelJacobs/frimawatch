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
                        <h4 class="page-title">横断検索</h4>
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
                                    <a href="{{route('search.create')}}" class="btn btn-sm btn-primary mr-3 mb-3 btn-done">新しい検索</a>
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
                                    @forelse($searchs as $search)
                                        <tr>
                                            <td>{{$search->keyword}}</td>
                                            <td>{{$search->lower_price}}</td>
                                            <td>{{$search->upper_price}}</td>
                                            <td>{{$search->excluded_words}}</td>
                                            <td>
                                                @foreach ($search->services as $service)
                                                    {{$service->service}}<br>
                                                @endforeach
                                            </td>
                                            <td>{{$search->status}}</td>
                                            <td>
                                                {{-- <a href="{{ route('search.show', $search->id) }}" class="btn btn-sm btn-block btn-primary mt-1">詳細</a> --}}
                                                <form action="{{ route('search.destroy',$search)}}" method="post">
                                                    <input type="hidden" name="_method" value="delete" />
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-sm btn-block mt-1 btn-danger btn-delete-company" data-id="{{ $search->id }}">削除</button>
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
