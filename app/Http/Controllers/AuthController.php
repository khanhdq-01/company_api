<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email'=> 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        unset($user->email_verified_at);
        unset($user->created_at);
        unset($user->deleted_at);
        unset($user->updated_at);
        $user->tokens()->delete();
        $token = $user->createToken('sanctum')->plainTextToken;
        $user->token= $token;


        return response(['data'=>$user]);
    }


    public function logout() {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa đăng nhập hoặc token không hợp lệ'], 401);
        }
    
        $user->tokens()->delete(); // Xóa tất cả token
        return response()->json(['message' => 'Logout thành công'], 200);
    }
    

    public function me(){
        return response(['data' => auth()->user()]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'email'=> 'required|email',
            'password' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                'status'=> false,
                'message'=> $errorMessage,
            ];

            return response()->json($response, 401 );
        }

        User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'role_id'=> '1',
        ]);

        return response()->json([
            'status'=> true,
            'message'=> 'User register successfully',
        ]);
    }
}
