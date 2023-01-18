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
                                        <form method="POST" action="{{route('notification.update',$notification->id)}}" class="form-horizontal" role="form" id="storeForm">
                                            @csrf
                                            <input type="hidden" name="_method" value="PUT">
                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="keyword">キーワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="keyword" id="keyword" class="form-control" value="{{$notification->keyword}}" required>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="lower_price">下限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" id="lower_price" value="{{$notification->lower_price}}" name="lower_price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="upper_price">上限価格</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="number" id="upper_price" value="{{$notification->upper_price}}" name="upper_price">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="excluded_word">除外ワード</label>
                                                <div class="col-md-10">
                                                    <input type="text" id="excluded_word" value="{{$notification->excluded_words}}" name="excluded_word" class="form-control" placeholder="スペースで区切ります。">
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label">対象のサービス</label>
                                                <div class="col-md-10">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="wowma" name="services[wowma]" {{in_array('wowma' , $services)?'checked':''}}>
                                                        <label class="form-check-label" for="wowma">(ブランディア)(wowma)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="2ndstreet" name="services[2ndstreet]" {{in_array('2ndstreet' , $services)?'checked':''}}>
                                                        <label class="form-check-label" for="2ndstreet">(セカンドストリートオンライン)(2ndstreet)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="komehyo" name="services[komehyo]" {{in_array('komehyo' , $services)?'checked':''}}>
                                                        <label class="form-check-label" for="komehyo">(コメ兵)(komehyo)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="mercari" name="services[mercari]" {{in_array('mercari' , $services)?'checked':''}}>
                                                        <label class="form-check-label" for="mercari">(メルカリ)(mercari)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="yahoo" name="services[yahoo]" {{in_array('yahoo' , $services)?'checked':''}}>
                                                        <label class="form-check-label" for="yahoo">(ヤフオク)(yahoo)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="ecoauc" name="services[ecoauc]" {{in_array('ecoauc' , $services)?'checked':''}}>
                                                        <label class="form-check-label" for="ecoauc">(エコリングオークション)(ecoauc)</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-md-2 col-form-label" for="status">商品の状態</label>
                                                <div class="col-md-10">
                                                    <input type="text" id="item_status" value="{{$notification->status}}" name="status" class="form-control" value="">
                                                </div>
                                            </div>
                                     
                                            <div class="mb-2 row">
                                                <div class="button-list text-end">
                                                    <button type="button" class="btn btn-success" id="preview" data-bs-toggle="modal" data-bs-target="#standard-modal">プレビュー</button>
                                                    <input type="submit" value="編集する" class="btn btn-primary pl-4 pr-4 mr-3">
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
                                            <div class="d-flex justify-content-center">
                                                <div class="spinner-border text-success m-2" role="status" id="loading"></div>
                                            </div>
                                            <div class="row" id="search_results">  
                                               
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">いいえ</button>
                                            <button type="button" class="btn btn-primary" id="addButton">追加する</button>
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
            $("#loading").show();
            var keyword = $("#keyword").val();
            var lower_price = $("#lower_price").val();
            var upper_price = $("#upper_price").val();
            var excluded_word = $("#excluded_word").val();
            var services = [];
            if($("#wowma").is(':checked')){
                services.push('wowma');
            }
            if($("#2ndstreet").is(':checked')){
                services.push('2ndstreet');
            }
            if($("#komehyo").is(':checked')){
                services.push('komehyo');
            }
            if($("#mercari").is(':checked')){
                services.push('mercari');
            }
            if($("#yahoo").is(':checked')){
                services.push('yahoo');
            }
            if($("#ecoauc").is(':checked')){
                services.push('ecoauc');
            }
            var status = $("#item_status").val();
            if(!keyword)alert("キーワードを入力してください。");
            if(services.length == 0)alert("対象のサービスを選択してください。");
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
                    $("#loading").hide();
                    $('#search_results').append(results);
                        
                }
            });
            
        })

        $("#addButton").on('click', function() {
            $("#storeForm").submit();
        })
    });
</script>
@endsection