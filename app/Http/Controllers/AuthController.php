<?php
namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    use responseTrait;
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = Auth::guard('users')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::guard('users')->user();
        $user->api_token = $token;
        return new UserResource($user);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone'=>'required|string|between:5,15',
            'address'=>'string',
            'language'=>'string',
            'country'=>'string',
            'city'=>'string',
            'photo'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    [
                        'password' => bcrypt($request->password),
                        'photo' => $this->uploadFile($request,'usersImages','photo')
                    ]
                ));
        $credentials = $request->only(['email', 'password']);
        $token = Auth::guard('users')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $userToken = Auth::guard('users')->user();
        $userToken->api_token = $token;
        return response()->json([
            'message' => 'User successfully registered',
            'user' => new UserResource($userToken)
        ], 201);
    }


    public function updateUser(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $photo = $request->photo;
            if ($photo && $user->photo) {
                $this->deleteFile($user->photo);
                $photo = $this->uploadFile($request, 'usersImages', 'photo');
            } elseif ($photo != null && $user->photo == null) {
                $photo = $this->uploadFile($request, 'usersImages', 'photo');
            } else {
                $photo = $user->photo;
            }
            $newData = [
                'name' => $request->name ? $request->name : $user->name,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'country' => $request->country ? $request->country : $user->country,
                'address' => $request->address ? $request->address : $user->address,
                'phone' => $request->phone ? $request->phone : $user->phone,
                'language' => $request->language ? $request->language : $user->language,
                'city' => $request->city ? $request->city : $user->city,
                'photo' => $photo,
            ];
            $user->update($newData);
        }
        return $this->response(new UserResource($user), 'user updated successfully', 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        Auth::guard('users')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(JWTAuth::refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(new UserResource(Auth::guard('users')->user()));
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' =>new UserResource( auth()->user())
        ]);
    }

    public function getUserPhoto($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            if ($user->photo) {
                return $this->getFile($user->photo);
            }
            return $this->response("", "This user doesn't has photo", 404);
        }
        return $this->response("", 'this user_id not found', 401);
    }
}
