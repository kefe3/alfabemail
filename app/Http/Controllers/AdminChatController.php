<?php

namespace App\Http\Controllers;

use App\Models\AdminChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function getMessages(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $lastId = $request->input('last_id', 0);

        $messages = AdminChatMessage::with('user')
            ->where('id', '>', $lastId)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'user_id' => $msg->user_id,
                'name' => $msg->user?->name ?? 'Silinmiş Kullanıcı',
                'message' => $msg->message,
                'created_at' => $msg->created_at->format('H:i'),
            ]);

        $allMessages = AdminChatMessage::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'user_id' => $msg->user_id,
                'name' => $msg->user?->name ?? 'Silinmiş Kullanıcı',
                'message' => $msg->message,
                'created_at' => $msg->created_at->format('H:i'),
            ]);

        return response()->json([
            'messages' => $messages,
            'all_messages' => $allMessages,
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $msg = AdminChatMessage::create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        return response()->json([
            'id' => $msg->id,
            'user_id' => $msg->user_id,
            'name' => $user->name,
            'message' => $msg->message,
            'created_at' => $msg->created_at->format('H:i'),
        ]);
    }

    public function getOnlineAdmins(): JsonResponse
    {
        $user = request()->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $threshold = now()->subMinutes(5);

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('is_active', true)
            ->where('last_active_at', '>=', $threshold)
            ->get(['id', 'name', 'last_active_at'])
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'online' => true,
            ]);

        return response()->json(['admins' => $admins]);
    }
}
