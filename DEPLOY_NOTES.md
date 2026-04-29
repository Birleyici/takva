# Deploy Notes (Vite /management CORS)

## Symptom
- Browser console shows CORS errors that try to load assets from
  `http://127.0.0.1:5173/@vite/client` or `http://127.0.0.1:5173/resources/...`

## Root cause
- `public/hot` exists on production.
- Laravel `@vite` detects this file and points to the dev server instead of
  using the built assets in `public/build`.

## Fix (production)
1) Delete `public/hot`
2) Run `npm run build`
3) Deploy the `public/build` output to the server
4) (Optional) `php artisan optimize:clear`

## Reminder / Checklist
- `public/hot` should NOT exist on production.
- `APP_ENV=production` and `APP_DEBUG=false`
- No `VITE_DEV_SERVER_URL` set in production env

## Why this happens
- `public/hot` is created by `npm run dev` and is only for local development.
- If it remains on the server, Laravel thinks it should load the dev server,
  which causes CORS errors on the live domain.
