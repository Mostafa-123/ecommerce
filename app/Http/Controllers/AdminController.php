<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Http\responseTrait;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use responseTrait;


    public function loginAdmin(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = Auth::guard('admins')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admin = Auth::guard('admins')->user();
        $admin->api_token = $token;
        return new AdminResource($admin);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone'=>'required|string|between:5,15',
            'photo'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $admin = Admin::create(array_merge(
                    $validator->validated(),
                    [
                        'password' => bcrypt($request->password),
                        'photo' => $this->uploadFile($request,'adminsImages','photo')
                    ]
                ));
        return response()->json([
            'message' => 'admin successfully added',
            'admin' => new AdminResource($admin)
        ], 201);
    }

    public function addUser(Request $request) {
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
        return response()->json([
            'message' => 'user successfully added',
            'admin' => new UserResource($user)
        ], 201);
    }

    public function getUserCount(){
        $users=count(User::get());
        return $users;
    }

    public function getAdminsCount(){
        $admins=count(Admin::get());
        return $admins;
    }

    public function getAllUsers(){
        $users=User::get();
        if($users){
            foreach($users as $user){
                $data[]=new UserResource($user);
            }
            return $this->response($data,"users returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function getAllAdmins(){
        $admins=Admin::get();
        if($admins){
            foreach($admins as $admin){
                $data[]=new AdminResource($admin);
            }
            return $this->response($data,"admins returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function deleteUser($user_id){
        $user=User::find($user_id);

        if(!$user){
            return $this->response(null,'The user  Not Found',404);
        }else if ($user){
            $photo=$user->photo;
            if($photo){
                $this->deleteFile($photo);

            }
            $user->delete();
            return $this->response('','The user  deleted',200);
        }
    }


    public function deleteAdmin($admin_id){
        $admin=Admin::find($admin_id);

        if(!$admin){
            return $this->response(null,'The admin  Not Found',404);
        }else if ($admin){
            $photo=$admin->photo;
            if($photo){
                $this->deleteFile($photo);
            }
            $admin->delete();
            return $this->response('','The admin  deleted',200);
        }
    }

    public function updateAdmin(Request $request, $admin_id)
    {
        $admin = Admin::find($admin_id);
        if ($admin) {
            $photo = $request->photo;
            if ($photo && $admin->photo) {
                $this->deleteFile($admin->photo);
                $photo = $this->uploadFile($request, 'adminsImages', 'photo');
            } elseif ($photo != null && $admin->photo == null) {
                $photo = $this->uploadFile($request, 'adminsImages', 'photo');
            } else {
                $photo = $admin->photo;
            }
            $newData = [
                'name' => $request->name ? $request->name : $admin->name,
                'password' => $request->password ? bcrypt($request->password) : $admin->password,
                'phone' => $request->phone ? $request->phone : $admin->phone,
                'photo' => $photo,
            ];
            $admin->update($newData);
        }
        return $this->response(new AdminResource($admin), 'admin updated successfully', 201);
    }

    public function logoutAdmin() {
        Auth::guard('admins')->logout();
        return response()->json(['message' => 'admin successfully signed out']);
    }

    public function adminProfile() {
        return response()->json(new AdminResource(Auth::guard('admins')->user()));
    }

    public function getAdminPhoto($admin_id)
    {
        $admin = Admin::find($admin_id);
        if ($admin) {
            if ($admin->photo) {
                return $this->getFile($admin->photo);
            }
            return $this->response("", "This admin doesn't has photo", 404);
        }
        return $this->response("", 'this admin_id not found', 401);
    }
}


