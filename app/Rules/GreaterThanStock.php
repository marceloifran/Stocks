<?php
namespace App\Rules;

use App\Models\Stock;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Rule;

class GreaterThanStock implements Rule
{
    public function passes($attribute, $value)
    {
       $stock = stock::find(request()->stock_id);
         if($stock->cantidad < $value){
              return false;
         }
    }

    public function message()
    {
         return 'El movimiento sera mayor a la cantidad de stock actual, actualiza el stock';
    }
}
