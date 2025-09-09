<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use NotificationChannels\Telegram\Telegram;
use NotificationChannels\Telegram\TelegramFile;

class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send a daily device report to Telegram.';

    /**
     * Execute the console command.
     */
    public function handle(Telegram $telegram)
    {
        $this->info('Generating daily device report...');

        $devices = Device::all();
        $csvHeader = [
            'ID', 'Name', 'Client Hostname', 'Model Number', 'Serial Number',
            'Service Tag', 'Version', 'Manufacturer', 'Status', 'Device ID',
            'Created At', 'Updated At',
        ];

        $csvData = implode(',', $csvHeader);

        foreach ($devices as $device) {
            $csvRow = [
                $device->id,
                $device->name,
                $device->client_hostname,
                $device->model_number,
                $device->serial_number,
                $device->service_tag,
                $device->version,
                $device->manufacturer,
                $device->status,
                $device->device_id,
                $device->created_at,
                $device->updated_at,
            ];
            $csvData .= "\n" . implode(',', $csvRow);
        }

        // Store the CSV in a temporary file
        $fileName = 'daily_device_report_' . now()->format('Y-m-d') . '.csv';
        $filePath = storage_path('app/' . $fileName);
        File::put($filePath, $csvData);

        $this->info("Report saved to {$filePath}");

        // Send the file to Telegram
        $telegram->sendFile([
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'document' => $filePath,
            'caption' => 'Here is the daily device report for ' . now()->format('Y-m-d') . '.',
        ]);

        // Clean up the temporary file
        File::delete($filePath);

        $this->info('Daily device report sent to Telegram successfully.');

        return 0;
    }
}
