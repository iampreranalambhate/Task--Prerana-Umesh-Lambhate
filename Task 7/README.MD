# Task 7: Troubleshooting Guide

This guide explains common problems when deploying containerized applications and how to fix them in simple steps.

## Architecture
![Architecture](../images/architectures/task-7.png)

## 1. App Not Accessible
**Problem:**  
You open the application URL in the browser, but the page does not load.

**What to check:**
- **DNS / IP Address:**  
  Make sure you are using the correct Load Balancer DNS name or EC2 Public IP.
- **Security Group Rules:**  
  Check that inbound rules allow:
  - Port 80 (HTTP) or
  - Port 443 (HTTPS)  
  from `0.0.0.0/0` or your IP address.
- **Internet Gateway:**  
  If the instance is in a public subnet, ensure the VPC has an Internet Gateway attached.

---

## 2. Container Running but Port Not Reachable
**Problem:**  
The Docker container status shows **running**, but the application is not accessible.

**What to check:**
- **Port Mapping:**  
  Confirm correct port mapping. Example: -p 80:3000
This means port `80` on the host forwards to port `3000` inside the container.
- **Listening Address:**  
The application must listen on `0.0.0.0`, not `127.0.0.1`.  
If it listens on localhost, external traffic cannot reach it.
- **Container Logs:**  
Check logs to see if the app failed to start:
docker logs <container_id>


---

## 3. ALB Health Check Failures
**Problem:**  
The Application Load Balancer shows targets as **Unhealthy**.

**What to check:**
- **Health Check Path:**  
Ensure the ALB health check path matches your application endpoint.  
Example: `/` or `/health`
- **Success Response Code:**  
The app must return `200 OK`.  
Codes like `302` or `401` will cause health check failure.
- **Security Group Rules:**  
The EC2 instance security group must allow inbound traffic **from the ALB security group** on the application port.
- **Application Startup Time:**  
If the app takes time to start, increase:
- Health check interval
- Healthy threshold  
in the Target Group settings.

---

## Conclusion
Most issues occur due to:
- Incorrect security group rules
- Wrong port mapping
- Application not listening on the correct address
- ALB health check misconfiguration  

Fixing these usually resolves application access problems.


