<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SignatureDocument;
use App\Models\DocumentActivity;

class DashboardController
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $documents = SignatureDocument::where('owner_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $pendingCount = $documents->filter(fn($d) => $d->status === 'pending')->count();
        $inProgressCount = $documents->filter(fn($d) => $d->status === 'in-progress')->count();
        $signedCount = $documents->filter(fn($d) => $d->status === 'signed')->count();
        $rejectedCount = $documents->filter(fn($d) => $d->status === 'rejected')->count();

        $pendingDocuments = $documents
            ->filter(fn($d) => $d->status === 'pending' || $d->status === 'in-progress')
            ->take(5)
            ->values()
            ->map(fn($doc) => [
                'id' => $doc->id,
                'name' => $doc->document_name,
                'sender' => $user->name,
                'createdAt' => $doc->created_at,
                'status' => $doc->status,
                'signers' => $doc->signers ?? [],
            ]);

        $recentActivities = DocumentActivity::whereIn('document_id', $documents->pluck('id'))
            ->orderByDesc('created_at')
            ->take(6)
            ->get()
            ->map(fn($activity) => [
                'type' => $activity->event_type,
                'title' => $activity->title,
                'description' => $activity->description,
                'date' => $activity->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'pendingCount' => $pendingCount,
                'inProgressCount' => $inProgressCount,
                'signedCount' => $signedCount,
                'rejectedCount' => $rejectedCount,
                'pendingDocuments' => $pendingDocuments,
                'recentActivity' => $recentActivities,
            ],
        ]);
    }
}
