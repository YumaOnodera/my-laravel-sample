<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $isAdmin = Auth::user()->is_admin;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($isAdmin, $this->email),
            'email_verified_at' => $this->when($isAdmin, $this->email_verified_at?->format('Y-m-d H:i:s')),
            'is_admin' => $this->when($isAdmin, $this->is_admin),
            'created_at' => $this->when($isAdmin, $this->created_at->format('Y-m-d H:i:s')),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at' => $this->when($isAdmin, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
