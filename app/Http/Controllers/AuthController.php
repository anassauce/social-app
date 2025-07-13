<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->expectsJson()) {
            $token = null;
            if ($request->header('X-Requested-With') !== 'XMLHttpRequest') {
                $token = $user->createToken('API Token')->plainTextToken;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'message' => $token ? 'API token provided for non-browser requests' : 'Use session-based authentication for web requests'
                ],
                'message' => 'User registered successfully'
            ], 201);
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        $user = Auth::user();
        
        if ($request->expectsJson()) {
            $token = null;
            if ($request->header('X-Requested-With') !== 'XMLHttpRequest') {
                $token = $user->createToken('API Token')->plainTextToken;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'message' => $token ? 'API token provided for non-browser requests' : 'Use session-based authentication for web requests'
                ],
                'message' => 'Login successful'
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            Auth::logout();
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }

    public function profile(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $request->user(),
                'message' => 'Profile retrieved successfully'
            ]);
        }

        return view('auth.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'email']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile updated successfully'
            ]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }
}