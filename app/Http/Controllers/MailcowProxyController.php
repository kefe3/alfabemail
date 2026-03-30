<?php

namespace App\Http\Controllers;

use App\Services\MailcowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailcowProxyController extends Controller
{
    public function __construct(private MailcowService $mailcow) {}

    public function status(): JsonResponse
    {
        return response()->json([
            'ok'         => true,
            'configured' => $this->mailcow->isConfigured(),
            'domain'     => config('mailcow.domain'),
        ]);
    }

    public function listMailboxes(): JsonResponse
    {
        $mailboxes = $this->mailcow->listMailboxes();
        return response()->json(['ok' => true, 'data' => $mailboxes]);
    }

    public function getQuota(string $email): JsonResponse
    {
        $quota = $this->mailcow->getMailboxQuota($email);
        return response()->json(['ok' => true, 'data' => $quota]);
    }

    public function createMailbox(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'nickname'   => 'nullable|string|max:100',
            'quota_mb'   => 'nullable|integer|min:256|max:10240',
        ]);

        $result = $this->mailcow->createStudentMailbox(
            $data['first_name'],
            $data['last_name'],
            $data['nickname'] ?? null,
            $data['quota_mb'] ?? 0
        );

        return response()->json(['ok' => true, 'data' => $result], 201);
    }

    public function deleteMailbox(string $email): JsonResponse
    {
        $this->mailcow->deleteMailbox($email);
        return response()->json(['ok' => true, 'message' => "{$email} silindi."]);
    }
}
