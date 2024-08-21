<?php

namespace App\Enums;

enum Authority: int
{
    case Employee = 0;
    case Contractor = 1;
    case Admin = 2;
}