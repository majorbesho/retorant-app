# Sprint Backlog & Capacity Plan

**Role:** Project Manager
**Method:** Agile Scrum (2-Week Sprints)
**Capacity:** 2.5 FTE Developers (approx 80 Story Points / Sprint)
**Project:** Retorant Launch

## Sprint 1: The Core Foundation (Completed)
**Dates:** Jan 15 - Jan 31
**Goal:** Operational MVP & Basic AI Connectivity.
**Velocity:** 75 SP

| ID | User Story | Points | Status | Assignee |
|----|------------|--------|--------|----------|
| SP-10 | Public Restaurant SEO Page | 13 | Done | Frontend |
| SP-11 | AI Context API (Redis Cached) | 21 | Done | Backend |
| SP-12 | API Security (Middleware) | 8 | Done | Security |
| SP-13 | Localization Infrastructure | 13 | Done | Fullstack |

---

## Sprint 2: AI Interaction & Commerce (Current)
**Dates:** Feb 1 - Feb 14
**Goal:** Enable "Chat-to-Order" flow via n8n.
**Target Velocity:** 80 SP

| ID | Module | User Story | Points | Priority |
|----|--------|------------|--------|----------|
| SP-15 | **Admin** | Dashboard for Menu Image Uploads | 8 | High |
| SP-16 | **Analytics** | Conversation Logs UI (View Chat History) | 13 | Medium |
| SP-17 | **Security** | API Rate Limiting (Throttle n8n bugs) | 5 | Low |
| SP-18 | **AI** | "Function Calling" definition for n8n (Order JSON) | 21 | High |
| SP-19 | **Billing** | Soft-check limit on "Conversations" usage | 8 | High |

---

## Sprint 3: Resilience & Scale (Planned)
**Dates:** Feb 15 - Feb 28
**Goal:** Monitoring & Self-Serve Onboarding.
**Target Velocity:** 80 SP

| ID | Module | User Story | Points | Priority |
|----|--------|------------|--------|----------|
| SP-20 | **DevOps** | Auto-scaling rules for Redis/Worker nodes | 13 | High |
| SP-21 | **Admin** | "Test your Bot" Playground in Dashboard | 21 | Medium |
| SP-22 | **Onboarding** | Wizard: "Setup your AI Personality" | 13 | Medium |
| SP-23 | **SEO** | Sitemap.xml auto-generation job | 5 | Low |

---

## Backlog (Unscheduled)
*   **Integrations:** POS Sync (Clover/Square).
*   **Marketing:** Auto-email to dormant customers.
