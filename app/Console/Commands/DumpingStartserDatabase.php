<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class DumpingStartserDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dumping-starter-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connections = ['mysql_db_starter', 'mysql_db_starter_demo'];

        foreach($connections as $con) {

            DB::setDefaultConnection($con);
            $cmd = 'app:install';
            if($con == 'mysql_db_starter_demo') {
                $cmd = 'app:install --with_demo=1';
            }
            Artisan::call($cmd);
            $command = 'mysqldump';
            $args = [
                '--user=' . DB::connection()->getConfig('username'),
                '--password=' . DB::connection()->getConfig('password'),
                '--host=' . DB::connection()->getConfig('host'),
                // '--routines', // Optional: Include stored procedures
                DB::connection()->getConfig('database'),
                '>' . database_path(DB::connection()->getConfig('database') . '.sql') // Path to the backup file
            ];
    
            $command .= ' ' . implode(' ', $args);
            
            $process = Process::path(base_path())->start($command);

            $status = $process->wait();

            $this->info($command);

            if ($status->successful()) {
              $this->info('sukses');
            } else {
                $this->info('failed');
               Log::error(json_encode($status));
            }

        }

        // $con = 'mysql_db_starter_demo';

        //  DB::setDefaultConnection($con);
    
        //     $command = 'mysqldump';
        //     $args = [
        //         '--no-create-info',
        //         '--user=' . DB::connection()->getConfig('username'),
        //         '--password=' . DB::connection()->getConfig('password'),
        //         '--host=' . DB::connection()->getConfig('host'),
        //         // '--routines', // Optional: Include stored procedures
        //         DB::connection()->getConfig('database'),
        //         'assets sliders categories posts products product_varians product_asset',
        //         '>' . database_path('demo/content.sql') // Path to the backup file
        //     ];
    
        //     $command .= ' ' . implode(' ', $args);
            
        //     $process = Process::path(base_path())->start($command);

        //     $status = $process->wait();

        //     $this->info($command);

        //     if ($status->successful()) {
        //       $this->info('sukses dump content');
        //     } else {
        //         $this->info('failed dump content');
        //        Log::error(json_encode($status));
        //     }

        DB::setDefaultConnection('mysql');
    }
}
