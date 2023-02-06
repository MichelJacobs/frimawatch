<?php

namespace App\Http\Controllers;

use App\Models\TimeLine;
use App\Http\Requests\StoreTimeLineRequest;
use App\Http\Requests\UpdateTimeLineRequest;

class TimeLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $timelines = TimeLine::where('user_id',auth()->user()->id)->orderBy('id', 'desc')->paginate(50);
        $counts = TimeLine::where('user_id',auth()->user()->id)->count();
        return view('timeline.index',compact('timelines','counts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTimeLineRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimeLineRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeLine  $timeLine
     * @return \Illuminate\Http\Response
     */
    public function show(TimeLine $timeLine)
    {
        //
        
    }

    public function delete()
    {
        //
        TimeLine::where('id',request()->itemId)->delete();
        return "ok";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeLine  $timeLine
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeLine $timeLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTimeLineRequest  $request
     * @param  \App\Models\TimeLine  $timeLine
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeLineRequest $request, TimeLine $timeLine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeLine  $timeLine
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeLine $timeLine)
    {
        //
    }
}
