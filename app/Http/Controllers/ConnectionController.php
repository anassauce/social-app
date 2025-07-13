<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConnectionController extends Controller
{
    private function isApiRequest(Request $request)
    {
        return $request->expectsJson() || $request->is('api/*');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $connections = Connection::with('connectedUser')
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->paginate(20);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $connections,
                'message' => 'Connections retrieved successfully'
            ]);
        }

        return view('connections.index', compact('connections'));
    }

    public function destroy(Request $request, Connection $connection)
    {
        $user = Auth::user();
        
        if ($connection->user_id !== $user->id && $connection->connected_user_id !== $user->id) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to remove this connection'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Unauthorized to remove this connection');
        }

        DB::transaction(function () use ($connection, $user) {
            Connection::where(function ($query) use ($connection) {
                $query->where('user_id', $connection->user_id)
                      ->where('connected_user_id', $connection->connected_user_id);
            })->orWhere(function ($query) use ($connection) {
                $query->where('user_id', $connection->connected_user_id)
                      ->where('connected_user_id', $connection->user_id);
            })->delete();
        });

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Connection removed successfully'
            ]);
        }

        return redirect()->route('connections.index')->with('success', 'Connection removed successfully');
    }

    public function block(Request $request, User $user)
    {
        $authUser = Auth::user();
        
        if ($authUser->id === $user->id) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot block yourself'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Cannot block yourself');
        }

        DB::transaction(function () use ($authUser, $user) {
            Connection::where(function ($query) use ($authUser, $user) {
                $query->where('user_id', $authUser->id)
                      ->where('connected_user_id', $user->id);
            })->orWhere(function ($query) use ($authUser, $user) {
                $query->where('user_id', $user->id)
                      ->where('connected_user_id', $authUser->id);
            })->delete();

            Connection::create([
                'user_id' => $authUser->id,
                'connected_user_id' => $user->id,
                'status' => 'blocked',
                'connected_at' => now()
            ]);
        });

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'message' => 'User blocked successfully'
            ]);
        }

        return redirect()->route('connections.blocked')->with('success', 'User blocked successfully');
    }

    public function unblock(Request $request, User $user)
    {
        $authUser = Auth::user();
        
        Connection::where('user_id', $authUser->id)
            ->where('connected_user_id', $user->id)
            ->where('status', 'blocked')
            ->delete();

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'message' => 'User unblocked successfully'
            ]);
        }

        return redirect()->route('connections.blocked')->with('success', 'User unblocked successfully');
    }

    public function blocked(Request $request)
    {
        $user = Auth::user();
        
        $blockedUsers = Connection::with('connectedUser')
            ->where('user_id', $user->id)
            ->where('status', 'blocked')
            ->paginate(20);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $blockedUsers,
                'message' => 'Blocked users retrieved successfully'
            ]);
        }

        return view('connections.blocked', compact('blockedUsers'));
    }

    public function suggestions(Request $request)
    {
        $user = Auth::user();
        
        $connectedUserIds = Connection::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->pluck('connected_user_id');

        $blockedUserIds = Connection::where('user_id', $user->id)
            ->where('status', 'blocked')
            ->pluck('connected_user_id');

        $suggestions = User::whereNotIn('id', $connectedUserIds)
            ->whereNotIn('id', $blockedUserIds)
            ->where('id', '!=', $user->id)
            ->limit(10)
            ->get();

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'message' => 'Connection suggestions retrieved successfully'
            ]);
        }

        return view('connections.suggestions', compact('suggestions'));
    }

    public function mutualConnections(Request $request, User $user)
    {
        $authUser = Auth::user();
        
        if (!$authUser->isConnectedTo($user->id)) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not connected to this user'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'You are not connected to this user');
        }

        $authUserConnections = Connection::where('user_id', $authUser->id)
            ->where('status', 'accepted')
            ->pluck('connected_user_id');

        $userConnections = Connection::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->pluck('connected_user_id');

        $mutualConnectionIds = $authUserConnections->intersect($userConnections);

        $mutualConnections = User::whereIn('id', $mutualConnectionIds)
            ->limit(10)
            ->get();

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $mutualConnections,
                'message' => 'Mutual connections retrieved successfully'
            ]);
        }

        return view('connections.mutual', compact('mutualConnections', 'user'));
    }
}