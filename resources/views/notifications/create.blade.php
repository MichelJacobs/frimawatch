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
                                        <form method="POST" action="{{route('notification.store')}}" class="form-horizontal" role="form">
                                            @csrf
                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="keyword">キーワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="keyword" class="form-control" value="">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="lower-price">下限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" name="lower-price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="upper-price">上限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" name="upper-price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="excluded_word">除外ワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="excluded_word" class="form-control" placeholder="、で区切ります。">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label">除外サービス</label>
                                                <div class="col-md-10">
                                                    <select multiple="multiple" name="excluded_service" class="form-control">
                                                        <option>1</option>
                                                        <option>2</option>
                                                        <option>3</option>
                                                        <option>4</option>
                                                        <option>5</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="status">スターテス</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="status" class="form-control" value="">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <div class="button-list text-end"><input type="submit" value="追加する" class="btn btn-primary pl-4 pr-4 mr-3"></div>
                                            </div>
                                        </form>
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