<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListFormRequest;
use App\Http\Resources\User\AddsResource;
use App\Models\Add;
use Illuminate\Http\Request;

class AddsController extends Controller
{
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListFormRequest $request)
    {
        $limit=isset($request['limit']) ? $request['limit']:10;

        $adds = Add::paginate($limit);
        return $this->paginateCollection(AddsResource::collection($adds), $request->limit, 'adds');
    }
}
