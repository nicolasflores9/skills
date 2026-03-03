# Moodle 5 Skills

Skills for [Moodle 5.x](https://moodle.org/) LMS development and customization.

Moodle 5.0 (April 2025) introduced Bootstrap 5, and Moodle 5.1 (October 2025) brought the `/public/` directory restructuring. These skills cover the latest patterns and best practices for Moodle 5.1.3+.

## Available Skills

| Skill | Description | Install |
|---|---|---|
| [`moodle5-theme`](moodle5-theme/) | Create and customize themes based on Boost (Bootstrap 5.3) | `npx skills add nicolasflores9/skills -s moodle5-theme` |

## Quick Install

```bash
# Install all Moodle 5 skills
npx skills add nicolasflores9/skills

# Install a specific skill
npx skills add nicolasflores9/skills -s moodle5-theme
```

## What's Covered

### moodle5-theme

Everything you need to build and maintain Moodle 5 themes:

- **Theme scaffolding** — Generate complete Boost child themes from scratch
- **SCSS pipeline** — Three-phase compilation (pre-SCSS → preset → extra-SCSS)
- **Mustache templates** — Override core and plugin templates with Moodle helpers
- **Admin settings** — Color pickers, file uploads, SCSS editors, and more
- **Renderers** — Custom output components and core renderer overrides
- **Bootstrap 5.3 migration** — Complete BS4 → BS5 class and attribute mapping
- **Debugging** — Cache management, Theme Designer Mode, CLI tools

## Requirements

- Moodle 5.0+ (5.1.3+ recommended)
- PHP 8.3+
- PostgreSQL 15+ or MySQL 8.4+
