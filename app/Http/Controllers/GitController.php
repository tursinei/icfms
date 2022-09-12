<?php

namespace App\Http\Controllers;

class GitController extends Controller
{
    public function pull()
    {
        $old = getcwd();
        chdir($old);
        $output = shell_exec('git pull');
        echo nl2br($output);
    }
}
