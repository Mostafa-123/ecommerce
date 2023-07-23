<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     *
     */
    public function toArray($request): array
    {
        $role='user';
        $photo=$this->photo;
        if($photo){
            $photo="http://127.0.0.1:8000/api/userPhoto/".$this->id;
        }
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'password'=>$this->password,
            'phone'=>$this->phone,
            'address'=>$this->address,
            'language'=>$this->language,
            'country'=>$this->country,
            'city'=>$this->city,
            'photo'=>$photo,
            'role'=>$role,
            'token'=>$this->api_token
        ];
    }
}
