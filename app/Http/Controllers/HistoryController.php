<?php

namespace App\Http\Controllers;

use App\Models\HistoryModel;

class History extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $userId = $this->getUserId();
        $lists = HistoryModel::where(['user_id' => $userId])->get();

        return ['status' => $this->status_success, 'info' => $lists];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
        $userId = $this->getUserId();

        HistoryModel::where('user_id', $userId)->delete();
    }
}
