<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    private function isApiRequest(Request $request)
    {
        return $request->expectsJson() || $request->is('api/*');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'received');
        
        $query = Invitation::with(['sender', 'recipient']);
        
        if ($type === 'sent') {
            $query->where('sender_id', $user->id);
        } else {
            $query->where('recipient_id', $user->id);
        }
        
        $invitations = $query->latest()->paginate(20);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $invitations,
                'message' => ucfirst($type) . ' invitations retrieved successfully'
            ]);
        }

        return view('invitations.index', compact('invitations', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500'
        ]);

        $sender = Auth::user();
        $recipientId = $request->recipient_id;

        if ($sender->id === $recipientId) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot send invitation to yourself'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Cannot send invitation to yourself');
        }

        if ($sender->isConnectedTo($recipientId)) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already connected to this user'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'You are already connected to this user');
        }

        $existingInvitation = Invitation::where(function ($query) use ($sender, $recipientId) {
            $query->where('sender_id', $sender->id)
                  ->where('recipient_id', $recipientId);
        })->orWhere(function ($query) use ($sender, $recipientId) {
            $query->where('sender_id', $recipientId)
                  ->where('recipient_id', $sender->id);
        })->where('status', 'pending')->first();

        if ($existingInvitation) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'An invitation already exists between you and this user'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'An invitation already exists between you and this user');
        }

        $invitation = Invitation::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipientId,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $invitation->load(['sender', 'recipient']),
                'message' => 'Invitation sent successfully'
            ], 201);
        }

        return redirect()->back()->with('success', 'Invitation sent successfully');
    }

    public function show(Request $request, Invitation $invitation)
    {
        $user = Auth::user();
        
        if ($invitation->sender_id !== $user->id && $invitation->recipient_id !== $user->id) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this invitation'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Unauthorized to view this invitation');
        }

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $invitation->load(['sender', 'recipient']),
                'message' => 'Invitation retrieved successfully'
            ]);
        }

        return view('invitations.show', compact('invitation'));
    }

    public function accept(Request $request, Invitation $invitation)
    {
        $user = Auth::user();
        
        if ($invitation->recipient_id !== $user->id) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to accept this invitation'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Unauthorized to accept this invitation');
        }

        if ($invitation->status !== 'pending') {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This invitation has already been responded to'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'This invitation has already been responded to');
        }

        DB::transaction(function () use ($invitation) {
            $invitation->accept();
        });

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $invitation->load(['sender', 'recipient']),
                'message' => 'Invitation accepted successfully'
            ]);
        }

        return redirect()->route('invitations.pending')->with('success', 'Invitation accepted successfully');
    }

    public function reject(Request $request, Invitation $invitation)
    {
        $user = Auth::user();
        
        if ($invitation->recipient_id !== $user->id) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to reject this invitation'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Unauthorized to reject this invitation');
        }

        if ($invitation->status !== 'pending') {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This invitation has already been responded to'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'This invitation has already been responded to');
        }

        $invitation->reject();

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $invitation->load(['sender', 'recipient']),
                'message' => 'Invitation rejected successfully'
            ]);
        }

        return redirect()->route('invitations.pending')->with('success', 'Invitation rejected successfully');
    }

    public function destroy(Request $request, Invitation $invitation)
    {
        $user = Auth::user();
        
        if ($invitation->sender_id !== $user->id) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this invitation'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Unauthorized to cancel this invitation');
        }

        if ($invitation->status !== 'pending') {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel an invitation that has already been responded to'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Cannot cancel an invitation that has already been responded to');
        }

        $invitation->delete();

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Invitation cancelled successfully'
            ]);
        }

        return redirect()->route('invitations.sent')->with('success', 'Invitation cancelled successfully');
    }

    public function pending(Request $request)
    {
        $user = Auth::user();
        
        $pendingInvitations = Invitation::with(['sender', 'recipient'])
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $pendingInvitations,
                'message' => 'Pending invitations retrieved successfully'
            ]);
        }

        return view('invitations.pending', compact('pendingInvitations'));
    }

    public function sent(Request $request)
    {
        $user = Auth::user();
        
        $sentInvitations = Invitation::with(['sender', 'recipient'])
            ->where('sender_id', $user->id)
            ->latest()
            ->paginate(20);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $sentInvitations,
                'message' => 'Sent invitations retrieved successfully'
            ]);
        }

        return view('invitations.sent', compact('sentInvitations'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Get all users excluding current user, already connected users, and blocked users
        $connectedUserIds = Connection::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('connected_user_id', $user->id);
        })->where('status', 'accepted')->pluck('connected_user_id')->merge(
            Connection::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('connected_user_id', $user->id);
            })->where('status', 'accepted')->pluck('user_id')
        )->unique();

        $blockedUserIds = Connection::where('user_id', $user->id)
            ->where('status', 'blocked')
            ->pluck('connected_user_id');

        // Get users with pending invitations
        $pendingInvitationUserIds = Invitation::where(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
        })->where('status', 'pending')->pluck('recipient_id')->merge(
            Invitation::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })->where('status', 'pending')->pluck('sender_id')
        )->unique();

        $availableUsers = User::whereNotIn('id', $connectedUserIds)
            ->whereNotIn('id', $blockedUserIds)
            ->whereNotIn('id', $pendingInvitationUserIds)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $availableUsers,
                'message' => 'Available users retrieved successfully'
            ]);
        }

        return view('invitations.create', compact('availableUsers'));
    }

    public function stats(Request $request)
    {
        $user = Auth::user();
        
        $stats = [
            'sent' => [
                'total' => Invitation::where('sender_id', $user->id)->count(),
                'pending' => Invitation::where('sender_id', $user->id)->where('status', 'pending')->count(),
                'accepted' => Invitation::where('sender_id', $user->id)->where('status', 'accepted')->count(),
                'rejected' => Invitation::where('sender_id', $user->id)->where('status', 'rejected')->count(),
            ],
            'received' => [
                'total' => Invitation::where('recipient_id', $user->id)->count(),
                'pending' => Invitation::where('recipient_id', $user->id)->where('status', 'pending')->count(),
                'accepted' => Invitation::where('recipient_id', $user->id)->where('status', 'accepted')->count(),
                'rejected' => Invitation::where('recipient_id', $user->id)->where('status', 'rejected')->count(),
            ]
        ];

        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Invitation statistics retrieved successfully'
            ]);
        }

        return view('invitations.stats', compact('stats'));
    }
}