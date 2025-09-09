<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;

class GenerateDeviceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate-devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a CSV report of all devices.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $devices = Device::all();

        $csv = Writer::createFromString('');
        $csv->insertOne([
            'Device Name',
            'Model Number',
            'Serial Number',
            'Service Tag',
            'Version',
            'Manufacturer',
            'Status',
            'Client Hostname',
            'Last Updated At',
        ]);

        foreach ($devices as $device) {
            $csv->insertOne([
                $device->name,
                $device->model_number,
                $device->serial_number,
                $device->service_tag,
                $device->version,
                $device->manufacturer,
                $device->status,
                $device->client_hostname,
                $device->updated_at,
            ]);
        }

        $filename = 'device_report_' . now()->format('Ymd_His') . '.csv';
        Storage::put('reports/' . $filename, $csv->toString());

        $this->info('Device report generated: ' . $filename);
    }
}
