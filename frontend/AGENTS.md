# Frontend DOX (frontend/)

## Purpose
Vite + React + TypeScript console that renders the shortcode-builder UI
on the "WR Ajax Load More" admin page. Builds to a single IIFE
(`../dist/app.js`) plus `../dist/style.css`, which is what the plugin's
PHP (`inc/class-admin.php`) actually enqueues.

## Ownership
Web.Revizor.

## Local Contracts
- No backend/API calls exist for this console — it only reads
  `window.wralmSettings` (post types + taxonomies, injected by
  `WRALM_Admin::render_page()`) and computes shortcode strings entirely
  client-side. Because of that there is intentionally **no `client/`
  directory** — add one only if this console starts calling `admin-ajax.php`
  or a REST route.
- State for the shortcode form lives in `hooks/useShortcodeBuilder.ts`;
  components stay presentational and receive state + setters as props.
- Must build as `format: 'iife'` exporting a single global
  (`WebRevizorAjaxLoadMore`) — WordPress loads it as a plain `<script>`
  tag, not an ES module. `react` / `react-dom` are externalized and must
  be enqueued as separate script dependencies (see `vite.config.ts`).
- Styling is plain CSS in `src/styles.css` (kept intentionally simple —
  no Tailwind/PostCSS pipeline for this small a UI).

## Work Guidance
- New form fields: extend the relevant type in `types/index.ts`, the
  matching default in `hooks/useShortcodeBuilder.ts`, and the tab
  component in `components/`.
- Shared inputs (text/number/checkbox/select) live in
  `components/FormControls.tsx` — reuse them instead of hand-rolling new
  `<input>` markup per tab.

## Verification
- `npm install && npm run build` from `frontend/` regenerates
  `../dist/app.js` and `../dist/style.css`.
- `npm run dev` runs `vite build --watch` for iterative development.
- To check it in WordPress: activate the plugin, open
  **WR Ajax Load More** in the admin menu, confirm the console mounts
  into `#wralm-console` with no console errors, and that both shortcode
  fields update live and copy correctly.
