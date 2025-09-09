import platform
import socket
import json
import time
import re

try:
    import requests
except ImportError:
    print("Error: The 'requests' library is not installed.")
    print("Please install it by running: pip install requests")
    exit()

try:
    import wmi
except ImportError:
    print("\nError: The 'WMI' library is not installed.")
    print("Please install it by running: pip install WMI")
    exit()


SERVER_URL = "http://127.0.0.1:8000/api/devices/sync"
SYNC_INTERVAL = 300  # 5 minutes


def parse_usb_device_id(device_id):
    """
    Parses a USB device ID string to extract VID, PID, and Serial Number.
    Example DeviceID: USB\VID_04F2&PID_B2E5&MI_00\6&1F7B8B3&0&0000
    Example DeviceID with serial: USB\VID_03F0&PID_2B17\CN12345678
    """
    vid = None
    pid = None
    serial_number = None

    parts = device_id.split('\\')
    if len(parts) > 1:
        # Extract VID and PID
        vid_pid_part = parts[1]
        vid_match = re.search(r'VID_([0-9A-F]{4})', vid_pid_part)
        pid_match = re.search(r'PID_([0-9A-F]{4})', vid_pid_part)
        if vid_match:
            vid = vid_match.group(1)
        if pid_match:
            pid = pid_match.group(1)

        # Attempt to extract serial number from the last part
        if len(parts) > 2:
            # Check if the last part looks like a serial number (e.g., not a generic instance ID)
            # This is a heuristic and might not be perfect for all devices.
            last_part = parts[2]
            # Exclude common instance IDs that are not serial numbers
            if not re.match(r'^[0-9A-F]{16}$', last_part) and \
               not re.match(r'^[0-9A-F]{8}$', last_part) and \
               not last_part.startswith('MI_'):
                serial_number = last_part

    return {"vid": vid, "pid": pid, "serial_number": serial_number}


def get_disk_drive_details():
    """
    Gets model and serial number for disk drives.
    """
    disk_details = {}
    c = wmi.WMI()
    for drive in c.Win32_DiskDrive():
        if drive.PNPDeviceID:
            disk_details[drive.PNPDeviceID] = {
                "Model Number": drive.Model,
                "Series Number": drive.SerialNumber.strip() if drive.SerialNumber else None,
            }
    return disk_details


def get_monitor_details():
    """
    Gets serial number for monitors from WMI.
    """
    monitor_details = {}
    try:
        c = wmi.WMI(namespace="root\\wmi")
        for monitor in c.WmiMonitorID():
            pnp_id = monitor.InstanceName.rsplit('_', 1)[0]
            serial = None
            if monitor.SerialNumberID:
                try:
                    serial = "".join(map(chr, monitor.SerialNumberID)).strip()
                except TypeError:
                    serial = None
            
            monitor_details[pnp_id] = {
                "Series Number": serial,
            }
    except wmi.x_wmi:
        # This namespace might not be available or user may not have perms
        pass # Silently fail, as this is an enhancement
    return monitor_details


def get_printer_details():
    """
    Gets details for installed printers.
    """
    printer_details = {}
    c = wmi.WMI()
    for printer in c.Win32_Printer():
        printer_details[printer.Name] = {
            "Model Number": printer.Name, # Use the printer name as model
        }
    return printer_details


def get_network_adapter_details():
    """
    Gets details for network adapters.
    """
    adapter_details = {}
    c = wmi.WMI()
    for adapter in c.Win32_NetworkAdapter():
        if adapter.PNPDeviceID:
            adapter_details[adapter.PNPDeviceID] = {
                "Model Number": adapter.AdapterType,
                "Series Number": adapter.MACAddress,
                "Version": str(adapter.Speed), # Convert speed to string
            }
    return adapter_details

def get_dell_service_tag():
    """
    Retrieves the Dell Service Tag from WMI.
    """
    c = wmi.WMI()
    for bios in c.Win32_BIOS():
        if hasattr(bios, 'SerialNumber') and bios.SerialNumber:
            return bios.SerialNumber.strip()
    return None

def get_connected_devices():
    """
    Scans for and identifies connected PnP devices, enriching with details.
    """
    c = wmi.WMI()
    devices = []
    disk_details = get_disk_drive_details()
    monitor_details = get_monitor_details()
    printer_details = get_printer_details()
    network_adapter_details = get_network_adapter_details()
    service_tag = get_dell_service_tag()

    for device in c.Win32_PnPEntity():
        if device.Name and device.Name.strip():
            device_name = device.Name.strip()
            details = disk_details.get(device.DeviceID, {})
            monitor_info = monitor_details.get(device.DeviceID, {})
            printer_info = printer_details.get(device_name, {})
            adapter_info = network_adapter_details.get(device.PNPDeviceID, {})

            device_info = {
                "Device Name": device_name,
                "Model Number":  details.get("Model Number") or printer_info.get("Model Number") or adapter_info.get("Model Number"),
                "Series Number": details.get("Series Number") or monitor_info.get("Series Number") or adapter_info.get("Series Number"),
                "Service Tag": service_tag,
                "Version": device.DriverVersion if hasattr(device, 'DriverVersion') else None or adapter_info.get("Version"),
                "Manufacturer": device.Manufacturer,
                "Status": device.Status,
                "DeviceID": device.DeviceID,
            }

            # Add USB specific details if it's a USB device
            if device.DeviceID.startswith("USB"):
                usb_details = parse_usb_device_id(device.DeviceID)
                if usb_details["serial_number"]:
                    device_info["Series Number"] = usb_details["serial_number"]
                if usb_details["vid"] and usb_details["pid"]:
                    # For model, we can combine VID and PID if no other model is found
                    if not device_info["Model Number"]:
                        device_info["Model Number"] = f"VID_{usb_details['vid']}&PID_{usb_details['pid']}"

            devices.append(device_info)
    return devices


def send_to_server(client_hostname, devices):
    """
    Sends the collected device data to the server.
    """
    print(f"\nSending {len(devices)} device(s) to the server at {SERVER_URL}...")

    payload = {
        "client_hostname": client_hostname,
        "devices": devices,
    }

    try:
        response = requests.post(SERVER_URL, json=payload, timeout=10)
        response.raise_for_status()
        print("Server response:", response.json())
    except requests.exceptions.RequestException as e:
        print(f"Error sending data to server: {e}")


def main():
    """
    Main function to collect and send device information periodically.
    """
    if platform.system() != "Windows":
        print("This script is designed to run on Windows only.")
        return

    client_hostname = socket.gethostname()
    print(f"DHM Client Agent started for hostname: {client_hostname}")
    print(f"Will sync with server every {SYNC_INTERVAL} seconds.")

    while True:
        print("\n--- Starting device scan... ---")
        all_devices = get_connected_devices()
        print(f"Found {len(all_devices)} devices.")

        send_to_server(client_hostname, all_devices)

        print(f"--- Scan complete. Waiting for {SYNC_INTERVAL} seconds... ---")
        time.sleep(SYNC_INTERVAL)


if __name__ == "__main__":
    main()