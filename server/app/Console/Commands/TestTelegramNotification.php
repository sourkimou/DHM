<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeviceStatusNotification;
use App\Models\Device;

class TestTelegramNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-telegram-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a test notification to Telegram.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending test notification to Telegram...');

        // Create a dummy device for the notification
        $device = new Device([
            'name' => 'Test Device',
            'client_hostname' => 'Test Host',
        ]);

        Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                    ->notify(new DeviceStatusNotification($device, 'new'));

        $this->info('Test notification sent!');
    }
}