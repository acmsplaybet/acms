# ACMS Dynamic Configuration Testing Protocol

To verify the successful end-to-end synchronization between the Admin Panel and the mobile SPA for the newly integrated dynamic configurations, follow this testing protocol:

### Step 1: Admin Panel Configuration Test
1. Log in to the ACMS Admin Dashboard.
2. Navigate to **Uygulama İşlemleri** > **Tüm Uygulamalar**.
3. Edit an existing application (e.g., Real VIP Tips).
4. Navigate to the **UX & Metinler** tab.
5. Make the following changes:
   - Change **Guide Step 1** to `TEST STEP 1`.
   - Change **Ana Sayfa Duyuru Metni** to `TEST ANNOUNCEMENT TEXT`.
   - Expand the **Onboarding Ekranı Ayarları** and set **Step 1 Title** to `TEST TITLE` and **Desc** to `TEST DESC`.
6. Click **Kaydet** and verify the "Başarılı" popup is displayed.

### Step 2: API Validation Test
1. Access the `init.php` endpoint via Postman or browser: `http://<your-local-url>/acms/api/app/init.php?app_id=<the-app-id-you-edited>`
2. Inspect the JSON response payload.
3. Assert that:
   - `guide_steps[0]` equals `"TEST STEP 1"`.
   - `home_announcement_text` equals `"TEST ANNOUNCEMENT TEXT"`.
   - `onboarding_steps[0].title` equals `"TEST TITLE"`.
   - `onboarding_steps[0].desc` equals `"TEST DESC"`.

### Step 3: Mobile SPA Validation Test
1. Clear your browser cache and open the mobile SPA URL in incognito mode (to reset localStorage `seen_onboarding` keys).
2. Validate **Onboarding Screen**:
   - The first slide should now display `TEST TITLE` and `TEST DESC`.
3. Complete onboarding to land on the **GuestLanding** component.
   - The Guide Card #1 should now display `TEST STEP 1`.
4. Proceed to **Sign Up/Login** and authenticate.
5. Validate **Home Screen**:
   - The "Latest News" module should now display `TEST ANNOUNCEMENT TEXT`.

If all assertions pass, the backend-to-frontend synchronization for dynamic content management is 100% functional.
