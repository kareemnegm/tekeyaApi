<?php

namespace App\Repositories;

use App\Http\Resources\Message\MessageResource;
use App\Interfaces\MessageInterface;
use App\Models\Message;
use Carbon\Carbon;

class MessageRepository implements MessageInterface
{
    public function sendMessage($data)
    {
        $date = Carbon::now()->format('Y-m-d');
        $day = Carbon::now()->format('l');
        $data['date'] = $date;
        $data['day'] = $day;
        Message::create($data);
    }

    public function Messages($data)
    {
        $messages = Message::where('shop_id', $data['shop_id'])->get();
        return MessageResource::collection($messages);
    }
}
