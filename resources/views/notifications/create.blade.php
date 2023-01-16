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
                                                    <input type="text" name="keyword" id="keyword" class="form-control" value="" required>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="lower_price">下限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" id="lower_price" name="lower_price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="upper_price">上限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" id="upper_price" name="upper_price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="excluded_word">除外ワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" id="excluded_word" name="excluded_word" class="form-control" placeholder="スペースで区切ります。">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label">対象のサービス</label>
                                                <div class="col-md-10">
                                                    <select multiple="multiple" name="services[]" id="services" class="form-control" required>
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
                                                    <input type="text" id="item_status" name="status" class="form-control" value="">
                                                </div>
                                            </div>
                                     
                                            <div class="mb-2 row">
                                                <div class="button-list text-end">
                                                    <button type="button" class="btn btn-success" id="preview" data-bs-toggle="modal" data-bs-target="#standard-modal">プレビュー</button>
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
                                            <div class="d-flex justify-content-center" id="loading">
                                                <div class="spinner-border text-success m-2" role="status"></div>
                                            </div>
                                            <div class="row" id="search_results">  
                                               
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">いいえ</button>
                                            <button type="button" class="btn btn-primary">追加する</button>
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

@section('scripts')
<script>
    $( document ).ready(function() {
        $('#preview').on('click',function() {
            $('#search_results').children().remove();
            var keyword = $("#keyword").val();
            var lower_price = $("#lower_price").val();
            var upper_price = $("#upper_price").val();
            var excluded_word = $("#excluded_word").val();
            var services = $("#services").val();
            var status = $("#item_status").val();
            $.ajax({
                type:'POST',
                url:"{{ route('scrape') }}",
                data:{
                    keyword:keyword,
                    lower_price:lower_price,
                    upper_price:upper_price,
                    excluded_word:excluded_word,
                    services:services,
                    status:status,
                    _token: '{{csrf_token()}}'},
                success:function(results){
                    $("#loading").remove();
                    $('#search_results').append(results);
                        
                }
            });
            
        })
    });
</script>
@endsection