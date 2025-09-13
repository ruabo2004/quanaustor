<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_categories',
        'preferred_styles',
        'preferred_materials',
        'preferred_fits',
        'preferred_sizes',
        'preferred_min_price',
        'preferred_max_price',
        'average_spent',
        'total_products_viewed',
        'total_purchases',
        'conversion_rate',
        'preferred_browsing_time',
        'device_preferences',
        'price_sensitivity',
        'brand_loyalty',
        'style_consistency',
        'impulse_buying',
        'last_analyzed_at'
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'preferred_styles' => 'array',
        'preferred_materials' => 'array',
        'preferred_fits' => 'array',
        'preferred_sizes' => 'array',
        'device_preferences' => 'array',
        'preferred_min_price' => 'decimal:2',
        'preferred_max_price' => 'decimal:2',
        'average_spent' => 'decimal:2',
        'conversion_rate' => 'decimal:4',
        'price_sensitivity' => 'decimal:2',
        'brand_loyalty' => 'decimal:2',
        'style_consistency' => 'decimal:2',
        'impulse_buying' => 'decimal:2',
        'last_analyzed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user's price range preference
     */
    public function getPriceRangeAttribute()
    {
        return [
            'min' => $this->preferred_min_price,
            'max' => $this->preferred_max_price
        ];
    }

    /**
     * Check if user prefers a specific category
     */
    public function preferCategory($categoryId)
    {
        return in_array($categoryId, $this->preferred_categories ?: []);
    }

    /**
     * Check if user prefers a specific style
     */
    public function preferStyle($style)
    {
        return in_array($style, $this->preferred_styles ?: []);
    }

    /**
     * Get user's shopping behavior profile
     */
    public function getBehaviorProfileAttribute()
    {
        return [
            'type' => $this->getBehaviorType(),
            'price_sensitivity' => $this->price_sensitivity,
            'brand_loyalty' => $this->brand_loyalty,
            'style_consistency' => $this->style_consistency,
            'impulse_buying' => $this->impulse_buying
        ];
    }

    /**
     * Determine user's behavior type based on metrics
     */
    private function getBehaviorType()
    {
        if ($this->price_sensitivity > 0.7) {
            return 'price_conscious';
        } elseif ($this->brand_loyalty > 0.7) {
            return 'brand_loyal';
        } elseif ($this->impulse_buying > 0.7) {
            return 'impulse_buyer';
        } elseif ($this->style_consistency > 0.7) {
            return 'style_focused';
        } else {
            return 'explorer';
        }
    }

    /**
     * Update preferences based on new data
     */
    public function updateFromBehavior($behaviorData)
    {
        // Update counters
        if (isset($behaviorData['products_viewed'])) {
            $this->total_products_viewed += $behaviorData['products_viewed'];
        }
        
        if (isset($behaviorData['purchases'])) {
            $this->total_purchases += $behaviorData['purchases'];
        }

        // Recalculate conversion rate
        if ($this->total_products_viewed > 0) {
            $this->conversion_rate = $this->total_purchases / $this->total_products_viewed;
        }

        // Update preferences arrays
        if (isset($behaviorData['categories'])) {
            $this->updatePreferenceArray('preferred_categories', $behaviorData['categories']);
        }

        if (isset($behaviorData['styles'])) {
            $this->updatePreferenceArray('preferred_styles', $behaviorData['styles']);
        }

        $this->last_analyzed_at = now();
        $this->save();
    }

    /**
     * Update preference array with weighted scoring
     */
    private function updatePreferenceArray($field, $newItems)
    {
        $current = $this->$field ?: [];
        $updated = [];

        // Add new items with initial weight
        foreach ($newItems as $item) {
            if (isset($updated[$item])) {
                $updated[$item]++;
            } else {
                $updated[$item] = 1;
            }
        }

        // Merge with existing preferences
        foreach ($current as $item) {
            if (isset($updated[$item])) {
                $updated[$item]++;
            } else {
                $updated[$item] = 1;
            }
        }

        // Sort by preference score and take top items
        arsort($updated);
        $this->$field = array_keys(array_slice($updated, 0, 10, true));
    }
}
