<?php

namespace App\Console\Commands;

use App\Models\Point;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class DevCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test futures';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Auth::loginUsingId(1);
        $point = Point::query()->first();
        dd($point);
    }
}
