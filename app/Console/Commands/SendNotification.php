<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Url;
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

    public const SENT_COUNT = 2;
    protected $count = 1;
    protected $lower_price;
    protected $upper_price;
    protected $excluded_word;
    protected $user;

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
            foreach($notifications as $notification) {
                $this->count = 1;
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
                                    $content = '';
                                    $content .= $user->name.' 様'.'<br>';
                                    $content .= '商品があります。'.'<br>';
                                    $content .= '商品名　'.$item->itemName.'<br>';
                                    $content .= '商品価格　'.$item->currentPrice.'<br>';
                                    $content .= '商品サービス　wowma'.'<br>';
                                    $content .= '商品ページ '.$item->url.'<br>';
                                    $this->sendEmail($content,$item->url, $user);
                                    
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
                                $pages = ($crawler->filter('nav.ecPager li')->count() > 0)
                                    ? $crawler->filter('nav.ecPager li:nth-last-child(2)')->text()
                                    : 0
                                ;
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
                                    $content = '';
                                    $content .= $this->user->name.' 様'.'<br>';
                                    $content .= '商品があります。'.'<br>';
                                    $content .= '商品名　'.$itemName.'<br>';
                                    $content .= '商品価格　'.$currentPrice.'<br>';
                                    $content .= '商品サービス　2ndstreet'.'<br>';
                                    $content .= '商品ページ https://www.2ndstreet.jp'.$url.'<br>';
                                    $this->sendEmail($content,$url, $this->user);
                                   
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
                                $pages = ($crawler->filter('.p-pager li')->count() > 0)
                                    ? $crawler->filter('.p-pager li:nth-last-child(2)')->text()
                                    : 1
                                ;
                            }else {
                                $url = "https://komehyo.jp/search/?q=".$keyword."&page=".$i;
                                $crawler = $client->request('GET', $url);
                            }
                            $crawler->filter('.p-lists__item')->each(function ($node) {
                                if($this->count > self::SENT_COUNT) return false;
                                $url = $node->filter('a.p-link')->attr('href');
                                $itemImageUrl = $node->filter('.p-link__head img')->attr('src');
                                $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.p-link__txt--price')->text()), 10);
                                $itemName   = $node->filter('.p-link__txt--productsname')->text();
                                if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                    $content = '';
                                    $content .= $this->user->name.' 様'.'<br>';
                                    $content .= '商品があります。'.'<br>';
                                    $content .= '商品名　'.$itemName.'<br>';
                                    $content .= '商品価格　'.$currentPrice.'<br>';
                                    $content .= '商品サービス　komehyo'.'<br>';
                                    $content .= '商品ページ https://komehyo.jp'.$url.'<br>';
                                    $this->sendEmail($content,$url, $this->user);
                                    
                                }
                                
                            });
                        }
                    }

                }
            }
        }
        $this->info("end");
        return 0;
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

    public function sendEmail($content,$url, $user) {

        if(Url::where('user_id',$user->id)->where('url',$url)->count()){
            $this->info("it is already registered");
        }else{
            $this->count++;
            Url::create([
                'user_id' => $user->id,
                'url' => $url,
            ]);
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
                        "email" => "yuuzi2006@gmail.com",
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
        }
        
    }
}
