<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'company_name', 'branch_count',
        'current_software', 'pain_points', 'preferred_language',
        'preferred_date', 'preferred_time', 'questions',
        'referral_source', 'lead_score', 'status',
    ];

    protected $casts = [
        'current_software' => 'array',
        'pain_points'      => 'array',
        'preferred_date'   => 'date',
    ];

    /**
     * Calculate lead score based on submitted data.
     */
    public static function calculateScore(array $data): int
    {
        $score = 0;

        $branchScores = ['1' => 5, '2-5' => 15, '6-20' => 25, '20+' => 40];
        $score += $branchScores[$data['branch_count'] ?? '1'] ?? 5;

        // Always +25 for requesting a demo
        $score += 25;

        // Pain point bonus (each = +5)
        $painPoints = $data['pain_points'] ?? [];
        $score += count($painPoints) * 5;

        return $score;
    }
}
