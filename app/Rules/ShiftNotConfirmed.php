<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Shift;

class ShiftNotConfirmed implements Rule
{
    protected $availabilityId;

    public function __construct($availabilityId)
    {
        $this->availabilityId = $availabilityId;
    }

    public function passes($attribute, $value)
    {
        return !Shift::where('availability_id', $this->availabilityId)->exists();
    }

    public function message()
    {
        return 'このシフトはすでに確定しているため、変更できません。';
    }
}

