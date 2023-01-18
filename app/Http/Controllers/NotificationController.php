<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationService;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{

    public const TOTAL_COUNT = 50;

    protected $results = [];
    protected $count = 1;
    protected $lower_price;
    protected $upper_price;
    protected $excluded_word;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $notifications = Notification::where('user_id',auth()->user()->id)->get();

        return view('notifications.index',compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = auth()->user();
        $notification = new Notification();
        $notification->fill([
            "user_id" => $user->id,
            "keyword" => $request->get('keyword'),
            "lower_price" => $request->get('lower_price'),
            "upper_price" => $request->get('upper_price'),
            "excluded_words" => $request->get('excluded_word'),
            "status" => $request->get('status')
        ]);

        $notification->save();

        foreach($request->get('services') as $key => $service) {

            $notificationService = new NotificationService();
            $notificationService->fill([
                "notification_id" => $notification->id,
                "service" => $key,
            ]);

            $notificationService->save();
        }

        return redirect()->action([NotificationController::class, 'index']);
    }

    public function scrape(Request $request) 
    {
        $keyword = $request->get('keyword');
        $this->lower_price = $request->get('lower_price');
        $this->upper_price = $request->get('upper_price');
        $this->excluded_word = $request->get('excluded_word');
        $services = $request->get('services');
        $status = $request->get('status');

        foreach($services as $service) {
            
            if($this->count > self::TOTAL_COUNT) break;
            if (str_contains($service, 'wowma')) {
                $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                $totalPages = 1;

                for($i = 0; $i < $totalPages; $i++) {
                    if($this->count > self::TOTAL_COUNT) break;
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
                        if($this->count > self::TOTAL_COUNT) break;
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
                    if($this->count > self::TOTAL_COUNT) break;
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
                        if($this->count > self::TOTAL_COUNT) return false;
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
                    if($this->count > self::TOTAL_COUNT) break;
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
                    $crawler->filter('.p-lists__item')->each(function ($node) {
                        if($this->count > self::TOTAL_COUNT) return false;
                        $url = $node->filter('a.p-link')->attr('href');
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
                    });
                }
            }

            if (str_contains($service, 'mercari')) {
                $client = new Client();
                $pages = 0;
                for ($i = 0; ; $i++) {
                    if($this->count > self::TOTAL_COUNT) break;
                    $url = "https://komehyo.jp/search/?q=".$keyword."&page=".$i;
                    $crawler = $client->request('GET', $url);
                    if($crawler->filter('#item-grid li')->count() > 0) {
                        $crawler->filter('#item-grid li')->each(function ($node) {
                            if($this->count > self::TOTAL_COUNT) return false;
                            $url = $node->filter('a')->attr('href');
                            $itemImageUrl = $node->filter('img')->attr('src');
                            $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.number')->text()), 10);
                            $itemName   = $node->filter('.item-name')->text();
                            if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                array_push($this->results, [
                                    'currentPrice' => $currentPrice,
                                    'itemImageUrl' => $itemImageUrl,
                                    'itemName' => $itemName,
                                    'url' => $url,
                                    'service' => 'komehyo',
                                ]);
                                $this->count++;
                            }
                            
                        });
                    }else{
                        break;
                    }
                    
                }
            }

            //result part
            $str = '<div class="col-xl-12 col-md-12 mt-1">
                        <h4 class="mt-0 mb-1 text-danger" style="text-align:center;">ヒット件数 : '.count($this->results).'件</h4>
                    </div>';
            if(count($this->results) > 0) {
                foreach($this->results as $item) {
                    $str .= '<div class="col-xl-12 col-md-12 mt-1">
                                <a href="'.$item['url'].'" target="_blank">
                                <div class="d-flex">
                                    <div style="width:100px;height:100px;">
                                        <img src="'.$item['itemImageUrl'].'" class="img-fluid" alt="result">
                                    </div>
                                    <div class="col-xl-8 col-md-8 p-2">
                                        <h6 class="mt-0 mb-1 text-danger">'.$item['currentPrice'].'円</h6>
                                        <p class="text-muted mb-0 font-13">'.$item['itemName'].'</p>
                                        <p class="text-muted mb-0 font-13">'.$item['service'].'</p>
                                    </div>
                                </div>
                                </a>
                            </div>';
                }
            }else{
                $str .= '<div class="text-center">一致する商品が見つかりません。</div>';
            }
            
        }

        return $str;
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
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $notification = Notification::where('id',$id)->first();
        $services = [];
        foreach($notification->services as $service){
            array_push($services, $service->service);
        }
        return view('notifications.show',compact('notification','services'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = auth()->user();
        $notification = new Notification();

        Notification::where('id', $id)
            ->update([
                "keyword" => $request->get('keyword'),
                "lower_price" => $request->get('lower_price'),
                "upper_price" => $request->get('upper_price'),
                "excluded_words" => $request->get('excluded_word'),
                "status" => $request->get('status')
            ]);

        NotificationService::where('notification_id', $id)->delete();
        foreach($request->get('services') as $key => $service) {

            NotificationService::Create(
                ["notification_id" => $id,"service" => $key,]
            );

        }

        return redirect()->action([NotificationController::class, 'index']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Notification::where('id',$id)->delete();
        return redirect()->action([NotificationController::class, 'index'])->with(['system.message.success' => "削除されました。"]);
    }
}
