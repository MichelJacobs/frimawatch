<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\TimeLine;
use App\Models\Setting;
use App\Models\Notification;
use App\Models\NotificationService;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Http;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public const SENT_COUNT = 25;
    protected $count = 1;
    protected $lower_price;
    protected $upper_price;
    protected $excluded_word;
    protected $user;
    protected $results = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("start");

        $availableUsers = User::where('is_admin',0)->where('active',1)->get();
        foreach($availableUsers as $user) {
            $this->user = $user;
            $notifications = $user->notifications;

            if($user->mailSent >= $user->mailLimit) {
                $this->info("mail limited");
                continue;
            }
            foreach($notifications as $notification) {
                $this->count = 0;
                $this->results = [];
                $keyword = $notification->keyword;
                $this->lower_price = $notification->lower_price;
                $this->upper_price = $notification->upper_price;
                $this->excluded_word = $notification->excluded_word;
                $services = $notification->services;
                $status = $notification->status;

                foreach($services as $service) {
                    
                    if($this->count > self::SENT_COUNT) break;
                    if (str_contains($service, 'wowma')) {
                        $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                        $totalPages = 1;

                        for($i = 0; $i < $totalPages; $i++) {
                            if($this->count > self::SENT_COUNT) break;
                            $response = Http::get('https://wowma.jp/catalog/api/search/items', [
                                'keyword' => $new,
                                'e_scope' => 'O',
                                'user' => 39095799,
                                'x' => 0,
                                'y' => 0,
                                'page' => $i,
                                'uads' => 0,
                                'acc_filter' => 'N',
                                'shop_only' => 'Y',
                                'ref_id' => 'catalog_klist2',
                                'mode' => 'pc',
                            ]);
                            if($i == 0) {
                                $totalPages = $response->object()->pageInformation->totalPages;
                            }
                            $hitItems = $response->object()->hitItems;
                            foreach($hitItems as $item) {
                                if($this->count > self::SENT_COUNT) break;
                                if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $item->currentPrice, $item->itemName )){
                                    array_push($this->results, [
                                        'currentPrice' => $item->currentPrice,
                                        'itemImageUrl' => $item->itemImageUrl,
                                        'itemName' => $item->itemName,
                                        'url' => $item->url,
                                        'service' => 'wowma',
                                    ]);
                                    $this->count++;
                                }
                            }
                        }
                    }

                    if (str_contains($service, '2ndstreet')) {
                        $client = new Client();
                        $pages = 0;
                        // $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                        for ($i = 0; $i < $pages + 1; $i++) {
                            if($this->count > self::SENT_COUNT) break;
                            if($i == 0 ){
                                $url = "https://www.2ndstreet.jp/search?keyword=".$keyword."&page=0";
                                $crawler = $client->request('GET', $url);
                                try {
                                    $pages = ($crawler->filter('nav.ecPager li')->count() > 0)
                                    ? $crawler->filter('nav.ecPager li:nth-last-child(2)')->text()
                                    : 0
                                ;
                                }catch(\Throwable  $e){
                                    $pages = 0;break;
                                }
                                
                            }else {
                                $url = "https://www.2ndstreet.jp/search?keyword=".$keyword."&page=".$i;
                                $crawler = $client->request('GET', $url);
                            }
                            $crawler->filter('.js-favorite')->each(function ($node) {
                                if($this->count > self::SENT_COUNT) return false;
                                $url = $node->filter('a.listLink')->attr('href');
                                $itemImageUrl = $node->filter('.imgBlock img')->attr('data-src');
                                $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.price')->text()), 10);
                                $itemName   = $node->filter('.name-goods')->text();

                                if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                    array_push($this->results, [
                                        'currentPrice' => $currentPrice,
                                        'itemImageUrl' => $itemImageUrl,
                                        'itemName' => $itemName,
                                        'url' => 'https://www.2ndstreet.jp'.$url,
                                        'service' => '2ndstreet',
                                    ]);
                                    $this->count++;
                                }
                            });
                            
                        }
                    }

                    if (str_contains($service, 'komehyo')) {
                        $client = new Client();
                        $pages = 1;
                        // $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                        for ($i = 1; $i < $pages + 1; $i++) {
                            if($this->count > self::SENT_COUNT) break;
                            if($i == 1 ){
                                $url = "https://komehyo.jp/search/?q=".$keyword."&page=1";
                                $crawler = $client->request('GET', $url);
                                try {
                                    $pages = ($crawler->filter('.p-pager li')->count() > 0)
                                    ? $crawler->filter('.p-pager li:nth-last-child(2)')->text()
                                    : 0
                                ;
                                if($pages == 0) break;
                                }catch(\Throwable  $e){
                                    $pages = 1;break;
                                }
                                
                            }else {
                                $url = "https://komehyo.jp/search/?q=".$keyword."&page=".$i;
                                $crawler = $client->request('GET', $url);
                            }
                            try {
                                
                                $crawler->filter('.p-lists__item')->each(function ($node) {
                                    if($this->count > self::SENT_COUNT) return false;
                                    $url = $node->filter('a.p-link')->attr('href');
                                    $itemStatus = $node->filter('.p-link__label')->text();
                                    if($status == $itemStatus) {
                                        $itemImageUrl = $node->filter('.p-link__head img')->attr('src');
                                        $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.p-link__txt--price')->text()), 10);
                                        $itemName   = $node->filter('.p-link__txt--productsname')->text();
                                        if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                            array_push($this->results, [
                                                'currentPrice' => $currentPrice,
                                                'itemImageUrl' => $itemImageUrl,
                                                'itemName' => $itemName,
                                                'url' => 'https://komehyo.jp'.$url,
                                                'service' => 'komehyo',
                                            ]);
                                            $this->count++;
                                        }
                                    }
                                });
                            }catch(\Throwable  $e){
                                continue;
                            }
                            
                        }
                    }

                    if (str_contains($service, 'mercari')) {
                        $client = new Client();
                        $pages = 0;
                        $pageToken = "";
                        for ($i = 0; ; $i++) {
                            if($this->count > self::SENT_COUNT) break;
                            $url = "https://api.mercari.jp/v2/entities:search";
                            $options = array(
                                "userId"=> "",
                                "pageSize"=> 120,
                                "pageToken"=> $pageToken,
                                "searchSessionId"=> "6a0556d3330b3f3f8b87d8648c24aab1",
                                "indexRouting"=> "INDEX_ROUTING_UNSPECIFIED",
                                "thumbnailTypes"=> [],
                                "searchCondition"=> array(
                                    "keyword"=> $keyword,
                                    "excludeKeyword"=> "",
                                    "sort"=> "SORT_SCORE",
                                    "order"=> "ORDER_DESC",
                                    "status"=> ["STATUS_ON_SALE"],
                                    "sizeId"=> [],
                                    "categoryId"=> [],
                                    "brandId"=> [],
                                    "sellerId"=> [],
                                    "priceMin"=> $this->lower_price??0,
                                    "priceMax"=> $this->upper_price??1000000,
                                    "itemConditionId"=> [],
                                    "shippingPayerId"=> [],
                                    "shippingFromArea"=> [],
                                    "shippingMethod"=> [],
                                    "colorId"=> [],
                                    "hasCoupon"=> false,
                                    "attributes"=> [],
                                    "itemTypes"=> [],
                                    "skuIds"=> []
                                ),
                                "defaultDatasets"=> [
                                    "DATASET_TYPE_MERCARI",
                                    "DATASET_TYPE_BEYOND"
                                ],
                                "serviceFrom"=> "suruga",
                                "withItemBrand"=> false,
                                "withItemSize"=> false
                            );
                            $response = Http::withHeaders([
                                'dpop' => 'eyJ0eXAiOiJkcG9wK2p3dCIsImFsZyI6IkVTMjU2IiwiandrIjp7ImNydiI6IlAtMjU2Iiwia3R5IjoiRUMiLCJ4Ijoic2d1S2hIQ3c4WTllQzlTZ2ZiUWVMdzRKSU5BZ3VNRWVFQjZJLUVub1U2RSIsInkiOiJJWmtnS284dGhZX2ZYUHFhSTgyWndNci1TYjV0VHFrZC1SRGg2UktLS0V3In19.eyJpYXQiOjE2NzQ3MjI2NDAsImp0aSI6IjQ3YTc0YzUzLWY3OGItNGY1OS04ZGEyLWJmMDNlZTZkYzI0ZiIsImh0dSI6Imh0dHBzOi8vYXBpLm1lcmNhcmkuanAvdjIvZW50aXRpZXM6c2VhcmNoIiwiaHRtIjoiUE9TVCIsInV1aWQiOiJkZDJjMGEzYS0wYjA4LTQ0YzEtOTJhYi0zMjQwMjEwOTU2N2UifQ.xcqFcoO0Yyz06FdaEN_3ZgtYYIkQfo0QXn-4-3Hn1QPwyPdBUzroFmkzMc5_wVpDc4tPJxYd5xYfPM7fFu49Gw',
                                'x-platform' => 'web'
                            ])->post($url,$options);
                            $hitItems = $response->object()->items;
                            $pageToken = $response->object()->meta->nextPageToken;
                            
                            foreach($hitItems as $item) {
                                if($this->count > self::SENT_COUNT) break;
                                if($this->compareWords($this->excluded_word, $item->name )){
                                    array_push($this->results, [
                                        'currentPrice' => $item->price,
                                        'itemImageUrl' => $item->thumbnails[0],
                                        'itemName' => $item->name,
                                        'url' => 'https://jp.mercari.com/item/'.$item->id,
                                        'service' => 'mercari',
                                    ]);
                                    $this->count++;
                                }
                            }

                            if($pageToken == "") break;
                            
                        }
                    }

                    if (str_contains($service, 'yahooflat')) {
                        $client = new Client();
                        $pages = 1;
                        // $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                        for ($i = 1; $i < $pages + 1; $i++) {
                            if($this->count > self::SENT_COUNT) break;
                            if($i == 1 ){
                                $url = "https://auctions.yahoo.co.jp/search/search?p=".$keyword."&va=".$keyword."&fixed=1&exflg=1&b=1&n=50";//ヤフオク（定額）
                                
                                $crawler = $client->request('GET', $url);
                                try {
                                    $pages = ($crawler->filter('.Pager__lists li')->count() > 0)
                                    ? $crawler->filter('.Pager__lists li:nth-last-child(3)')->text()
                                    : 0
                                ;
                                if($pages == 0) break;
                                }catch(\Throwable  $e){
                                    $pages = 1;break;
                                }
                                
                            }else {
                                $n = ($i - 1) * 50;
                                $url = "https://auctions.yahoo.co.jp/search/search?p=".$keyword."&va=".$keyword."&fixed=1&exflg=1&b=".(string)$n."&n=50";
                                $crawler = $client->request('GET', $url);
                            }
                            try {
                                $crawler->filter('.Product')->each(function ($node) {
                                    if($this->count > self::SENT_COUNT) return false;
                                    $url = $node->filter('a.Product__imageLink')->attr('href');
                                    $itemImageUrl = $node->filter('.Product__imageData')->attr('src');
                                    $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.Product__priceValue')->text()), 10);
                                    $itemName   = $node->filter('.Product__title')->text();
                                    if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                        array_push($this->results, [
                                            'currentPrice' => $currentPrice,
                                            'itemImageUrl' => $itemImageUrl,
                                            'itemName' => $itemName,
                                            'url' => $url,
                                            'service' => 'ヤフオク（定額）',
                                        ]);
                                        $this->count++;
                                    }
                                });
                                
                            }catch(\Throwable  $e){
                                continue;
                            }
                            
                        }
                    }

                    if (str_contains($service, 'auction')) {
                        $client = new Client();
                        $pages = 1;
                        // $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                        for ($i = 1; $i < $pages + 1; $i++) {
                            if($this->count > self::SENT_COUNT) break;
                            if($i == 1 ){
                                $url = "https://auctions.yahoo.co.jp/search/search?p=".$keyword."&va=".$keyword."&fixed=2&exflg=1&b=1&n=50";//ヤフオク（定額）
                                
                                $crawler = $client->request('GET', $url);
                                try {
                                    $pages = ($crawler->filter('.Pager__lists li')->count() > 0)
                                    ? $crawler->filter('.Pager__lists li:nth-last-child(3)')->text()
                                    : 0
                                ;
                                if($pages == 0) break;
                                }catch(\Throwable  $e){
                                    $pages = 1;break;
                                }
                                
                            }else {
                                $n = ($i - 1) * 50;
                                $url = "https://auctions.yahoo.co.jp/search/search?p=".$keyword."&va=".$keyword."&fixed=2&exflg=1&b=".(string)$n."&n=50";
                                $crawler = $client->request('GET', $url);
                            }
                            try {
                                $crawler->filter('.Product')->each(function ($node) {
                                    if($this->count > self::SENT_COUNT) return false;
                                    $url = $node->filter('a.Product__imageLink')->attr('href');
                                    $itemImageUrl = $node->filter('.Product__imageData')->attr('src');
                                    $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.Product__priceValue')->text()), 10);
                                    $itemName   = $node->filter('.Product__title')->text();
                                    if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                        array_push($this->results, [
                                            'currentPrice' => $currentPrice,
                                            'itemImageUrl' => $itemImageUrl,
                                            'itemName' => $itemName,
                                            'url' => $url,
                                            'service' => 'ヤフオク（オークション）',
                                        ]);
                                        $this->count++;
                                    }
                                });
                                
                            }catch(\Throwable  $e){
                                continue;
                            }
                            
                        }
                    }

                    if (str_contains($service, 'netmall')) {
                        $client = new Client();
                        $pages = 1;
                        // $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                        for ($i = 1; $i < $pages + 1; $i++) {
                            if($this->count > self::SENT_COUNT) break;
                            if($i == 1 ){
                                $url = "https://netmall.hardoff.co.jp/search/?exso=1&q=".$keyword."&s=7";
                                
                                $crawler = $client->request('GET', $url);
                                try {
                                    $pages = ($crawler->filter('.pagenation a')->count() > 0)
                                    ? $crawler->filter('.pagenation a:nth-last-child(2)')->text()
                                    : 0
                                ;
                                if($pages == 0) break;
                                }catch(\Throwable  $e){
                                    $pages = 1;break;
                                }
                                
                            }else {
                                $url = "https://netmall.hardoff.co.jp/search/?p=2&q=".$keyword."&exso=1&s=7";
                                $crawler = $client->request('GET', $url);
                            }
                            try {
                                $crawler->filter('.itemcolmn_item')->each(function ($node) {
                                    if($this->count > self::SENT_COUNT) return false;
                                    $url = $node->filter('.itemcolmn_item a')->attr('href');
                                    $itemImageUrl = $node->filter('.item-img-square img')->attr('src');
                                    $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.item-price-en')->text()), 10);
                                    $itemName   = $node->filter('.item-brand-name')->text();
                                    if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                        array_push($this->results, [
                                            'currentPrice' => $currentPrice,
                                            'itemImageUrl' => $itemImageUrl,
                                            'itemName' => $itemName,
                                            'url' => $url,
                                            'service' => '中古通販のオフモール',
                                        ]);
                                        $this->count++;
                                    }
                                });
                                
                            }catch(\Throwable  $e){
                                continue;
                            }
                            
                        }
                    }

                }

                $this->sendEmail($this->results, $this->user);
            }
        }
        $this->info("end");
        return 0;
    }

    public function compareWords($excluded_word, $itemName) {
        $result = true;
        if(isset($excluded_word)) {
            $words = explode(' ',$excluded_word);
            foreach($words as $word) {
                if(str_contains($itemName, $word))$result = false;
            }
        }
        return $result;
    }

    public function compareCondition($lower_price, $upper_price,$excluded_word, $currentPrice, $itemName ) {
        $result = false;
        $result = $lower_price ? ($currentPrice >= $lower_price) : true;
        if($result) {
            $result = $upper_price ? ($currentPrice <= $upper_price) : true;
            if($result) {
                if(isset($excluded_word)) {
                    $words = explode(' ',$excluded_word);
                    foreach($words as $word) {
                        if(str_contains($itemName, $word))$result = false;
                    }
                }
            }
        }
        return $result;
    }

    public function sendEmail($results, $user) {

        $items = $results;
        $items = array_unique($items,SORT_REGULAR);

        $mailLimit = $user->mailLimit;
        $mailSent = $user->mailSent;

        $urls = TimeLine::where('user_id',$user->id)->get();
        foreach($urls as $url) {
            foreach($results as $key => $result) {
                if($url->url == $result['url']) {
                    unset($items[$key]);break;
                }
            }
        }

        $content = $user->name.'様<br>
                    商品があります。<br>';
        if(count($items) > 0) {
            
            foreach($items as $item) {
                
                $content .= '商品名　'.$item['itemName'].'<br>
                商品価格　'.$item['currentPrice'].'円<br>
                商品サービス　'.$item['service'].'<br>
                商品ページ '.$item['url'].'<br><br><br><br>';
    
            }
            
            $email = $user->email;
            $user_id = 'trialphoenix';
            $api_key = '2aUSJ6gntGT6paez6XPaihMc0XEXZDWJqbwIVbRmpSWXwsDCKGjUZRDjfMIjt4Hw';
            if ($user_id === false) {
                echo "ユーザIDは必須です";
                exit;
            }
            if ($api_key === false) {
                echo "APIキーは必須です";
                exit;
            }
            // トークン生成
            $str = "$user_id$api_key";
            $token = base64_encode(strtolower(hash('sha256', $str)));
            // APIエンドポイント
            $url = 'https://app.engn.jp/api/v1/deliveries/transaction';
            // POSTデータ
            
            $data = [
                "from" => [
                        "email" => "devlife128@gmail.com",
                        "name" => "frimawatch"
                ],
                "to" => $email,
                "subject" => "商品があります。",
                "encode" => "ISO-2022-JP",
                "text_part" => "テスト配信",
                "html_part" => $content
            ];
            
            $data = json_encode($data);

            // ヘッダー
            $header = [
                "Content-Type: application/json",
                "Authorization: Bearer $token"
            ];
            // リクエスト内容を組み立て
            $context = [
                "http" => [
                        "method"  => "POST",
                        "header"  => implode("\r\n", $header),
                        "content" => $data
                ]
            ];
            // APIリクエスト
            $res = file_get_contents($url, false, stream_context_create($context));
            // 結果の出力
            $this->info("sent");

            foreach($items as $item) {
                TimeLine::create([
                    'user_id' => $user->id,
                    'itemName' => $item['itemName'],
                    'itemImageUrl' => $item['itemImageUrl'],
                    'currentPrice' => $item['currentPrice'],
                    'url' => $item['url'],
                    'service' => $item['service'],
                ]);
            }

            User::where('id',$user->id)->update(array('mailSent' => $mailSent + 1));
        } else {
            $this->info("There are no matching items");
        }
        
    }
}
