<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Destination extends Model
{
    protected $fillable = ['name', 'description', 'location', 'category_id', 'region_id', 'image_url', 'rating', 'total_riviews', 'total_orders', 'total_views', 'total_likes', 'popularity'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function packagePricings()
    {
        return $this->hasMany(PackagePricing::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, PackagePricing::class);
    }

    public function updateRating()
    {
        $this->total_reviews = $this->reviews()->count();
        if ($this->total_reviews > 0) {
            $this->rating = $this->reviews()->avg('rating');
        } else {
            $this->rating = 0;
        }
        $this->save();
    }

    public function updateTotalOrders()
    {
        $this->total_orders = $this->orders()->where('status', 'approved')->count();
        $this->save();
        $this->updatePopularity();

    }

    public function updateTotalViews()
    {
        $this->total_views++;
        $this->save();
        $this->updatePopularity();

    }


    public function updatePopularity()
    {
        // Definisikan bobot untuk setiap faktor
        $orderWeight = 0.5; // Bobot untuk total orders
        $viewWeight = 0.3;  // Bobot untuk total views
        $likeWeight = 0.2;  // Bobot untuk total likes

        // Hitung nilai popularity berdasarkan bobot
        $this->popularity =
            ($this->total_orders * $orderWeight) +
            ($this->total_views * $viewWeight) +
            ($this->total_likes * $likeWeight);

        // Simpan perubahan ke database
        $this->save();
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorites');
    }

    public function incrementLikes()
    {
        $this->total_likes++;
        $this->save();
    }

    public function decrementLikes()
    {
        if ($this->total_likes > 0) {
            $this->total_likes--;
            $this->save();
        }
    }

}
?>