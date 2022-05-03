<?php

declare (strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Author;
class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'isbn' => $this->isbn,
            'title' => $this->title,
            'description' => $this->description,
            'published_year' => $this->published_year,
            'authors' => $this->authors->map(function (Author $author) {
                            return [
                                'id' => $author->id, 
                                'name' => $author->name, 
                                'surname' => $author->surname
                            ];
                        })->toArray(),
            'review' => [
                'avg' =>  (int) round($this->reviews->avg('review')),
                'count' => (int) $this->reviews->count()
            ],
        ];
    }
}
