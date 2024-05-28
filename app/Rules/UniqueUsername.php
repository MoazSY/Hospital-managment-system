<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class UniqueUsername implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tables = ['hospital_manager', 'doctors', 'reseption_employee', 'nurses','warehouse_manager','accounter','laboratorys','consumer_employee','patient'];
        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'userName')){
            if (DB::table($table)->where('userName', $value)->exists()) {
                $fail('The :attribute has already been taken.');
                return;
            }
        }
    }
    }
    }

