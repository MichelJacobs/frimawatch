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
                                                        <option value="https://plus.wowma.jp/user/39095799/plus/">(ブランディア)(wowma)</option>
                                                        <option value="https://www.2ndstreet.jp/store">(セカンドストリートオンライン)(2ndstreet)</option>
                                                        <option value="https://komehyo.jp/">(コメ兵)(komehyo)</option>
                                                        <option value="https://jp.mercari.com/">(メルカリ)(mercari)</option>
                                                        <option value="https://auctions.yahoo.co.jp/">(ヤフオク)(yahoo)</option>
                                                        <option value="https://www.ecoauc.com/client">(エコリングオークション)(ecoauc)</option>
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
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#standard-modal">プレビュー</button>
                                                    <input type="submit" value="追加する" class="btn btn-primary pl-4 pr-4 mr-3">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                            <!-- end row -->
                            <!-- Standard modal content -->
                            <div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="standard-modalLabel">アラート プレビュー</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xl-12 col-md-12">
                                                    <div class="d-flex">
                                                        <div style="width:100px;height:100px;">
                                                            <img src="{{ asset('assets/images/small/img-3.jpg') }}" class="img-fluid" alt="result">
                                                        </div>
                                                        <div class="col-xl-8 col-md-8 p-2">
                                                            <h6 class="mt-0 mb-1 text-danger">7500</h6>
                                                            <p class="text-muted mb-0 font-13">テキストテキストテキストテキストテキスト</p>
                                                            <p class="text-muted mb-0 font-13">wowma</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-12 col-md-12">
                                                    <div class="d-flex">
                                                        <div style="width:100px;height:100px;">
                                                            <img src="{{ asset('assets/images/small/img-3.jpg') }}" class="img-fluid" alt="result">
                                                        </div>
                                                        <div class="col-xl-8 col-md-8 p-2">
                                                            <h6 class="mt-0 mb-1 text-danger">6500</h6>
                                                            <p class="text-muted mb-0 font-13">テキストテキストテキストテキスト</p>
                                                            <p class="text-muted mb-0 font-13">2ndstreet</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
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