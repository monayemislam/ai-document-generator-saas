<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalFactory> */
    use HasFactory;
    protected $fillable = ['project_id', 'content', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
