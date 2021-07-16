<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Excel;
use App\Imports\ClientsImport;

class importExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'importacion de presolicitudes ';

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
     * @return mixed
     */
    public function handle()
    {
        Excel::import(new ClientsImport,public_path().'/clientes.csv',null,\Maatwebsite\Excel\Excel::CSV);     

        $this->info('clientes completados');
    }
}
