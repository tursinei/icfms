<?php

namespace App\Http\Controllers;
 
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
