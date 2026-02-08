# MediAItor

**MediAItor** is a turn-based AI mediation web application designed to help users resolve conflicts constructively. Built during a 5-hour hackathon, it features "Dr. Harmony," an AI mediator powered by Groq's Llama 3.3 70B API, who provides professional, research-backed mediation responses based on Gottman Method and Nonviolent Communication principles.

Screenshots: https://drive.google.com/drive/u/1/folders/1omU94HAnNa_h0Z34EVjXN-1oe-8QK23U

## Features

-   **Turn-Based Mediation**: Structured 3-step rounds (User 1 -> User 2 -> AI Response).
-   **AI Mediator (Dr. Harmony)**: Uses advanced prompt engineering to act as a neutral, empathetic mediator.
-   **Multiple Conflict Types**: Optimized for Relationship, Family, Roommate, Workplace, and Friendship disputes.
-   **Real-Time Updates**: Frontend polling ensures both users see updates immediately.
-   **Guest Access**: No account creation required to start or join a session.
-   **Session Codes**: simple 6-character codes for connecting partners.

## Tech Stack

-   **Backend**: Laravel 12 (PHP 8.3+)
-   **Frontend**: Blade Templates + Alpine.js
-   **Styling**: Tailwind CSS
-   **Database**: SQLite
-   **AI Provider**: Groq API (Llama 3.3 70B Versatile)

## Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js & NPM
-   A generic SQLite viewer (optional, for debugging)
-   **Groq API Key**: Get a free key at [console.groq.com](https://console.groq.com)

## Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/abdullah2142/Mediator-App.git
    cd Mediator-App
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Setup Environment**
    ```bash
    cp .env.example .env
    ```
    Open `.env` and add your Groq API key:
    ```env
    GROQ_API_KEY=your_api_key_here
    ```

4.  **Generate App Key & Database**
    ```bash
    php artisan key:generate
    touch database/database.sqlite
    php artisan migrate
    ```

5.  **Install & Build Frontend**
    ```bash
    npm install
    npm run build
    ```

6.  **Run the Application**
    ```bash
    php artisan serve
    ```
    The app will be available at `http://localhost:8000`.

## How to Use

1.  **Create a Session**: Open the app, click "Start Mediation", enter your name, and choose a conflict type.
2.  **Invite Partner**: Share the 6-character Session Code with your partner.
3.  **Join Session**: Your partner opens the app in a **separate browser/incognito window**, clicks "Join Session", and enters the code.
4.  **Mediate**: Take turns sharing your perspectives. Dr. Harmony will intervene after every full round to guide the conversation.

## Future Work

-   User accounts for saving session history.
-   Premium features (extended rounds, PDF reports).
-   WebSocket integration (Reverb/Pusher) for true real-time updates.
-   Email notifications for session invites.
-   Mobile application.
