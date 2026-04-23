# CareerCompass – AI-Powered Career Guidance System

CareerCompass is a smart web-based career guidance platform built to help students and graduates make informed career decisions using AI-driven recommendations based on their skills, interests, and academic background.

---

## Key Features

-  AI-powered career recommendation engine
-  Personalized career suggestions based on user profile
-  Secure authentication system (Login/Register)
-  Dynamic user dashboard
-  Multi-API AI integration (Groq, OpenAI, Gemini, HuggingFace)
-  Google OAuth login integration
-  Email notification system
-  Fast and responsive UI using Bootstrap
-  Modular and scalable Laravel architecture

---

##  Tech Stack

- **Backend:** Laravel (PHP)
- **Frontend:** Blade Templates + Bootstrap
- **Database:** MySQL
- **Authentication:** Laravel Auth + Google OAuth
- **AI Services:** Groq, OpenAI, Gemini, HuggingFace APIs
- **Queue System:** Database Queue
- **Mail Service:** SMTP (Gmail / configurable)

---

##  System Overview

CareerCompass uses a multi-layer AI approach:

- User inputs skills, interests, and education details
- Data is processed through AI service layer
- Multiple AI models generate career suggestions
- Best result is returned to the user dashboard

---

##  Architecture
User → Laravel Controller → AI Service Layer → External AI APIs → Response Engine → Dashboard UI

---


##  Screenshots

### Dashboard
![Dashboard](screenshots/dashboard.png)

### Login Page
![Login](screenshots/login.png)

### Career Recommendations
![Recommendations](screenshots/recommendations.png)
