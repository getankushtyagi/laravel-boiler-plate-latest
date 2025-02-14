# Laravel Boilerplate for Squareboat

This repository is a Laravel 11 boilerplate designed to standardize project setups across the organization. It incorporates best practices and includes several key features out-of-the-box:

- **Authentication** using Laravel Sanctum (suitable for both SPAs and APIs)
- **Role-Based Access Control (RBAC)** via Spatie Laravel Permission
- **API Routing** with a global prefix (e.g., `/api/v1`) using a Route Service Provider
- **Service-Repository Pattern** for a clean separation between business logic and data access
- Preconfigured middleware, caching, and deployment-ready settings

---

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Authentication & Authorization](#authentication--authorization)
- [API Routing](#api-routing)
- [Service-Repository Pattern](#service-repository-pattern)
- [Middleware Configuration](#middleware-configuration)
- [Testing & Usage](#testing--usage)
- [Contributing](#contributing)
- [License](#license)

---

## Requirements

- PHP >= 8.0
- Composer
- Laravel 11
- Node.js & npm (for frontend assets)
- MySQL (or another supported database)

---

## Installation

1. **Clone the Repository**
   ```bash
   git clone <your-repo-url>
   cd laravel_boiler_plate
