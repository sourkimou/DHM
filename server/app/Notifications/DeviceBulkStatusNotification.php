<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class DeviceBulkStatusNotification extends Notification
{
    use Queueable;

    protected $clientHostname;
    protected $newDevices;
    protected $onlineDevices;
    protected $offlineDevices;

    /**
     * Create a new notification instance.
     */
    public function __construct($clientHostname, $newDevices, $onlineDevices, $offlineDevices)
    {
        $this->clientHostname = $clientHostname;
        $this->newDevices = $newDevices;
        $this->onlineDevices = $onlineDevices;
        $this->offlineDevices = $offlineDevices;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram($notifiable)
    {
        $message = "*Device Status Report for Station: {$this->clientHostname}*\n\n";

        if (!empty($this->newDevices)) {
            $message .= "*✨ New Devices Detected*\n";
            foreach ($this->newDevices as $device) {
                $message .= "- Name: {$device->name}\n";
                $message .= "  Model: {$device->model_number}\n";
                $message .= "  Serial: {$device->serial_number}\n";
            }
            $message .= "\n";
        }

        if (!empty($this->onlineDevices)) {
            $message .= "*✅ Devices Now Online*\n";
            foreach ($this->onlineDevices as $device) {
                $message .= "- Name: {$device->name}\n";
                $message .= "  Model: {$device->model_number}\n";
                $message .= "  Serial: {$device->serial_number}\n";
            }
            $message .= "\n";
        }

        if (!empty($this->offlineDevices)) {
            $message .= "*❌ Devices Now Offline*\n";
            foreach ($this->offlineDevices as $device) {
                $message .= "- Name: {$device->name}\n";
                $message .= "  Model: {$device->model_number}\n";
                $message .= "  Serial: {$device->serial_number}\n";
            }
            $message .= "\n";
        }

        return TelegramMessage::create()
            ->content($message)
            ->options(['parse_mode' => 'Markdown'])
            ->to(env('TELEGRAM_CHAT_ID'));
    }
}
