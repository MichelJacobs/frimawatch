<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationService;

class NotificationController extends Controller
{
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
