# Startup India Progress Dashboard

A premium Laravel 11 + Blade + Tailwind CSS dashboard scaffold for a government-grade startup ecosystem analytics platform.

## Included structure

- `resources/css/app.css` - custom design system tokens and reusable UI utilities
- `resources/js/app.js` - theme toggle, sidebar drawer, password toggle, and Chart.js bootstrapping
- `routes/web.php` - view routes for dashboard, analytics, reports, auth, and admin pages
- `resources/views/layouts/app.blade.php` - authenticated dashboard shell
- `resources/views/layouts/auth.blade.php` - centered auth layout
- `resources/views/partials/*` - sidebar, navbar, breadcrumbs, footer
- `resources/views/components/ui/*` - reusable Blade UI primitives
- `resources/views/**` - dashboard, startups, analytics, reports, users, activity, settings, and auth pages

## Notes

- The structure is intentionally frontend-first and can be wired to controllers and view models later.
- The design system uses indigo, blue, slate, and emerald accents with glassmorphism, rounded cards, and enterprise spacing.
- Chart containers are built for Chart.js and can be fed with real data from Laravel controllers.
