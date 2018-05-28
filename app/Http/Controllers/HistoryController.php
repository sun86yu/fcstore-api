<?php

namespace App\Http\Controllers;

use App\Models\HistoryModel;
use Dingo\Api\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $userId = $this->getUserId();
        $lists = HistoryModel::where(['user_id' => $userId])->paginate(15);
        $lists = $lists->toArray();

        return ['status' => $this->status_success, 'info' => $lists['data'], 'count' => $lists['total']];
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
