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
                                                    <input type="text" name="keyword" class="form-control" value="" required>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="lower_price">下限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" name="lower_price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="upper_price">上限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" name="upper_price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="excluded_word">除外ワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="excluded_word" class="form-control" placeholder="スペースで区切ります。">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label">対象のサービス</label>
                                                <div class="col-md-10">
                                                    <select multiple="multiple" name="services[]" class="form-control" required>
                                                        <option value="https://plus.wowma.jp/user/39095799/plus/">(ブランディア)</option>
                                                        <option value="https://www.2ndstreet.jp/store">(セカンドストリートオンライン)</option>
                                                        <option value="https://komehyo.jp/">(コメ兵)</option>
                                                        <option value="https://jp.mercari.com/">(メルカリ)</option>
                                                        <option value="https://auctions.yahoo.co.jp/">(ヤフオク)</option>
                                                        <option value="https://www.ecoauc.com/client">(エコリングオークション)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="status">商品の状態</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="status" class="form-control" value="">
                                                </div>
                                            </div>
                                     
                                            <div class="mb-2 row">
                                                <div class="button-list text-end">
                                                    <input type="button" value="プレビュー" class="btn btn-success pl-4 pr-4 mr-3">
                                                    <input type="submit" value="追加する" class="btn btn-primary pl-4 pr-4 mr-3">
                                                </div>
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