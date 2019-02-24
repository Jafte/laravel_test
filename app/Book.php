<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use Searchable;

    protected $table = 'books';


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        unset($array['source']);
        unset($array['year']);
        unset($array['img']);
        unset($array['price']);
        unset($array['created_at']);
        unset($array['updated_at']);
        return $array;
    }
}
