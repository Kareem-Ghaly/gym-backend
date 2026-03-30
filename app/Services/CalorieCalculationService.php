<?php

namespace App\Services;

class CalorieCalculationService
{
    /**
     * Activity level multipliers
     */
    private const ACTIVITY_MULTIPLIERS = [
        'sedentary' => 1.2,      // Little or no exercise
        'light' => 1.375,        // Light exercise 1-3 days/week
        'moderate' => 1.55,      // Moderate exercise 3-5 days/week
        'active' => 1.725,       // Hard exercise 6-7 days/week
        'very_active' => 1.9,    // Very hard exercise, physical job
    ];

    /**
     * Goal adjustment factors
     */
    private const GOAL_ADJUSTMENTS = [
        'lose' => -500,      // 500 calorie deficit for weight loss
        'maintain' => 0,     // No adjustment
        'gain' => 500,       // 500 calorie surplus for weight gain
    ];

    /**
     * Calculate BMR (Basal Metabolic Rate) using Mifflin-St Jeor Equation
     *
     * @param float $weight Weight in kg
     * @param float $height Height in cm
     * @param int $age Age in years
     * @param bool $isMale Gender (true for male, false for female)
     * @return float BMR in calories
     */
    public function calculateBMR(float $weight, float $height, int $age, bool $isMale): float
    {
        // BMR = 10 * weight + 6.25 * height - 5 * age + (is_male ? 5 : -161)
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age);
        $bmr += $isMale ? 5 : -161;

        return round($bmr, 2);
    }

    /**
     * Calculate Total Daily Energy Expenditure (TDEE)
     *
     * @param float $bmr BMR in calories
     * @param string $activityLevel Activity level
     * @return float TDEE in calories
     */
    public function calculateTDEE(float $bmr, string $activityLevel): float
    {
        $multiplier = self::ACTIVITY_MULTIPLIERS[$activityLevel] ?? 1.2;
        return round($bmr * $multiplier, 2);
    }

    /**
     * Calculate daily calories based on goal
     *
     * @param float $weight Weight in kg
     * @param float $height Height in cm
     * @param int $age Age in years
     * @param bool $isMale Gender (true for male, false for female)
     * @param string $activityLevel Activity level
     * @param string $goal Goal (lose, maintain, gain)
     * @return int Daily calories
     */
    public function calculateDailyCalories(
        float $weight,
        float $height,
        int $age,
        bool $isMale,
        string $activityLevel,
        string $goal
    ): int {
        // Calculate BMR
        $bmr = $this->calculateBMR($weight, $height, $age, $isMale);

        // Calculate TDEE
        $tdee = $this->calculateTDEE($bmr, $activityLevel);

        // Apply goal adjustment
        $adjustment = self::GOAL_ADJUSTMENTS[$goal] ?? 0;
        $dailyCalories = $tdee + $adjustment;

        // Ensure minimum calories (1200 for safety)
        return max(1200, (int) round($dailyCalories));
    }
}


