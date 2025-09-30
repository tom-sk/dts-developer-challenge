# Laravel Project Setup

This guide explains how to set up and run the project locally.

## Requirements
- PHP >= 8.1
- Composer
- Node.js & npm
- SQLite (already included in the repo)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd <project-folder>
2. **Install dependencies**
   ```bash
   composer install
   npm install
   npm run dev
3. **Set up environment file and see DB**
   ```bash
   cp .env.example .env
   php artisan key:generate
   
   DB_CONNECTION=sqlite

   php artisan migrate:fresh --seed

4. **Launch**
   ```bash
   php artisan serve
