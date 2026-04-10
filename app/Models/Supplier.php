<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'category',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseRequests()
    {
        return $this->belongsToMany(PurchaseRequest::class, 'purchase_request_supplier');
    }
}
