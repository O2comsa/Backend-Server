<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoursesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return string[]
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "image" => $this->image,
            "price" => $this->price,
            "free" => $this->free,
            "lessons_count" => $this->lessons_count,
            "subscribed" => $this->subscribed,
            'eligible' => $this->eligible,
            "completed" => $this->completed,
            "bookmarked" => $this->bookmarked,
            "prerequisite" => new RelatedCourses($this->courses),
            "lessons" => $this->lessons,
        ];
    }
}
