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
                        <h4 class="page-title">アラート条件</h4>
                    </div>
                </div>
            </div>     
            <!-- end page title --> 

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-12">
                                    <div class="p-2">
                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="keyword">キーワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="keyword" class="form-control" value="{{$notification->keyword}}" disabled>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="lower_price">下限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" name="lower_price" value="{{$notification->lower_price}}" disabled>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="upper_price">上限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" name="upper_price" value="{{$notification->upper_price}}" disabled>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="excluded_word">除外ワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="excluded_word" class="form-control" value="{{$notification->excluded_word}}" disabled>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label">対象のサービス</label>
                                                <div class="col-md-10">
                                                    @foreach ($notification->services as $service)
                                                        {{$service->service}}<br>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="status">商品の状態</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="status" class="form-control" value="{{$notification->status}}" disabled>
                                                </div>
                                            </div>

                                    </div>
                                </div>

                            </div>
                            <!-- end row -->
                        </div>
                    </div> <!-- end card -->

                    <!-- end modal-->
                </div>
                <!-- end col-12 -->
            </div> <!-- end row -->
            
        </div> <!-- container-fluid -->

    </div> <!-- content -->

</div>
@endsection