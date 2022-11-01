<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
enum PostType: string
{
    case Draft = "DRAFT";
    case Published = "PUBLISHED";
}
