<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Type\TypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    : array {
        return [
            'id'               => $this->id,
            'type'             => new TypeResource($this->type),
            'title'            => $this->title,
            'date_of_creation' => Carbon::createFromFormat('Y-m-d', $this->date_of_creation)->format('d.m.Y'),
            'contracted_at'    => Carbon::createFromFormat('Y-m-d', $this->contracted_at)->format('d.m.Y'),
            'deadline'         => isset($this->deadline) ? Carbon::createFromFormat('Y-m-d', $this->deadline)->format(
                'd.m.Y'
            ) : '',
            'is_chain'         => $this->is_chain ? 'Да' : 'Нет',
            'is_on_time'       => $this->is_on_time ? 'Да' : 'Нет',
            'has_outsource'    => $this->has_outsource ? 'Да' : 'Нет',
            'has_investors'    => $this->has_investors ? 'Да' : 'Нет',
            'worker_count'     => $this->worker_count,
            'service_count'    => $this->service_count,
        ];
    }
}
