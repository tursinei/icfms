<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitController extends Controller
{
    public function pull()
    {
        $old = getcwd();
        chdir($old);
        $output = shell_exec('git pull');
        $nToBr = str_replace('\n','<br/>', $output);
        echo $nToBr;
    }
}
