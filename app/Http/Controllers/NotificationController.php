<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user
     */
    public function index(): View
    {
        // Verificar que el usuario es administrador
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo los administradores pueden ver las notificaciones.');
        }
        
        $notifications = auth()->user()->notifications()->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(string $id): JsonResponse
    {
        // Verificar que el usuario es administrador
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Solo los administradores pueden gestionar notificaciones.'
            ], 403);
        }
        
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída',
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        // Verificar que el usuario es administrador
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Solo los administradores pueden gestionar notificaciones.'
            ], 403);
        }
        
        auth()->user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas',
            'unread_count' => 0
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(): JsonResponse
    {
        // Solo administradores pueden ver el contador
        if (!auth()->user()->isAdmin()) {
            return response()->json(['count' => 0]);
        }
        
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Get latest unread notifications for dropdown
     */
    public function getRecent(): JsonResponse
    {
        // Solo administradores pueden ver notificaciones recientes
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'notifications' => [],
                'total_unread' => 0
            ]);
        }
        
        $notifications = auth()->user()->unreadNotifications()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notificación',
                    'message' => $notification->data['message'] ?? '',
                    'type' => $notification->data['type'] ?? 'info',
                    'created_at' => $notification->created_at->diffForHumans(),
                    'action_url' => $notification->data['action_url'] ?? '#'
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'total_unread' => auth()->user()->unreadNotifications()->count()
        ]);
    }
}
