<?php

namespace App\Exports;

use App\Models\Notification;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NotificationExport implements FromCollection, WithHeadings, WithMapping
{
    protected $params = array();

    /**
    * Optional headers
    */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function headings(): array
    {
        return ["キーワード","下限価格", "上限価格", "除外ワード","対象のサービス", "商品の状態"];
    }

    /**
    * @var notification $notification
    */
    public function map($notification): array
    {
        $res = array();
        $res[] = $notification->keyword;
        $res[] = $notification->upper_price;
        $res[] = $notification->lower_price;
        $res[] = $notification->excluded_words;
        if(count($notification->services) >= 1 ){
            $service_word = '';
            foreach ($notification->services as $key => $service) {
                if($key == 0) {
                    $service_word = $service->service;
                }else{
                    $service_word .= ','.$service->service;
                }
            }
            $res[] = $service_word;
        }else{
            $res[] = "";
        }
        $res[] = $notification->status;
     
        return $res;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Notification::query();
        $query->where('user_id', $this->params);

        return $query->get();
    }
}
