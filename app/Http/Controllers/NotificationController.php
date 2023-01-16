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

    protected $services = [
        'https://plus.wowma.jp/user/39095799/plus/',
        'https://www.2ndstreet.jp/store',
        'https://komehyo.jp/',
        'https://jp.mercari.com/',
        'https://auctions.yahoo.co.jp/',
        'https://www.ecoauc.com/client',
    ];
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

        foreach($request->get('services') as $service) {

            $notificationService = new NotificationService();
            $notificationService->fill([
                "notification_id" => $notification->id,
                "service" => $service,
            ]);

            $notificationService->save();
        }

        return redirect()->action([NotificationController::class, 'index']);
    }

    public function scrape(Request $request) 
    {
        $keyword = $request->get('keyword');
        $lower_price = $request->get('lower_price');
        $upper_price = $request->get('upper_price');
        $excluded_word = $request->get('excluded_word');
        $services = $request->get('services');
        $status = $request->get('status');

        $results = [];
        $count = 1;
        
        foreach($services as $service) {
            // $client = new Client(HttpClient::create(['timeout' => 60]));
            // $serviceIndex = 'none';
            // if(str_contains($service, 'wowma')) {
            //     $serviceIndex = 'wowma';
            // }else if()
            if($count > 5) break;
            $new = mb_convert_encoding($keyword, "SJIS", "UTF-8");
            $response = Http::get('https://wowma.jp/catalog/api/search/items', [
                'keyword' => $new,
                'e_scope' => 'O',
                'user' => 39095799,
                'x' => 0,
                'y' => 0,
                'page' => 0,
                'uads' => 0,
                'acc_filter' => 'N',
                'shop_only' => 'Y',
                'ref_id' => 'catalog_klist2',
                'mode' => 'pc',
            ]);
            $pages = $response->object();
            $pageInfo = $pages->pageInformation;
// itemImageUrl;currentPrice
            for($i = 0; $i < $pageInfo->totalPages; $i++) {
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
                $hitItems = $response->object()->hitItems;
                foreach($hitItems as $item) {
                    if($count > 5) break;
                    if(isset($lower_price) && isset($upper_price)){
                        if(($item->currentPrice >= $lower_price) && ($item->currentPrice <= $upper_price)){
                            array_push($results, [
                                'currentPrice' => $item->currentPrice,
                                'itemImageUrl' => $item->itemImageUrl,
                                'itemName' => $item->itemName,
                            ]);
                            $count++;
                        }
                    }else{
                        array_push($results, [
                            'currentPrice' => $item->currentPrice,
                            'itemImageUrl' => $item->itemImageUrl,
                            'itemName' => $item->itemName,
                        ]);
                        $count++;
                    }
                }
            }

            //2ndstreet
            // $response = Http::get('https://www.2ndstreet.jp/searchapi/getCount', [
            //     'keyword' => $keyword,
            //     '_' => 1673854048585,
            // ]);
            // $pages = $response->object();
            $str = '';
            foreach($results as $item) {
                $str .= '<div class="col-xl-12 col-md-12">
                <div class="d-flex">
                    <div style="width:100px;height:100px;">
                        <img src="'.$item['itemImageUrl'].'" class="img-fluid" alt="result">
                    </div>
                    <div class="col-xl-8 col-md-8 p-2">
                        <h6 class="mt-0 mb-1 text-danger">'.$item['currentPrice'].'円</h6>
                        <p class="text-muted mb-0 font-13">'.$item['itemName'].'</p>
                        <p class="text-muted mb-0 font-13">wowma</p>
                    </div>
                </div>
            </div>';
            }
            // $str = 'シャツ';
            // $keyword = mb_convert_encoding($str,"SJIS","auto");dd($keyword);
            // $response = Http::get('https://wowma.jp/catalog/api/search/items?keyword='.$keyword.'&e_scope=O&user=39095799&x=0&y=0&page=9&uads=0&acc_filter=N&shop_only=Y&ref_id=catalog_klist2&mode=pc');
            // $response = Http::get('https://www.2ndstreet.jp/searchapi/getCount?keyword=%E3%82%B7%E3%83%A3%E3%83%84&&_=1673577370938');

            // $pages = $response->object()->pageInformation->totalCount;dd($pages);
            // ($crawler->filter('searchListingPagination li')->count() > 0)
            //     ? $crawler->filter('footer .pagination li:nth-last-child(2)')->text()
            //     : 0
            // ;
    
            // for ($i = 0; $i < $pages + 1; $i++) {
            //     if ($i != 0) {
            //         $crawler = Goutte::request('GET', env('FUNKO_POP_URL').'/'.$collection.'?page='.$i);
            //     }
    
            //     $crawler->filter('.product-item')->each(function ($node) {
            //         $sku   = explode('#', $node->filter('.product-sku')->text())[1];
            //         $title = trim($node->filter('.title a')->text());
    
            //         print_r($sku.', '.$title);
            //     });
            // }
        }

        return $str;
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

        return view('notifications.show',compact('notification'));
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
