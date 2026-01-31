# Software Architecture Document (SAD)

**Role:** Solution Architect
**Method:** Tech Stack Evaluation & Justification
**Project:** Retorant SaaS
**Version:** 2.0

## 1. Architectural Style
We utilize a **Modular Monolith** architecture. This provides the development speed of a monolith while keeping "AI Services" and "Core Domain" logically separated, enabling potential extraction to microservices (e.g., the AI Context API) in the future if scale demands it.

## 2. Technology Stack & Justification

### 2.1 Backend Framework: Laravel 11
**Choice:** Laravel PHP
**Benchmarks/Evidence:**
1.  **Dev Speed:** Laravel's ecosystem (Cashier, Sanctum, Eloquent) reduces boilerplate code by ~40% compared to raw Express.js or Go for SaaS features.
2.  **Performance:** With PHP 8.2+ and Octane/Opcache, Laravel handles ~500-800 req/sec on standard droplets, sufficient for our Phase 2 target (10k MRR).
3.  **Hiring:** PHP/Laravel talent is abundant and cost-effective in the target region (MENA).
**Why:** Rapid MVP delivery is critical. Laravel provides "Batteries Included" for Billing, Auth, and Queues, allowing us to focus on the AI differentiation.

### 2.2 Database layer: MySQL (Prod) / SQLite (Dev)
**Choice:** MySQL 8.0
**Benchmarks/Evidence:**
1.  **Read Heavy:** Our workload is 90% read (Menu fetch, AI context) / 10% write (Orders). MySQL optimized with proper indexing handles millions of reads efficiently.
2.  **JSON Support:** MySQL's native JSON columns allow us to store flexible `translations` and `ai_settings` without strictly normalizing every minor attribute, mirroring NoSQL flexibility with SQL integrity.
3.  **ACID Compliance:** Essential for financial transactions (subscriptions, orders).
**Why:** Hybrid relational/document model via JSON columns is the perfect fit for multi-language catalogues.

### 2.3 Caching Layer: Redis
**Choice:** Redis
**Benchmarks/Evidence:**
1.  **Latency:** Redis sub-millisecond retrieval vs MySQL 10-50ms query time. Critical for the AI API which must respond instantly to n8n to prevent chat lag.
2.  **Throughput:** Single Redis instance can handle >100k ops/sec.
**Why:** The `getContext` API payload is complex to compute (joins across 5 tables). Computing this on every chat message is wasteful. Redis caching (with Model Event invalidation) is the architecture's keystone for performance.

### 2.4 AI Integration: n8n (Self-Hosted)
**Choice:** n8n
**Benchmarks/Evidence:**
1.  **Cost:** Self-hosted n8n ($20-50/mo server) vs Zapier/Make ($500+/mo for high volume).
2.  **Control:** Visual workflow builder allows non-dev implementation of complex "Human-in-the-loop" logic.
**Why:** Cost leadership strategy. We cannot resell high-margin AI if we pay high margins to Zapier.

## 3. High-Level Diagram

```mermaid
graph TD
    Client[WhatsApp User] -->|Msg| MetaAPI
    MetaAPI -->|Webhook| N8N[n8n Workflow Engine]
    
    subgraph "Retorant SaaS Cloud"
        N8N -->|Get Context (Cached)| API[Laravel API]
        API -->|Check Hit| Redis[(Redis Cache)]
        API -->|Auth Check| Middleware[EnsureApiKey]
        API -->|Read| DB[(MySQL DB)]
    end
    
    subgraph "Public Web"
        WebUser -->|HTTPS| Web[Public Restaurant Page]
        Web -->|SSR| DB
    end
```
