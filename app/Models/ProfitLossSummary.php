<?php

namespace App\Models; // <-- Namespace updated to be cleaner

use Illuminate\Contracts\Support\Arrayable;

/**
 * Data Transfer Object (DTO) for the Profit & Loss Report Summary.
 * This model holds aggregated, calculated data, not raw database records.
 */
class ProfitLossSummary implements Arrayable // <-- Class name remains ProfitLossSummary
{
    public float $totalRevenue = 0.00;
    public float $totalCogs = 0.00;
    public float $totalExpenses = 0.00;
    public float $totalNetProfit = 0.00;
    public float $totalInputWeight = 0.00;
    public float $totalOutputWeight = 0.00;
    
    // Detailed daily breakdown data
    public array $dailyReport = [];

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        
        // Ensure total output weight is calculated based on input weight if provided
        if ($this->totalInputWeight > 0 && $this->totalOutputWeight === 0.00) {
            // Mock Calculation for Output Weight (e.g., 90% yield)
            $this->totalOutputWeight = $this->totalInputWeight * 0.9;
        }
        
        // Final calculation
        $this->totalNetProfit = $this->totalRevenue - $this->totalCogs - $this->totalExpenses;
    }

    /**
     * Get the instance as an array.
     * Required by Arrayable interface for easy use in compact() or views.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}