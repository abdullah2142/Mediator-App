# MediAItor - Project Context Document

> This document provides full context for AI assistants or developers to understand and continue work on this project.

## Project Overview

**MediAItor** is a turn-based AI mediation web app where 2 users can submit their perspectives on a conflict, and an AI mediator (Dr. Harmony) powered by Groq's Llama 3.3 70B API provides professional, research-backed mediation responses.

**Status:** Prototype/MVP - Functional on localhost
**Built for:** Hackathon (5-hour build)

---

## Tech Stack

| Component | Technology |
|-----------|------------|
| Backend | Laravel 12 (PHP 8.3) |
| Frontend | Blade templates + Alpine.js |
| Styling | Tailwind CSS |
| Database | SQLite |
| AI Provider | Groq API (llama-3.3-70b-versatile) |
| Real-time | Polling every 3 seconds (not WebSockets) |
| Auth | Laravel Breeze (optional - supports guest mode) |

---

## Database Schema

### mediation_sessions
```
id              - bigint, primary key
code            - string(6), unique, uppercase alphanumeric
status          - enum: 'waiting_for_partner', 'user1_turn', 'user2_turn', 'ai_responding', 'completed'
conflict_type   - enum: 'relationship', 'family', 'roommate', 'workplace', 'friendship', 'other'
created_at      - timestamp
updated_at      - timestamp
```

### participants
```
id              - bigint, primary key
session_id      - foreign key -> mediation_sessions
user_id         - foreign key -> users (nullable, for guests)
name            - string
role            - enum: 'user1', 'user2'
joined_at       - timestamp
created_at      - timestamp
updated_at      - timestamp
```

### messages
```
id              - bigint, primary key
session_id      - foreign key -> mediation_sessions
participant_id  - foreign key -> participants (nullable for AI messages)
content         - text
type            - enum: 'user', 'ai'
round_number    - integer (starts at 1)
created_at      - timestamp
updated_at      - timestamp
```

### users (Laravel default + added field)
```
is_premium      - boolean, default false (for future premium features)
```

---

## File Structure

```
app/
├── Http/Controllers/
│   ├── SessionController.php      # Main controller: create, join, room, sendMessage, end
│   └── Api/
│       └── SessionApiController.php # Polling endpoint: GET /api/session/{code}/status
├── Models/
│   ├── MediationSession.php       # Session model with status constants & helpers
│   ├── Participant.php            # Participant model (user1/user2)
│   ├── Message.php                # Message model (user/ai messages)
│   └── User.php                   # Laravel user model + is_premium
└── Services/
    ├── GroqService.php            # Handles Groq API HTTP calls
    └── MediationService.php       # Builds Dr. Harmony prompts, processes turns

database/migrations/
├── 2026_01_08_053952_create_mediation_sessions_table.php
├── 2026_01_08_053952_create_participants_table.php
├── 2026_01_08_053952_create_messages_table.php
└── 2026_01_08_053953_add_is_premium_to_users_table.php

resources/views/
├── welcome.blade.php              # Landing page with Start/Join CTAs
└── session/
    ├── create.blade.php           # Create session form (name + conflict type)
    ├── join.blade.php             # Join session form (code + name)
    └── room.blade.php             # Main mediation room with Alpine.js polling

routes/
├── web.php                        # Web routes
└── api.php                        # API routes (polling)

config/
└── services.php                   # Contains 'groq' => ['api_key' => env('GROQ_API_KEY')]
```

---

## Routes

### Web Routes (routes/web.php)
| Method | URI | Name | Controller Action |
|--------|-----|------|-------------------|
| GET | / | home | welcome view |
| GET | /session/create | session.create | SessionController@create |
| POST | /session/create | session.store | SessionController@store |
| GET | /session/join | session.join | SessionController@join |
| POST | /session/join | session.doJoin | SessionController@doJoin |
| GET | /session/{code} | session.room | SessionController@room |
| POST | /session/{code}/message | session.message | SessionController@sendMessage |
| POST | /session/{code}/end | session.end | SessionController@end |

### API Routes (routes/api.php)
| Method | URI | Controller Action |
|--------|-----|-------------------|
| GET | /api/session/{code}/status | SessionApiController@status |

---

## Application Flow

