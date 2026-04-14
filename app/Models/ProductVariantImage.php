<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductVariantImage extends BaseModel
{
    /**
     * Subfolder on the public disk (under storage/app/public) where files are stored as `{id}.{ext}`.
     */
    public const STORAGE_PATH = 'product/variant-images';

    /**
     * Tried in order; the first existing file wins.
     *
     * @var list<string>
     */
    public const FILENAME_EXTENSIONS = ['jpg', 'png', 'svg', 'webp', 'avif'];

    /**
     * @var list<string>
     */
    protected $appends = ['image'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_variant_id',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function ProductVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Public URL for {@see STORAGE_PATH}/{id}.{ext} on the public disk, if the file exists.
     */
    protected function image(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->resolvedUrl());
    }

    public function resolvedUrl(): ?string
    {
        $id = $this->getKey();
        if ($id === null) {
            return null;
        }

        $disk = Storage::disk('public');

        foreach (self::FILENAME_EXTENSIONS as $ext) {
            $relative = self::STORAGE_PATH.'/'.$id.'.'.$ext;
            if ($disk->exists($relative)) {
                return asset('storage/'.$relative);
            }
        }

        return null;
    }
}
