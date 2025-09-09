Product Requirements Document (PRD)
1. Introduction
The Device Health and Monitoring (DHM) Platform is a two-part system designed to address the lack of visibility into connected peripherals across a large enterprise. It consists of a client agent deployed on 2,500+ Windows 11 PCs and a centralized server with a web-based dashboard running on Ubuntu. The platform's primary function is to collect real-time data on devices and their health status to enable proactive IT management, enhance security, and streamline asset tracking. This PRD details all functional and technical requirements for the project.

2. Problem Statement
IT and management teams lack a unified and automated way to track the inventory and status of devices connected to client computers across an organization. This leads to inefficient troubleshooting, security vulnerabilities from unapproved devices, and difficulty with asset management and compliance audits.

3. Goals and Objectives
P1. Data Collection: Develop a system to automatically and reliably collect key device attributes.

P2. Status Monitoring: Provide real-time online/offline status for all monitored devices.

P3. Centralized View: Create a secure, web-based dashboard for centralized monitoring and management.

P4. Proactive Alerting: Implement a Telegram-based alert system to notify staff of critical events.

P5. Scalability & Reliability: Design a system that can handle a large number of clients and a high volume of data without failure.

4. Target Audience
IT Administrators: Need to quickly verify device status and details for troubleshooting.

Asset Managers: Require an accurate, real-time inventory of all peripherals.

Security Professionals: Must identify and monitor for unauthorized or risky devices.

5. Key Features and Functionality
Client Agent (Windows 11):

Device Discovery: Scans for and identifies all connected peripherals (e.g., USB hubs, printers, scanners, cameras) on boot-up and continuously.

Data Extraction: Collects the following attributes for each device:

Device Name: A human-readable name.

Model Number: Manufacturer's model number.

Series Number: The unique serial number.

Unique Identifier: A vendor-specific unique identifier.

Version: Firmware or driver version.

Status Reporting: Reports the online or offline status to the server.

Secure Communication: Transmits data to the server via an encrypted OpenVPN connection.

Central Dashboard (Ubuntu Server):

Unified Dashboard: Displays a summary of all client computers and their connected devices.

Advanced Filtering: Allows users to filter and search for devices by any collected attribute.

Detailed Device View: Provides a dedicated page for each device showing its full details and status history.

Proactive Alerting: A Laravel-based system that sends notifications to a designated Telegram group for events such as:

A device changing from online to offline.

A new, unknown device connecting to a client.

Telegram Alert Message Format: The alert will be a structured message, formatted clearly to show all relevant device information.

Station Name: [Client Station Name]

1. Device Name: [Device Name]
   Model Number: [Model Number]
   Series Number: [Series Number]
   Status: [Status: Online/Connected/Offline]

2. Device Name: [Device Name]
   Model Number: [Model Number]
   Series Number: [Series Number]
   Status: [Status: Online/Connected/Offline]

[...and so on for all connected devices...]
Automated Reporting: Generates scheduled and on-demand reports on asset inventory and device status history.

6. Technical Requirements
Architecture: A microservices architecture with a message queue (RabbitMQ) to handle data ingestion from 2,500+ clients.

Client Agent: Python running on Windows 11, using libraries like pywin32 or wmi.

HQ Server: Ubuntu Server LTS running a Laravel (PHP) backend with a Filament dashboard.

Database: PostgreSQL for its data integrity, advanced features, and scalability.

Telegram Integration: The backend must use a PHP library to interact with the Telegram Bot API and format the message payload as specified.

Security: All communication must be encrypted (TLS/SSL).

Networking: All client communication to the server will be funneled through an OpenVPN connection.

7. Success Metrics
Data Accuracy: >98% success rate in accurately collecting device data.

Troubleshooting Time: A measurable 50% reduction in time to resolve device-related issues.

User Satisfaction: A high NPS (Net Promoter Score) from the HQ team.

PRD Checklist
Product Information
[X] Project Name: Device Health and Monitoring (DHM) Platform

[X] Problem Statement: Inability to centrally monitor device status on 2,500+ client computers.

[X] Target Audience: IT Administrators, Asset Managers, and Security Professionals.

Product Goals & Objectives
[X] P1. Develop a scalable data collection system.

[X] P2. Implement real-time status monitoring.

[X] P3. Create a centralized, secure dashboard.

[X] P4. Integrate a Telegram-based alerting system.

[X] P5. Ensure the system can handle a large-scale deployment.

Functional Requirements
Client Agent (Windows 11)
[X] Device Discovery: The agent can find all connected peripherals.

[X] Data Extraction: The agent collects the following attributes for each device:

[X] Device Name

[X] Model Number

[X] Series Number

[X] Unique Identifier

[X] Version

[X] Status Reporting: The agent reports a device's online/offline status.

[X] Communication: The agent securely transmits data to the server via OpenVPN.

Central Dashboard (Ubuntu Server)
[X] Dashboard View: Displays a summary of all clients and devices.

[X] Filtering: Allows advanced filtering and searching of data.

[X] Detailed View: Provides a dedicated page with full device details.

[X] Proactive Alerting: The system sends real-time notifications to Telegram with a specific, structured format.

[X] Alert for a device going offline.

[X] Alert for a new/unknown device connecting.

[X] Reporting: The dashboard can generate and export reports.

Technical Requirements
[X] Architecture: Microservices with a message queue.

[X] Client Language: Python.

[X] Server OS: Ubuntu Server LTS.

[X] Backend Framework: Laravel with Filament.

[X] Database: PostgreSQL.

[X] Security: All communication uses TLS/SSL encryption.

[X] Networking: All client traffic uses OpenVPN.

Success Metrics
[X] Data Accuracy: >98%

[X] Troubleshooting Time Reduction: 50%

[X] User Satisfaction: High NPS (Net Promoter Score)