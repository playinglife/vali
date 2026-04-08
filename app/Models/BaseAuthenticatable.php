<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleText;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class BaseAuthenticatable extends Authenticatable
{
    use HasLocaleText;

    /**
     * @see \App\Models\BaseModel::$snakeAttributes
     *
     * @var bool
     */
    public static $snakeAttributes = false;
}
