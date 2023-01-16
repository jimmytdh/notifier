<?php

namespace App\Http\Controllers;

use App\Models\Department;

class DepartmentController extends Controller
{
    public function get_departments()
    {
        try {
            return ['code' => 200, 'results' => Department::get(['department','code'])];
        } catch (\Exception $err) {
            return ['code' => 500, 'message' => $err];
        }
    }
}
