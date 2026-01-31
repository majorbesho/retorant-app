# Business Requirements Document (BRD)

**Role:** Business Analyst
**Method:** User Story Mapping & Acceptance Criteria Definition
**Project:** Retorant SaaS
**Version:** 2.0

## 1. Introduction
This document defines the functional specifications for the Retorant platform, structured as detailed User Stories with verifiable Acceptance Criteria (AC).

## 2. Core User Stories

### Epics
1.  **AI Integration (The Brain)**
2.  **Public Presence (SEO)**
3.  **Subscription & Billing**

---

### Epic 1: AI Integration

#### US-01: AI Context Retrieval
**As an** External AI Agent (n8n),
**I want to** retrieve the full context of a restaurant via a single API call,
**So that** I can answer customer questions efficiently without hallucinating.

**Acceptance Criteria:**
*   **AC1:** Endpoint `GET /api/v1/external/restaurant/{slug}/context` returns HTTP 200 for valid API Key.
*   **AC2:** Response must include: `info` (Address, Hours), `menu` (products with prices), `faqs` (Q&A pairs), and `agent` (tone/prompt).
*   **AC3:** If `?lang=ar` is passed, all text fields must return Arabic values.
*   **AC4:** Response time must be under 300ms (cached).
*   **AC5:** Unauthorized requests (no key) must return HTTP 401.

#### US-02: Conversation Logging
**As an** Admin,
**I want to** log all AI-Customer interactions in the database,
**So that** I can analyze usage and bill based on conversation volume.

**Acceptance Criteria:**
*   **AC1:** API accepts `POST /api/v1/external/conversations` with mandatory `conversation_id` (UUID from n8n), `restaurant_id`, `message_text`, `direction`, and `channel`.
*   **AC2:** System MUST NOT generate a new UUID; it must use the provided `conversation_id` to thread messages.
*   **AC3:** Missing mandatory fields must return HTTP 422.

---

### Epic 2: Public Presence (SEO)

#### US-03: Public Restaurant Page
**As a** Potential Diner,
**I want to** view a restaurant's menu and info via a web link,
**So that** I can decide what to order before visiting.

**Acceptance Criteria:**
*   **AC1:** Visiting `/restaurant/{slug}` loads the specific restaurant's page.
*   **AC2:** Page `<title>` must be format: "{Restaurant Name} | {City} - Retorant".
*   **AC3:** Meta description must match the restaurant's short description.
*   **AC4:** OpenGraph image tags must point to the restaurant's cover image.

---

### Epic 3: Subscription Management

#### US-04: Feature Gating
**As a** Product Owner,
**I want to** restrict AI features to paid subscribers,
**So that** we can monetize the high-cost integration.

**Acceptance Criteria:**
*   **AC1:** AI API requests for a restaurant with `subscription_status != active` must return HTTP 403.
*   **AC2:** Dashboard must show a "Upgrade to enable AI" banner for free tier users.

## 3. Data Dictionary (Key Entities)
*   **Restaurant:** The core tenant entity. Identified by `uuid` and `slug`.
    *   *Key Props:* `subscription_status` (Critical for access control).
*   **AI Agent:** Configuration entity linked to Restaurant.
    *   *Config Props:* `ai_provider` (e.g., OpenAI), `ai_model` (gpt-4o), `temperature`, `voice_provider` (ElevenLabs), `voice_id`.
*   **Conversation:** Transactional entity logging chat volume.
    *   *Core Props:* `conversation_uuid` (External ID), `customer_phone_number`, `message_text`, `response_text`, `direction` (inbound/outbound), `channel` (whatsapp/web), `timestamp`.
