Based on the detailed project proposal for the **Device Health and Monitoring (DHM) Platform**, here is the Product Requirements Document (PRD) and a version converted to a markdown checklist.

## Product Requirements Document (PRD)

### **1. Introduction**

The **Device Health and Monitoring (DHM) Platform** is a two-part system designed to address the lack of visibility into connected peripherals across a large enterprise. It consists of a client agent deployed on **2,500+ Windows 11 PCs** and a centralized server with a web-based dashboard running on **Ubuntu**. The platform's primary function is to collect real-time data on devices and their health status to enable proactive IT management, enhance security, and streamline asset tracking.

-----

### **2. Problem Statement**

IT and management teams lack a unified and automated way to track the inventory and status of devices connected to client computers across an organization. This leads to inefficient troubleshooting, security vulnerabilities from unapproved devices, and difficulty with asset management and compliance audits.

-----

### **3. Goals and Objectives**

  * **P1. Data Collection:** Develop a system to automatically and reliably collect key device attributes.
  * **P2. Status Monitoring:** Provide real-time online/offline status for all monitored devices.
  * **P3. Centralized View:** Create a secure, web-based dashboard for centralized monitoring and management.
  * **P4. Proactive Alerting:** Implement an alert system to notify staff of critical events via Telegram.
  * **P5. Scalability:** Design a system that can handle a large number of clients and a high volume of data.

-----

### **4. Target Audience**

  * **IT Administrators:** Need to quickly verify device status and details for troubleshooting.
  * **Asset Managers:** Require an accurate, real-time inventory of all peripherals.
  * **Security Professionals:** Must identify and monitor for unauthorized or risky devices.

-----

### **5. Key Features and Functionality**

**Client Agent (Windows 11):**

  * **Device Discovery:** Scans for and identifies all connected peripherals (e.g., USB hubs, printers, scanners, cameras) on boot-up and continuously.
  * **Data Extraction:** Collects the following attributes for each device:
      * **Device Name:** A human-readable name.
      * **Model Number:** Manufacturer's model number.
      * **Series Number:** The unique serial number.
      * **Service Tag:** A Dell-specific identifier.
      * **Version:** Firmware or driver version.
  * **Status Reporting:** Reports the `online` or `offline` status to the server.
  * **Secure Communication:** Transmits data to the server via an encrypted connection.

**Central Dashboard (Ubuntu Server):**

  * **Unified Dashboard:** Displays a summary of all client computers and their connected devices.
  * **Advanced Filtering:** Allows users to filter and search for devices by any collected attribute.
  * **Detailed Device View:** Provides a dedicated page for each device showing its full details and status history.
  * **Proactive Alerting:** A Laravel-based system that sends notifications to a designated Telegram group for events such as:
      * A device changing from `online` to `offline`.
      * A new, unknown device connecting to a client.
  * **Automated Reporting:** Generates scheduled and on-demand reports on asset inventory and device status history.

-----

### **6. Technical Requirements**

  * **Architecture:** A microservices architecture with a message queue (RabbitMQ) to handle data ingestion.
  * **Client Agent:** **Python** running on **Windows 11**, using libraries like `pywin32` or `wmi`.
  * **HQ Server:** **Ubuntu Server LTS** running a **Laravel (PHP)** backend with a **Filament** dashboard.
  * **Database:** **PostgreSQL** for its data integrity, advanced features, and scalability.
  * **Telegram Integration:** The backend must use a PHP library to interact with the Telegram Bot API.
  * **Security:** All communication must be encrypted (TLS/SSL).

-----

### **7. Success Metrics**

  * **Data Accuracy:** \>98% success rate in accurately collecting device data.
  * **Troubleshooting Time:** A measurable 50% reduction in time to resolve device-related issues.
  * **User Satisfaction:** A high NPS (Net Promoter Score) from the HQ team.

\<br\>

\<br\>

## PRD Checklist

### **Product Information**

  * [ ] **Project Name:** Device Health and Monitoring (DHM) Platform
  * [ ] **Problem Statement:** Inability to centrally monitor device status on 2,500+ client computers.
  * [ ] **Target Audience:** IT Administrators, Asset Managers, and Security Professionals.

-----

### **Product Goals & Objectives**

  * [ ] **P1.** Develop a scalable data collection system.
  * [ ] **P2.** Implement real-time status monitoring.
  * [ ] **P3.** Create a centralized, secure dashboard.
  * [ ] **P4.** Integrate a Telegram-based alerting system.
  * [ ] **P5.** Ensure the system can handle a large-scale deployment.

-----

### **Functional Requirements**

#### **Client Agent (Windows 11)**

  * [x] **Device Discovery:** The agent can find all connected peripherals.
  * [ ] **Data Extraction:** The agent collects the following attributes for each device:
      * [x] Device Name
      * [ ] Model Number
      * [ ] Series Number
      * [ ] Service Tag
      * [x] Version
  * [ ] **Status Reporting:** The agent reports a device's `online`/`offline` status.
  * [ ] **Communication:** The agent securely transmits data to the server.

#### **Central Dashboard (Ubuntu Server)**

  * [ ] **Dashboard View:** Displays a summary of all clients and devices.
  * [ ] **Filtering:** Allows advanced filtering and searching of data.
  * [ ] **Detailed View:** Provides a dedicated page with full device details.
  * [ ] **Proactive Alerting:** The system sends real-time notifications to Telegram.
      * [ ] Alert for a device going offline.
      * [ ] Alert for a new/unknown device connecting.
  * [ ] **Reporting:** The dashboard can generate and export reports.

-----

### **Technical Requirements**

  * [ ] **Architecture:** Microservices with a message queue.
  * [x] **Client Language:** Python.
  * [ ] **Server OS:** Ubuntu Server LTS.
  * [ ] **Backend Framework:** Laravel with Filament.
  * [ ] **Database:** PostgreSQL.
  * [ ] **Security:** All communication uses TLS/SSL encryption.
  * [ ] **Telegram Integration:** The backend uses the Telegram Bot API.

-----

### **Success Metrics**

  * [ ] **Data Accuracy:** \>98%
  * [ ] **Troubleshooting Time Reduction:** 50%
  * [ ] **User Satisfaction:** High NPS (Net Promoter Score)