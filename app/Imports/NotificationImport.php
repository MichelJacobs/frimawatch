<?php

namespace App\Imports;

use App\Models\Notification;
use App\Models\NotificationService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class NotificationImport implements ToCollection, WithChunkReading, ShouldQueue
{

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function collection(Collection $rows)
    {

        foreach ($rows as $key => $row) 
        {
            if ($key === 0) {
                continue;
            }
            try {
                $notification = Notification::create([
                    'user_id'                => $this->params,
                    'keyword'                => $row[0],
                    'upper_price'            => $row[1],
                    'lower_price'            => $row[2],
                    'excluded_words'         => $row[3],
                    'status'                 => $row[5],
                ]);

                $services = explode(',',$row[4]);
                foreach($services as $key => $service) {
                    $notificationService = new NotificationService();
                    $notificationService->fill([
                        "notification_id" => $notification->id,
                        "service" => $service,
                    ]);

                    $notificationService->save();
                }
            } catch (\Throwable $e) {
                continue;
            }
           
        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
