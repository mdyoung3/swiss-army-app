<?php

namespace App\Http\Controllers;

use App\Models\BlockedUrl;
use Illuminate\Http\JsonResponse;

class UrlListController extends Controller
{
    public function getBlockedUrls(): JsonResponse
    {
        try {
            $urls = BlockedUrl::orderBy('created_at', 'desc')->get();
            $all_urls = BlockedUrl::all()->url;

            return response()->json([
                'data' => $urls,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching blocked URLs', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to fetch blocked URLs',
            ], 500);
        }
    }

    public function destroyBlockedUrl(BlockedUrl $blockedUrl): JsonResponse
    {
        try {
            $blockedUrl->delete();

            return response()->json([
                'message' => 'Blocked URL removed successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error removing blocked URL', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to remove blocked URL',
            ], 500);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $urls = PiholeUrl::orderBy('created_at', 'desc')->get();

            return response()->json([
                'data' => $urls,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching URLs', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to fetch URLs',
            ], 500);
        }
    }

    public function destroy(PiholeUrl $url): JsonResponse
    {
        try {
            $url->delete();

            return response()->json([
                'message' => 'URL deleted successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting URL', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to delete URL',
            ], 500);
        }
    }
}
