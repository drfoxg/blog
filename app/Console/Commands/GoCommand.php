<?php

namespace App\Console\Commands;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class GoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Go Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo $this->description, "\n";

        // $postCtrl = new PostController();

        $postCtrl = new CategoryController();

        echo 'store', "\n";
        $post = $postCtrl->store(new Request());

        echo 'show', "\n";
        $postCtrl->show($post);

        echo 'update', "\n";
        $postCtrl->update($post);

        echo 'index', "\n";
        $postCtrl->index();
    }
}
