<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;


class Marker extends Model
{
    use \Dimsav\Translatable\Translatable;
    use Sluggable;
    use SoftDeletes;
    use SluggableScopeHelpers;

    protected $table = 'markers';

    public $translatedAttributes = ['title', 'description'];
    //

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function favourites()
    {
        return $this->belongsToMany('App\User', 'favourite_markers');
    }

    public function category()
    {
        return $this->belongsTo(MarkerCategory::class, 'marker_category_id', 'id');
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'settlement_id', 'id');
    }

    public static function pagination($search_query = null, $settlements, $category, $order_by, $per_page)
    {

        return Marker::
        when($search_query, function ($q) use ($search_query,$order_by) {
            return $q->whereHas('translations', function ($q) use ($search_query,$order_by) {
                $q->where('title', 'LIKE', '%' . $search_query . '%');
            });
        })
            ->when($category, function ($q) use ($category) {
                return $q->whereIn('marker_category_id', $category);
            })
            ->when($settlements, function ($q) use ($settlements) {
                return $q->whereIn('settlement_id', $settlements);
            })
            ->with('category')
            ->with('settlement')
             ->orderBy($order_by, 'desc')
            ->paginate($per_page, ['id', 'slug', 'lat', 'lng', 'views',
                'marker_category_id','settlement_id',
                'created_at', 'updated_at']);

//        if($search_query){
//            return Marker::when($category, function ($q) use ($category) {
//                return $q->whereIn('marker_category_id', $category);})
//                ->select(['markers.*','t.title'])
//                ->join('marker_translations as t', 'markers.id', '=', 't.marker_id')
//                ->groupBy('markers.id')
//                ->where('title','LIKE', '%'.$search_query.'%')
//                ->with('category')
//                ->orderBy( $order_by,'desc')
//                ->paginate($per_page);
//        }
//        return Marker::when($category, function ($q) use ($category) {
//            return $q->whereIn('marker_category_id', $category);})
//            ->select(['markers.*','t.title'])
//            ->join('marker_translations as t', 'markers.id', '=', 't.marker_id')
//            ->groupBy('markers.id')
//            ->with('category')
//            ->orderBy( $order_by,'desc')
//            ->paginate($per_page);
    }

    public static function paginateTrashed($search_query = null, $category, $order_by, $per_page)
    {
        if ($search_query) {
            return Marker::onlyTrashed()
                ->select(['markers.*', 't.title'])
                ->join('marker_translations as t', 'markers.id', '=', 't.marker_id')
                ->groupBy('markers.id')
                ->whereIn('marker_category_id', $category)
                ->where('title', 'LIKE', '%' . $search_query . '%')
                ->with('category')
                ->orderBy($order_by, 'desc')
                ->paginate($per_page);
        }
        return Marker::onlyTrashed()
            ->when($category, function ($q) use ($category) {
                return $q->whereIn('marker_category_id', $category);
            })
            ->select(['markers.*', 't.title'])
            ->join('marker_translations as t', 'markers.id', '=', 't.marker_id')
            ->groupBy('markers.id')
            ->with('category')
            ->orderBy($order_by, 'desc')
            ->paginate($per_page);
    }
}
