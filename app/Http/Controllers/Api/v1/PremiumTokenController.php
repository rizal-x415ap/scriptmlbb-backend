<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\PremiumToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PremiumTokenController extends Controller
{
    /**
     * Activate a 5-character premium token
     */
    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string|size:5',
            'device_fingerprint' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $tokenInput = strtoupper(trim($request->input('token')));
        $fingerprint = $request->input('device_fingerprint');
        $deviceName = $request->input('device_name', 'Web Browser');

        $premiumToken = PremiumToken::where('token', $tokenInput)->first();

        if (!$premiumToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token premium tidak ditemukan. Silakan periksa kembali kode token Anda.',
            ], 404);
        }

        if (!$premiumToken->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token premium ini sudah tidak aktif.',
            ], 422);
        }

        if ($premiumToken->expires_at && Carbon::now()->greaterThan($premiumToken->expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token premium ini sudah kedaluwarsa.',
            ], 422);
        }

        // Device fingerprint binding check
        if ($premiumToken->device_fingerprint !== null && $premiumToken->device_fingerprint !== $fingerprint) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token ini sudah terikat pada perangkat lain.',
            ], 422);
        }

        // First-time activation for this token
        if ($premiumToken->device_fingerprint === null) {
            $premiumToken->device_fingerprint = $fingerprint;
            $premiumToken->device_name = $deviceName;
            $premiumToken->activated_at = Carbon::now();
            $premiumToken->save();
        }

        $message = ($premiumToken->wasChanged()) 
            ? 'Selamat! Berlangganan Premium berhasil diaktifkan.' 
            : 'Token premium Anda sudah aktif di perangkat ini.';

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'token' => $premiumToken->token,
                'expires_at' => $premiumToken->expires_at ? $premiumToken->expires_at->toIso8601String() : null,
                'device_name' => $premiumToken->device_name,
            ]
        ]);
    }

    /**
     * Verify stored token status from user's localStorage
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string|size:5',
            'device_fingerprint' => 'required|string',
        ]);

        $tokenInput = strtoupper(trim($request->input('token')));
        $fingerprint = $request->input('device_fingerprint');

        $premiumToken = PremiumToken::where('token', $tokenInput)->first();

        if (!$premiumToken || !$premiumToken->isValid($fingerprint)) {
            return response()->json([
                'status' => 'error',
                'is_premium' => false,
                'message' => 'Token premium tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'is_premium' => true,
            'data' => [
                'token' => $premiumToken->token,
                'expires_at' => $premiumToken->expires_at ? $premiumToken->expires_at->toIso8601String() : null,
            ]
        ]);
    }
}
