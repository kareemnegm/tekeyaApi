<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageFormRequest;
use App\Interfaces\MessageInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    private MessageInterface $MessageRepository;
    public function __construct(MessageInterface $MessageRepository)
    {
        $this->MessageRepository = $MessageRepository;
    }

    public function sendMessage(MessageFormRequest $request)
    {
        $data = $request->input();
           $data['user_id'] = Auth::user()->id;
        $this->MessageRepository->sendMessage($data);
        return $this->successResponse('success', 200);
    }

    public function ProviderRetrieveMessages(Request $request)
    {
        $limit = $request['limit'] ? $request['limit'] : 10;
        $data['shop_id']=Auth::user()->providerShopDetails->id;
      $messages=  $this->MessageRepository->Messages($data);
      return $this->paginateCollection($messages,$limit,'messages');

        }
}
