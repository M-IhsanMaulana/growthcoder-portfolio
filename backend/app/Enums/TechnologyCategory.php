<?php

namespace App\Enums;

enum TechnologyCategory: string
{
    case Frontend = 'frontend';
    case Backend = 'backend';
    case DevOps = 'devops';
    case Database = 'database';
    case Tools = 'tools';
}
