<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role='admin';
        $photo=$this->photo;
        if($photo){
            $photo="http://127.0.0.1:8000/admin/adminPhoto/".$this->id;
        }
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'password'=>$this->password,
            'phone'=>$this->phone,
            'photo'=>$photo,
            'role'=>$role,
            'token'=>$this->api_token
        ];
    }
}
