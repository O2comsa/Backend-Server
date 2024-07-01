<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RelatedCourses extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($course) {
            return [
                "id" => $course->id,
                "title" => $course->title,
                "image" => $course->image,
                "completed" => $course->completed,
                "subscribed" => $course->subscribed,
            ];
        });
    }
}
