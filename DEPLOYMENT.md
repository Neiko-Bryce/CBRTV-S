# Deployment (Railway)

## Candidate photos on the deployed site

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
