# Deployment (Railway)

## 1. Fix the build (lock file)

If the build fails with **"league/flysystem-aws-s3-v3 is not present in the lock file"**:

- Commit and push **`composer.lock`** (it’s already updated in this project).
- Redeploy so `composer install` uses the new lock file.

## 2. Custom domain (e.g. cpsuvotewisely.com from z.com)

1. **Railway**
   - Open your **web** service → **Settings** (or **Variables**).
   - Add or set **Variables**:
     - `APP_URL` = `https://cpsuvotewisely.com` (use your real domain, with `https://`).
   - Under **Settings**, find **Networking** / **Public Networking** or **Domains**.
   - Click **Add custom domain** (or **Generate domain** first, then **Custom domain**).
   - Enter **`cpsuvotewisely.com`** (and optionally **`www.cpsuvotewisely.com`**).
   - Railway will show the **CNAME target** (e.g. `something.up.railway.app` or a specific hostname). Copy it.

2. **z.com (your domain registrar)**
   - Open DNS settings for **cpsuvotewisely.com**.
   - Add a **CNAME** record:
     - **Name/host:** `@` (for root) or `www` (for www).
     - **Value/target:** the CNAME target from Railway (e.g. `web-production-xxxx.up.railway.app`).
   - For root domain (`cpsuvotewisely.com`), some registrars require an **ALIAS** or **A** record; use what z.com supports (they may show “use CNAME” or “use A record” for root).
   - Save and wait for DNS to propagate (a few minutes to 48 hours).

3. **HTTPS**
   - Railway usually provisions SSL for custom domains automatically once DNS points to them.

4. **Redeploy** after setting `APP_URL` so the app uses the correct URL in links and redirects.

## 3. Candidate photos on the deployed site

Locally, photos work because they’re stored on your machine. When deployed, the app’s filesystem is **ephemeral** by default, so photo files are lost on redeploy and only placeholders show. Use one of the options below so photos persist and display.

### Option 1: Persistent volume

1. In **Railway** → your **web** service → right‑click (or ⋮) → **Attach Volume**.
2. Set **Mount Path** to: **`/app/storage`**
3. Redeploy.
4. In **admin**, re‑upload candidate photos (they’ll be stored on the volume and persist).

If you get permission errors, add variable: `RAILWAY_RUN_UID` = `0`.

### Option 2: Amazon S3 (or compatible) for photos

Photos can be stored in S3 so they persist and display without a volume.

1. Create an S3 bucket (e.g. on AWS or a compatible provider).
2. In **Railway** → **web** service → **Variables**, add:
   - `CANDIDATE_PHOTOS_DISK` = `s3`
   - `AWS_ACCESS_KEY_ID` = your key
   - `AWS_SECRET_ACCESS_KEY` = your secret
   - `AWS_DEFAULT_REGION` = e.g. `us-east-1`
   - `AWS_BUCKET` = your bucket name
3. Redeploy. New uploads and re‑uploads in admin will go to S3 and **photos will show** on Vote and My Voting History.

Leave `CANDIDATE_PHOTOS_DISK` unset (or `public`) locally so photos stay on local disk.

### Option 3: No volume, no S3

- Pages work; when a photo file is missing, a **placeholder icon** is shown.
- Re‑upload candidate photos in admin after each deploy if you need real photos (they’ll disappear again on next redeploy unless you use Option 1 or 2).

## After deploy

- **Login**: Students are always redirected to the Student Dashboard (never to a raw photo URL).
- **Storage link**: `start.sh` runs `php artisan storage:link` and creates storage dirs for the volume.
