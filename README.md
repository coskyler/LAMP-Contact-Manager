# MyContacts

A **containerized LAMP stack** contact manager deployed behind an **NGINX reverse proxy**, engineered for **secure**, **scalable**, and **team-oriented** development using **Docker Compose**.

## Features
- **Secure authentication** - password hashing and managed sessions  
- **Full contact management** - add, edit, search, and delete entries  
- **Optimized n-gram search** - fast, accurate, and scalable name matching   
- **Persistent MySQL storage** - ensuring reliable data integrity  

## Tech Stack
- **Backend:** PHP, MySQL  
- **Frontend:** HTML, CSS, JavaScript  
- **Proxy:** NGINX (TLS termination, routing to Apache)  
- **Deployment:** Docker Compose (multi-service stack)

## Setup
1. Clone or download this repository  
2. Navigate to the project root directory  
3. Create a `.env` file with required variables
4. Run `docker-compose up -d`  
5. Visit `http://localhost` in your browser  

## Contributors
- [**griffey005**](https://github.com/griffey005) — Project Manager  
- [**RYU-P**](https://github.com/RYU-P) — Frontend  
- [**coskyler**](https://github.com/coskyler) — API Endpoints  
- [**Bingums**](https://github.com/Bingums) — Database  
