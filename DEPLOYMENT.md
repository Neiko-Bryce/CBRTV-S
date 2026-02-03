# Deployment (Railway)

## Candidate photos on the deployed site

Candidate photos are stored in `storage/app/public/candidates/`. On Railway, the filesystem is **ephemeral** by default, so uploads are lost on redeploy unless you use a **persistent volume**.

### Option 1: Persistent volume (recommended)

1. In **Railway** → your project → your **service** → **Volumes**.
2. Click **Add Volume**.
3. Set **Mount Path** to: `/app/storage`
4. Redeploy. Uploaded candidate photos will now persist across deploys.

If the app cannot write to the volume (permission errors), add this **variable** to the service:
- `RAILWAY_RUN_UID` = `0`

### Option 2: No volume

If you don’t add a volume:
- Student-facing pages (Vote, My Voting History) will still work.
- When a photo file is missing (e.g. after a redeploy), a **placeholder icon** is shown instead of a broken image.
- Re-upload candidate photos in the admin after each deploy if you need real photos.

## After deploy

- **Login**: Students are always redirected to the Student Dashboard after login (never to a raw photo URL).
- **Storage link**: `start.sh` runs `php artisan storage:link` so the public storage symlink exists when using a volume.
