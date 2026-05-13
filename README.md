# Collaborative Itinerary & Group Travel Planner

A full-stack web application for planning group trips, built mainly with PHP using the MVC architectural pattern. The system has everything you need to enjoy a trip with minimal worries about finance, places to go, and even what to eat. Because everything will be planned out by you, for you.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Getting Started](#getting-started)
- [Project Structure](#project-structure)
- [Design Patterns](#design-patterns)
- [Documentation](#documentation)

---

## Overview

Managing a group trip is hard. Coordinating schedules, splitting costs fairly, tracking who is bringing what, and making sure everyone has their paperwork in order, it all falls apart without a system. This project is a collaborative platform where a Trip Leader creates an itinerary, invites members, and the group manages everything in one place.

The system supports three roles:
- **Trip Leader** — full control over the itinerary, budget, members, and compliance
- **Editor** — can manage activities, polls, and trigger rollbacks
- **Member** — can propose activities, vote, track expenses, manage inventory, and more

---

## Features

### Trip Management
- Create itineraries with cover images, date ranges, and descriptions
- Generate secure invitation links with role assignment
- Manage subtrips nested within the main itinerary

### Activities & Scheduling
- Propose, approve, reject, and confirm activities
- Conflict detection for overlapping confirmed activities
- Attendance tracking per activity with going/not-going/pending status
- Transport mode selection between activities

### Finance & Expense Splitting
- Add expenses with even or custom (uneven) splits
- Group fund (kitty) support — members contribute, expenses deduct automatically
- Full refund processing with proportional share reduction
- Debt settlement engine using a minimal transactions algorithm
- Budget limit alerts when spending approaches or exceeds the limit
- Currency tracking per expense

### Polls & Voting
- Create polls per activity with weighted voting (Must Have / Nice to Have / Not Needed)
- Anonymous poll support
- Auto-calculate leading option based on weighted total

### Audit & History
- Every significant change (dates, money, deletions) is logged as a `HistoryLogEntry`
- Snapshot chaining via `previousSnapshotId` enables rollback to any prior state
- Trip Leaders and Editors can revert changes from the history log

### Safety & Compliance
- Members add allergies to their profile — visible to all trip members
- Insurance policy number stored per user — Trip Leader can view compliance status
- Emergency contact management with SOS trigger

### Inventory
- Members declare what they are bringing per activity
- Conflict detection prevents two members from bringing the same item
- Both members notified on conflict with a resolution prompt

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8 |
| Architecture | MVC (Model-View-Controller) |
| Database | MySQL |
| Frontend | HTML5, CSS3, JavaScript |
| Testing | PHPUnit 11 |
| ORM Pattern | Active Record |
| DB Connection | Singleton (PDO) |

---

## Architecture

The project follows a strict **MVC** structure:

```
Core/
├── Controller.php       # Base controller — provides $this->view()
├── Database.php         # Singleton PDO connection
├── Router.php           # URL dispatcher — maps routes to controllers
├── Validator.php        # Input validation helper
└── TimeHelper.php       # UTC/local timezone conversion

App/
├── Controllers/         # Handle HTTP requests, validate input, call models
├── Models/              # Active Record classes — own all SQL for their table
├── Services/            # Stateless business logic (FinanceService, TripSettlementManager)
├── Helpers/             # Auth, HistoryLogger
├── Enums/               # TripRole, ActivityStatus, SplitMethod, etc.
└── Views/               # PHP templates — no SQL, no business logic
```

**Dependency flow:**
```
Router → Controllers → Models → Database (Singleton)
                    → Services
                    → Views
```

---

## Getting Started

### Prerequisites

- [Docker](https://www.docker.com/get-started) 
- [Docker Compose](https://docs.docker.com/compose/)
- Git

### Installation

```bash
# Clone the repository
git clone https://github.com/RealAhmedKhairi/itinerary-planner.git
cd itinerary-planner

# Copy environment file and configure
cp .env.example .env
# Edit .env with your database credentials if needed

# Build and start all containers
docker compose up --build -d

# The app should now be running at http://localhost:8000
```

### Useful Docker Commands

```bash
# Stop all containers
docker compose down

# View running containers
docker ps

# View logs
docker compose logs -f

# Rebuild after code changes
docker compose up --build -d

# Access the PHP container shell
docker compose exec app bash

# Access MySQL inside the container
docker compose exec db mysql -u root -p
```S localhost:8000 -t public/
```



## Project Structure

```
itinerary-planner/
├── public/              # Entry point (index.php), assets
├── Core/                # Framework layer (Router, Database, Controller)
├── App/
│   ├── Controllers/     # 15 controllers
│   ├── Models/          # 16+ domain models + 6 finance models
│   ├── Services/        # FinanceService, TripSettlementManager
│   ├── Helpers/         # Auth, HistoryLogger
│   ├── Enums/           # PHP 8.1 backed enums
│   └── Views/           # HTML templates
├── tests/
│   └── Unit/            # PHPUnit test files per class
├── database/
│   └── schema.sql       # Full database schema (22 tables)
└── docs/                # SRS, ERDs, UML diagrams
```

---

## Design Patterns

**MVC (Model-View-Controller)**
Separates server-side logic (Controllers), database operations (Models), and UI rendering (Views). Controllers never write SQL; Models never write HTML.

**Active Record**
Each model class owns all SQL queries for its table. No raw SQL exists outside the model layer. External classes call `$expense->findById()` or `$tripFinance->readByItinerary()` — never writing queries directly.

**Singleton**
`Database::getInstance()->getConnection()` ensures a single shared PDO connection per request regardless of how many models are instantiated, avoiding redundant database connections.

---

## Documentation

This project was developed as part of **CS251 Software Engineering** at Helwan University and includes full academic documentation:

| Document | Description |
|---|---|
| SRS | 42 functional user requirements across all modules |
| Use Case Diagrams | Full system coverage across 3 actor roles |
| Sequence Diagrams | System-level (black-box) and design-level per use case |
| Communication Diagrams | Key interaction flows |
| Package Diagrams | Class structure and use case groupings |
| Class Diagram | Full OOP model with relationships and cardinality |
| Logical ERD | EER-style entity-relationship diagram |
| Physical ERD | Full schema with data types and constraints |
| OO Metrics | WMC, DIT, NOC, CBO, RFC, LCOM per class |
| White-Box Tests | PHPUnit path testing with CCM-guided coverage |
| Black-Box Tests | Equivalence Partitioning and Boundary Value Analysis |

** 📄 The full SRS document is available [here](https://drive.google.com/file/d/1vi76eRlEzswJybujJoVYiNLKtT26tBWb/view?usp=sharing) **

## Screenshots
<img width="1920" height="936" alt="image" src="https://github.com/user-attachments/assets/41fb2988-f80a-4d09-9fc3-1bf0d53599d6" />
<img width="1920" height="936" alt="image" src="https://github.com/user-attachments/assets/90d04418-75e2-4df9-b916-61baf9ef159a" />
<img width="1920" height="936" alt="image" src="https://github.com/user-attachments/assets/21aca798-91e6-48f7-9c57-e922883b8cbf" />
<img width="1920" height="936" alt="image" src="https://github.com/user-attachments/assets/48fc118c-9d9a-4464-8e1f-321b0f007a57" />
<img width="1920" height="936" alt="image" src="https://github.com/user-attachments/assets/c81492d6-4aea-475f-b1e6-33119d56fba4" />
<img width="1920" height="936" alt="image" src="https://github.com/user-attachments/assets/904bfd90-9c11-4bae-b3ad-09deff99bebe" />
<img width="1920" height="936" alt="image" src="https://github.com/user-attachments/assets/47fe79c8-3746-4c05-91f8-10f03cb9b854" />

---

## License

This project is licenced under the ISC licence. 
