<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'in_time' => $this->in_time,
            'out_time' => $this->out_time,
            'working_hours' => $this->working_hours,
            'date' => $this->date,
        ];
    }
}
