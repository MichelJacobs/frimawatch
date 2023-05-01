<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationService;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverCheckboxes;
use Facebook\WebDriver\WebDriverRadios;
use Facebook\WebDriver\WebDriverSelect;
use Symfony\Component\DomCrawler\Crawler;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Http;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NotificationExport;
use App\Imports\NotificationImport;

class NotificationController extends Controller
{

    public const TOTAL_COUNT = 25;

    protected $results = [];
    protected $count = 1;
    protected $keyword;
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
        $user = auth()->user();
        $notifications = Notification::where('user_id',auth()->user()->id)->get();

        return view('notifications.index',compact('notifications','user'));
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
        ini_set('max_execution_time', 999999);
        $keyword = $request->get('keyword');
        $this->keyword = $request->get('keyword');
        $this->lower_price = $request->get('lower_price');
        $this->upper_price = $request->get('upper_price');
        $this->excluded_word = $request->get('excluded_word');
        $services = $request->get('services');
        $status = $request->get('status');

        foreach($services as $service) {
            
            if($this->count > self::TOTAL_COUNT) break;
            if (str_contains($service, 'wowma')) {
                $client = new Client();
                $pages = 0;
                for ($i = 0; $i < $pages + 1; $i++) {
                    if($this->count > self::TOTAL_COUNT) break;
                    if($i == 0 ){
                        $url = "https://auction.brandear.jp/search/list/?SearchFullText=".$keyword;
                        $client = new Client(HttpClient::create([
                            'timeout'         => 20,
                            'headers' => [
                                'Accept' => '*/*',
                                'Host' => 'auction.brandear.jp',
                                'Postman-Token' => '',
                                'Cookie' => 'ba_defacto_analytics=%5B%5D; ba_search_history_entire=K%B42%B4%AA%CE%B42%B0N%B42%B2%AA.%B62%B1R%CAN%AD%2CV%02%F2%A1%12%C5V%86%40%C1%E0%D4%C4%A2%E4%0C%B7%D2%9C%9C%90%D4%8A%12%25%EB%DAb%2B3%2B%A5%B2%C4%9C%D2TT%C5%96VJ%8F%9B%B6%3Fn%5E%FC%B8%B9%05%A8%AC%B6%16%00; ba_sessid=6cfaca6f01c7292770b4a341000ffb32',
                                ]
                            ]));
                        $client->setServerParameter('HTTP_USER_AGENT', 'user agent');
                        $crawler = $client->request('GET',$url);
                        try {
                            $pages = $crawler->filter('.resultCount span')->text()
                            ? (intval($crawler->filter('.resultCount span')->text() / 50) + 1)
                            : 0
                        ;
                        }catch(\Throwable  $e){
                            $pages = 0;break;
                        }
                    }else {
                        $url = "https://auction.brandear.jp/search/list/?SearchFullText=".$keyword."&ItemOrder=0&page=".$i;
                        $client = new Client(HttpClient::create([
                            'timeout'         => 20,
                            'headers' => [
                                'Accept' => '*/*',
                                'Host' => 'auction.brandear.jp',
                                'Postman-Token' => '',
                                'Cookie' => 'ba_defacto_analytics=%5B%5D; ba_search_history_entire=K%B42%B4%AA%CE%B42%B0N%B42%B2%AA.%B62%B1R%CAN%AD%2CV%02%F2%A1%12%C5V%86%40%C1%E0%D4%C4%A2%E4%0C%B7%D2%9C%9C%90%D4%8A%12%25%EB%DAb%2B3%2B%A5%B2%C4%9C%D2TT%C5%96VJ%8F%9B%B6%3Fn%5E%FC%B8%B9%05%A8%AC%B6%16%00; ba_sessid=6cfaca6f01c7292770b4a341000ffb32',
                                ]
                            ]));
                        $crawler = $client->request('GET', $url);
                    }
                    try {
                        $crawler->filter('#result li')->each(function ($node) {
                            if($this->count > self::TOTAL_COUNT) return false;
                            $url = $node->filter('.item_name a')->attr('href');
                            $itemImageUrl = $node->filter('.item .img img')->attr('data-original');
                            $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('span.price')->text()), 10);
                            $itemName   = $node->filter('.item_name span')->text();
                            if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                array_push($this->results, [
                                    'currentPrice' => $currentPrice,
                                    'itemImageUrl' => $itemImageUrl,
                                    'itemName' => $itemName,
                                    'url' => 'https://auction.brandear.jp'.$url,
                                    'service' => 'brandear',
                                ]);
                                $this->count++;
                            }
                        });
                    }catch(\Throwable  $e){
                        continue;
                    }
                }
            }

            if (str_contains($service, '2ndstreet')) {

                if($this->count > self::TOTAL_COUNT) break;
                $this->initBrowser();
                $this->results = [];

                $url = "https://www.2ndstreet.jp/search?keyword=".$keyword."&page=0";

                if(isset($this->lower_price)){
                    $url .= '&minPrice='.$this->lower_price;
                }
                
                if(isset($this->upper_price)){
                    $url .= '&maxPrice='.$this->upper_price;
                }

                $url .= '&search=OK';

                
                $response = $this->driver->get($url);

                $crawler = new Crawler($response->getPageSource());

                $this->driver->close();
                
                try {
                    $crawler->filter('.js-favorite')->each(function ($node) {
                        if($this->count > self::TOTAL_COUNT) return false;
                        $url = $node->filter('a.itemCard_inner')->attr('href');
                        $itemImageUrl = $node->filter('.itemCard_img img')->attr('src');
                        $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.itemCard_price')->text()), 10);
                        $itemName   = $node->filter('.itemCard_name')->text();

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
                }catch(\Throwable  $e){
                    
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
                    try {
                        $crawler->filter('.p-lists__item')->each(function ($node) {
                            if($this->count > self::TOTAL_COUNT) return false;
                            $url = $node->filter('a.p-link')->attr('href');
                            $itemStatus = $node->filter('.p-link__label')->text();
                            $isStatus = false;
                            if(isset($status)) {
                                if($status == $itemStatus) {
                                    $isStatus = true;
                                }else{
                                    $isStatus = false;
                                }
                            }else {
                                $isStatus = true;
                            }
                            if($isStatus) {
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
                        dd($e->getMessage());
                        continue;
                    }
                    
                }
            }

            if (str_contains($service, 'mercari')) {
                if($this->count > self::TOTAL_COUNT) break;
                $this->initBrowser();
                $this->results = [];

                $url = "https://jp.mercari.com/search?keyword=".$this->keyword;
                if(isset($this->lower_price)){
                    $url .= '&price_min='.$this->lower_price;
                }
                $url .= '&status=on_sale';
                if(isset($this->upper_price)){
                    $url .= '&price_max='.$this->upper_price;
                }
                if(isset($this->upper_price)){
                    $url .= '&price_max='.$this->upper_price;
                }
                $crawler = $this->getPageHTMLUsingBrowser($url);

                $this->driver->close();

                try {
                    $crawler->filter('#item-grid li')->each(function ($node) {
                        if($this->count > self::TOTAL_COUNT) return false;
                        $url = $node->filter('a')->attr('href');
                        $itemImageUrl = $node->filter('source img')->attr('src');
                        $itemName   = $node->filter('source img')->attr('alt');
                        $itemName = str_replace("のサムネイル","",$itemName);
                        $price = intval(preg_replace('/[^0-9]+/', '', $node->filter('figure')->text()), 10);
                        if($this->compareWords($this->excluded_word, $itemName )){
                            array_push($this->results, [
                                'currentPrice' => $price,
                                'itemImageUrl' => $itemImageUrl,
                                'itemName' => $itemName,
                                'url' => 'https://jp.mercari.com'.$url,
                                'service' => 'mercari',
                            ]);
                        }
                        $this->count++;
                    });
                }catch(\Throwable  $e){
                    
                }
         
            }

            if (str_contains($service, 'yahooflat')) {
                $client = new Client();
                $pages = 1;
                // $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
                for ($i = 1; $i < $pages + 1; $i++) {
                    if($this->count > self::TOTAL_COUNT) break;
                    if($i == 1 ){
                        $url = "https://auctions.yahoo.co.jp/search/search?p=".$keyword."&va=".$keyword."&fixed=1&exflg=1&b=1&n=50";//ヤフオク（定額）
                        $totalUrl = "https://auctions.yahoo.co.jp/search/search?p=".$keyword."&va=".$keyword."&fixed=3&exflg=1&b=1&n=50";
                        $totalcrawler = $client->request('GET', $totalUrl);
                        $flatcount = intval(preg_replace('/[^0-9]+/', '', $totalcrawler->filter('.Tab__items li:nth-last-child(1) .Tab__subText')->text()), 10);
                        if($flatcount == 0) break;
                        // $order   = array(" ", "　");
                        // $replace = '+';
                        // $url = str_replace($order, $replace, $url);
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
                            if($this->count > self::TOTAL_COUNT) return false;
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
                    if($this->count > self::TOTAL_COUNT) break;
                    if($i == 1 ){
                        $url = "https://auctions.yahoo.co.jp/search/search?p=".$keyword."&va=".$keyword."&fixed=2&exflg=1&b=1&n=50";//ヤフオク（定額）
                        $crawler = $client->request('GET', $url);
                        try {
                            $pages = ($crawler->filter('.Pager__lists li')->count() > 0)
                            ? $crawler->filter('.Pager__lists li:nth-last-child(3)')->text()
                            : 0
                        ;
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
                            if($this->count > self::TOTAL_COUNT) return false;
                            $url = $node->filter('a.Product__imageLink')->attr('href');
                            $itemImageUrl = $node->filter('.Product__imageData')->attr('src');
                            $currentPrice = intval(preg_replace('/[^0-9]+/', '', $node->filter('.Product__priceValue')->text()), 10);
                            $itemName   = $node->filter('.Product__title')->text();
                            if($this->compareCondition($this->lower_price, $this->upper_price,$this->excluded_word, $currentPrice, $itemName )){
                                $productTime  = $node->filter('.Product__time')->text();
                                if($this->productTimeCompare($productTime)){
                                    array_push($this->results, [
                                        'currentPrice' => $currentPrice,
                                        'itemImageUrl' => $itemImageUrl,
                                        'itemName' => $itemName,
                                        'url' => $url,
                                        'service' => 'ヤフオク（オークション）',
                                    ]);
                                    $this->count++;
                                }
                            }
                        });

                        if($pages == 0) break;
                        
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
                    if($this->count > self::TOTAL_COUNT) break;
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
                            if($this->count > self::TOTAL_COUNT) return false;
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
                                        <h6 class="mt-0 mb-1 text-danger">'.number_format($item['currentPrice']).'円</h6>
                                        <p class="text-muted mb-0 font-13">'.$item['itemName'].'</p>
                                        <p class="mb-0 font-13 text-primary">'.$item['service'].'</p>
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

    public function import($userId){
        try {
            set_time_limit(0);
            ini_set('max_execution_time', 0); 
            $file = request()->file('file');
            $folder = '/tmp/';
            $filename = $file->getClientOriginalName();
            $path = $folder . $filename;
            $file->move($folder, $filename);
            $handle = fopen($path, 'r');
            $content = fread($handle, filesize($path));
            $enc = mb_detect_encoding($content, mb_list_encodings(), true);
            if (strtolower($enc) !== 'utf-8') {
                return back()->with(['system.message.info' => __('アップロードしたファイルの文字コードは、「' . $enc . '」です。UTF-8でアップロードしてください。')]);
            }
            Excel::import(new NotificationImport($userId), $path);
        } catch (\Throwable $e) {
            dd($e);
            return back()->with(['system.message.info' => __('CSVファイルのアップロードに失敗しました')]);
        }

        return back()->with(['system.message.success' => __(':itemが完了しました。', ['item' => __('アップロード(CSV)')])]);
    }

    public function export($userId)
    {
        try{
            return Excel::download(new NotificationExport($userId), '通知一覧.csv', \Maatwebsite\Excel\Excel::CSV);
        }catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Get page using browser.
     */
    public function getPageHTMLUsingBrowser(string $url)
    {
        $response = $this->driver->get($url);

        $this->driver->wait(5000,1000)->until(
            function () {
                $elements = $this->driver->findElements(WebDriverBy::XPath("//div[contains(@id,'search-result')]"));
                sleep(3);
                return count($elements) > 0;
            },
        );
        
        return new Crawler($response->getPageSource(), $url);
    }
    /**
     * Init browser.
     */
    public function initBrowser()
    {
        $options = new ChromeOptions();
        $arguments = ['--disable-gpu', '--no-sandbox', '--disable-images'];

        $options->addArguments($arguments);

        $caps = DesiredCapabilities::chrome();
        $caps->setCapability('acceptSslCerts', false);
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);
        
        $this->driver = RemoteWebDriver::create('http://localhost:4444', $caps);
    }

    public function productTimeCompare($productTime){
        if(str_contains($productTime, '日')){
            $time = intval(preg_replace('/[^0-9]+/', '', $productTime), 10);
            if($time == 1) {
                return true;
            }
            else {
                return false;
            }
        }else{
            return true;
        }
    }

    public function compareWords($excluded_word, $itemName) {
        $result = true;
        if(isset($excluded_word)) {
            $words = explode(' ',$excluded_word);
            foreach($words as $word) {
                if($word == "") continue;
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
                        if($word == "") continue;
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
