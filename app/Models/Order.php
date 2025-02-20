<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_id',
        'quantity',
        'total_price', 
        'payment_method',
        'payment_proof',
        'status',
        'booking_date',
        'approved_at',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($order) {
    //         if ($order->status === 'approved') {
    //             $order->destination->updatePopularity();
    //         }
    //     });

    //     static::updated(function ($order) {
    //         if ($order->isDirty('status') && $order->status === 'approved') {
    //             $order->destination->updatePopularity();
    //         }
    //     });
    // }


    // Relasi dengan tabel User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan tabel PackagePricing
    // public function packagePricing()
    // {
    //     return $this->belongsTo(PackagePricing::class);
    // }

    // Relasi untuk mendapatkan informasi Package
    // public function package()
    // {
    //     return $this->hasOneThrough(Package::class, PackagePricing::class, 'id', 'id', 'package_pricing_id', 'package_id');
    // }

    // Relasi untuk mendapatkan informasi Destination
    // public function destination()
    // {
    //     return $this->hasOneThrough(Destination::class, PackagePricing::class, 'id', 'id', 'package_pricing_id', 'destination_id');
    // }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function calculateTotalPrice()
    {
        $this->total_price = $this->destination->price * $this->quantity;
        $this->save();
    }

    public function scopePenjualan()
    {
        return $this->where('status', 'approved');
    }

}
