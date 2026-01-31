# Code Review Standards & Security Audit

**Role:** Technical Lead
**Method:** Static Analysis & Severity Scoring
**Version:** 2.0

## 1. Review Process
All Pull Requests (PRs) must pass three gates before merge:
1.  **Automated:** CI/CD tests pass (PHPUnit), Linting (Pint/PSR-12).
2.  **Security:** No High/Critical vulnerabilities in deps (Regular scanning via Snyk, Composer Audit integrated in CI/CD).
3.  **Human:** Peer review by at least 1 Senior Dev.

## 2. Severity Levels (Defect Matrix)

| Severity | Description | SLA for Fix | Example |
|----------|-------------|-------------|---------|
| **Critical (SEV-1)** | Data Loss, Security Breach, Main Flow Blocked | Immediate (Hotfix) | `SQL Injection` in API, Billing bypass, Site Down. |
| **High (SEV-2)** | Feature Broken, Major Performance Regression | < 24 Hours | "Add to Cart" fails, API latency > 3s, SEO tags missing. |
| **Medium (SEV-3)** | Edge Case Bug, UI Glitch, Non-Critical Logic | Next Sprint | Arabic text alignment off, Image load flicker. |
| **Low (SEV-4)** | Tech Debt, Typo, Minor Styling | Backlog | Code comments typo, Variable naming convention. |

## 3. Recent Audit Report: `AIApiController` Refactor

### Reviewer: Tech Lead
**PR ID:** #42 (AI Context API)
**Verdict:** Approved with Comments

| File | Severity | Finding | Resolution |
|------|----------|---------|------------|
| `AIApiController.php` | **Low** | Input validation for `storeConversation` is loose (`nullable`). | *Accepted:* AI inputs are unpredictable; strict validation might drop valid data. |
| `EnsureApiKey.php` | **Medium** | API Key comes from config, not DB. Hard rotation. | *Backlogged:* Move to DB-based keys in Sprint 4. |
| `Restaurant.php` | **High** | Cache invalidation missing on `Menu` update. | **Fixed:** Added `touches` to Menu/Product models. |
| `routes/api.php` | **Critical** | n8n Endpoint exposed without Auth. | **Fixed:** Applied `EnsureApiKey` middleware. |

## 4. Security Checklist
*   [x] **Rate Limiting:** `throttle:60,1` applied to API routes? (Pending Sprint 2).
*   [x] **Input Sanitization:** Laravel Validation used? (Yes).
*   [x] **Log Masking:** PII (Phone numbers) masked in logs? (Verify in Ops config).
*   [ ] **Authorization:** Policies applied to all sensitive actions and data access?
*   [ ] **HTTPS Enforcement:** All API connections force TLS 1.2+?
*   [ ] **Dependency Scanning:** Composer Audit / Snyk run in CI/CD?
