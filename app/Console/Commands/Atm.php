<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AtmInputHandler;

class Atm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atm:process-and-output {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validates customer account details, handles balance inquiries and cash withdrawals.  the --filename option allows you to choose any file in the /storage/json directory';

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
     * @return int
     */
    public function handle()
    {
        $atm = new AtmInputHandler;
        $result = $atm->processAtmData($this->argument("filename"));
        if($result['status'] === "failure") {
            $this->info($result['errorMessage']);
            return 0;
        }
        $this->info("Output file written to " . storage_path() . '/json/' . $this->argument("filename") . '-output.json');
    }
}