### State Machine
```
1. User creates session     → status: 'waiting_for_partner'
2. Partner joins            → status: 'user1_turn'
3. User 1 submits message   → status: 'user2_turn'
4. User 2 submits message   → status: 'ai_responding'
5. AI generates response    → status: 'user1_turn' (next round)
6. Repeat steps 3-5 or      → status: 'completed' (manual end)
```

### Session Identification
- Sessions identified by 6-character uppercase alphanumeric code (e.g., `ABC123`)
- Participants tracked via Laravel session storage (`session('participant_id')`)
- Guest mode: no login required, just enter display name
- Auth mode: optional Laravel Breeze login

---

## AI Mediator: Dr. Harmony

### Persona
Dr. Harmony is an AI relationship and conflict mediator trained in:
- Gottman Method Couples Therapy (Four Horsemen and antidotes)
- Nonviolent Communication (NVC) by Marshall Rosenberg
- Emotionally Focused Therapy (EFT) by Dr. Sue Johnson
- Harvard Negotiation Project principles

### Prompt Location
Full system prompt is in: `app/Services/MediationService.php` → `buildSystemPrompt()`

### AI Response Flow
1. Both users submit messages for current round
2. Status changes to 'ai_responding'
3. `MediationService::processTurn()` called
4. Builds context with both perspectives + previous rounds
5. Calls Groq API via `GroqService::chat()`
6. Saves AI message to database
7. Status changes to 'user1_turn' for next round

---

## Environment Variables

Required in `.env`:
```
GROQ_API_KEY=your_groq_api_key_here
```

Get free API key at: https://console.groq.com

---

## Key Code References

### Creating a Session
`app/Http/Controllers/SessionController.php:25-52`
- Generates unique 6-char code via `MediationSession::generateCode()`
- Creates session + first participant
- Stores participant_id in Laravel session

### Joining a Session
`app/Http/Controllers/SessionController.php:65-100`
- Validates code exists and status is 'waiting_for_partner'
- Creates second participant
- Updates status to 'user1_turn'

### Submitting Messages
`app/Http/Controllers/SessionController.php:149-198`
- Validates it's the user's turn
- Saves message with round number
- If user2, triggers AI response via `MediationService::processTurn()`

### Polling Endpoint
`app/Http/Controllers/Api/SessionApiController.php:14-60`
- Returns session status, messages, turn info as JSON
- Frontend polls every 3 seconds

### AI Integration
`app/Services/GroqService.php`
- `chat()` method sends request to Groq API
- Uses model: `llama-3.3-70b-versatile`
- OpenAI-compatible format

`app/Services/MediationService.php`
- `generateResponse()` builds full prompt with context
- `buildSystemPrompt()` contains Dr. Harmony persona
- `processTurn()` orchestrates AI response generation

### Frontend Polling (Alpine.js)
`resources/views/session/room.blade.php:211-280`
- `mediationRoom()` Alpine component
- `fetchStatus()` polls `/api/session/{code}/status`
- Auto-updates messages and turn indicators

---

## Testing Locally

Since this requires 2 users:
1. Open http://localhost:8000 in normal browser → Create session
2. Open http://localhost:8000 in Incognito window → Join with code
3. Both windows can now interact as different users

---

## What's Implemented

- [x] Session creation with unique codes
- [x] Session joining
- [x] Turn-based message submission
- [x] AI mediation responses via Groq
- [x] Real-time polling updates
- [x] Guest mode (no login required)
- [x] Multiple conflict types
- [x] Session ending
- [x] Responsive UI with Tailwind

## Not Yet Implemented (Future Ideas)

- [ ] User accounts saving session history
- [ ] Premium features (more rounds, detailed reports)
- [ ] Email notifications when partner joins
- [ ] Export conversation as PDF
- [ ] WebSocket for true real-time (instead of polling)
- [ ] Rate limiting on AI calls
- [ ] Session expiration/cleanup
- [ ] Mobile app

---

## Commands Reference

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear && php artisan view:clear

# Build frontend assets
npm run build

# Watch frontend changes
npm run dev
```

---

## Repository

GitHub: https://github.com/abdullah2142/Mediator-App

---

*Last updated: January 2026*
*Built with Laravel 12, Alpine.js, Tailwind CSS, and Groq AI*
