<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;
    public function __construct(OpenRouterService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        try {
            $aiContent = $this->aiService->askAi($request->message);
            $chatMessage = ChatMessage::create([
                'user_id' => auth()->id(),
                'user_message' => $request->message,
                'ai_response' => $aiContent,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => json_decode($aiContent, true),
                'timestamp' => $chatMessage->created_at->format('Y-m-d H:i')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getHistory(Request $request)
    {
        $history = ChatMessage::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->query('per_page', 15));

        $formattedData = collect($history->items())->map(function($item) {
            return [
                'id' => $item->id,
                'user_message' => $item->user_message,
                'ai_response' => json_decode($item->ai_response, true),
                'created_at' => $item->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'total' => $history->total(),
            'current_page' => $history->currentPage(),
        ]);
    }
}