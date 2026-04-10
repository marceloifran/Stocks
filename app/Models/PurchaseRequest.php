<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'product_name',
        'quantity',
        'notes',
        'status',
        'whats_app_template_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'purchase_request_supplier');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
