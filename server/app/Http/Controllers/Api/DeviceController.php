<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Notifications\DeviceBulkStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DeviceController extends Controller
{
    public function sync(Request $request)
    {
        $validatedData = $request->validate([
            'client_hostname' => 'required|string',
            'devices' => 'present|array',
            'devices.*.Device Name' => 'required|string',
            'devices.*.Model Number' => 'nullable|string',
            'devices.*.Series Number' => 'nullable|string',
            'devices.*.Service Tag' => 'nullable|string',
            'devices.*.Version' => 'nullable|string',
            'devices.*.Manufacturer' => 'nullable|string',
            'devices.*.Status' => 'required|string',
            'devices.*.DeviceID' => 'required|string',
        ]);

        $clientHostname = $validatedData['client_hostname'];
        $newDevices = [];
        $onlineDevices = [];
        $offlineDevices = [];

        // Get a map of previously online devices for efficient lookup
        $previouslyOnlineDevices = Device::where('client_hostname', $clientHostname)
            ->where('status', 'Online')
            ->get()
            ->keyBy('device_id');

        // Mark all devices for this client as offline before the sync.
        Device::where('client_hostname', $clientHostname)->update(['status' => 'Offline']);

        $syncedDeviceIds = [];
        foreach ($validatedData['devices'] as $deviceData) {
            $deviceId = $deviceData['DeviceID'];
            $syncedDeviceIds[] = $deviceId;

            // Find the device before updating/creating to check its old status
            $existingDevice = Device::where('device_id', $deviceId)
                ->where('client_hostname', $clientHostname)
                ->first();

            $device = Device::updateOrCreate(
                [
                    'device_id' => $deviceId,
                    'client_hostname' => $clientHostname,
                ],
                [
                    'name' => $deviceData['Device Name'],
                    'model_number' => $deviceData['Model Number'],
                    'serial_number' => $deviceData['Series Number'],
                    'service_tag' => $deviceData['Service Tag'],
                    'version' => $deviceData['Version'],
                    'manufacturer' => $deviceData['Manufacturer'],
                    'status' => 'Online', // Always set status to Online for synced devices
                ]
            );

            if ($device->wasRecentlyCreated) {
                $newDevices[] = $device;
            } elseif ($existingDevice && $existingDevice->status === 'Offline') {
                $onlineDevices[] = $device;
            }

            // Remove the device from the previously online list as it's still present
            if ($previouslyOnlineDevices->has($deviceId)) {
                $previouslyOnlineDevices->forget($deviceId);
            }
        }

        // Any devices remaining in this collection are now offline
        $offlineDevices = $previouslyOnlineDevices->all();

        // Check if there are any changes to notify about
        if (!empty($newDevices) || !empty($onlineDevices) || !empty($offlineDevices)) {
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                ->notify(new DeviceBulkStatusNotification(
                    $clientHostname,
                    collect($newDevices)->all(),
                    collect($onlineDevices)->all(),
                    collect($offlineDevices)->all()
                ));
        }

        return response()->json(['message' => 'Sync successful']);
    }

    public function export()
    {
        $devices = Device::all();
        $csvData = '';
        $csvHeader = [
            'ID',
            'Name',
            'Client Hostname',
            'Model Number',
            'Serial Number',
            'Service Tag',
            'Version',
            'Manufacturer',
            'Status',
            'Device ID',
            'Created At',
            'Updated At',
        ];

        $csvData .= implode(',', $csvHeader) . "\n";

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
            $csvData .= implode(',', $csvRow) . "\n";
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="devices.csv"',
        ];

        return response($csvData, 200, $headers);
    }
}
