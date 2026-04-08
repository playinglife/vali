<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleText;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasLocaleText;

    /**
     * When true, Laravel snake_cases relation keys in JSON/array output (e.g. Variants → variants).
     * Keep false so keys match relation method names (e.g. Variants, PriceBrackets).
     *
     * @var bool
     */
    public static $snakeAttributes = false;
}
