# Test Cases & Validations

**Role:** Quality Assurance Engineer
**Method:** BDD (Behavior Driven Development)
**Suite:** Release 1.2 "AI Features"

## Suite 1: Functional Testing (API)

### TC-01: AI Context Authorization
*   **Description:** Verify that only requests with valid `X-API-KEY` header can access context.
*   **Steps:**
    1.  Send GET request to `/api/v1/external/restaurant/{slug}/context` without header.
    2.  Send request with invalid key `1234`.
    3.  Send request with valid key from `.env`.
*   **Expected:** 
    1.  `401 Unauthorized`
    2.  `401 Unauthorized`
    3.  `200 OK` + JSON Body.

### TC-02: Content Freshness (Cache Invalidation)
*   **Description:** Verify that updating a Price in Admin Panel reflects in AI API immediately.
*   **Steps:**
    1.  Call API for `burger-place`. Note price of "Smashed Burger" ($10).
    2.  As Admin, update "Smashed Burger" price to $12.
    3.  Call API again immediately.
*   **Expected:** JSON returns price $12 (Cache was cleared by Observer).

### TC-03: Localization Fallback
*   **Description:** Verify system falls back to English if Arabic translation is missing.
*   **Steps:**
    1.  Request `?lang=ar` for a product that only has English data.
    2.  Check `name` field in JSON.
*   **Expected:** English Name returned (Graceful degradation), HTTP 200.

## Suite 2: SEO & Frontend

### TC-04: Sitemap Integrity
*   **Description:** Verify new dynamic public pages are indexable.
*   **Steps:**
    1.  Create new restaurant.
    2.  Check `/sitemap.xml`.
*   **Expected:** New URL included.

### TC-05: Meta Tag Verification
*   **Steps:** inspect Source on `/restaurant/my-shop`.
*   **Expected:** `<meta name="description">` matches DB content.
