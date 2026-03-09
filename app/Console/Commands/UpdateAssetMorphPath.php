<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;

class UpdateAssetMorphPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:update-path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assets = Asset::where('assetable_type', 'like', 'App%')->get();

        $this->info($assets->count());

        if($assets->count() > 0) {
            foreach($assets as $asset) {
                $this->info($asset->assetable_type);
                $string = $asset->assetable_type;
                $asset->assetable_type = str_replace("App\Models\\","", $string);
                $asset->save();
            }
        }
    }
}
