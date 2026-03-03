# skills

A collection of agent skills for LMS development, theming, and web technologies.

## Installation

Install all skills at once:

```bash
npx skills add nicolasflores9/skills
```

Or list available skills first:

```bash
npx skills add nicolasflores9/skills --list
```

Install a specific skill:

```bash
npx skills add nicolasflores9/skills -s moodle5-theme
```

Install globally (available across all projects):

```bash
npx skills add nicolasflores9/skills -g
```

## Available Skills

### Moodle 5

Skills for Moodle 5.x LMS development and customization.

| Skill | Description |
|---|---|
| [`moodle5-theme`](skills/moodle5/moodle5-theme/) | Create and customize Moodle 5.1+ themes based on Boost (Bootstrap 5.3) |

## Skill Structure

Each skill follows the [Agent Skills specification](https://agentskills.io/specification):

```
skill-name/
├── SKILL.md          # Instructions and metadata (required)
├── references/       # Supporting documentation loaded on demand
├── scripts/          # Executable automation helpers
└── assets/           # Templates, fonts, static files
```

## Compatibility

Skills work with 40+ coding agents including:

- [Claude Code](https://claude.com/claude-code)
- [Cursor](https://cursor.sh)
- [GitHub Copilot](https://github.com/features/copilot)
- [Windsurf](https://codeium.com/windsurf)
- [Cline](https://github.com/cline/cline)

For the full compatibility list, see the [npx skills documentation](https://github.com/vercel-labs/skills).

## Manual Installation

### Claude Code

```bash
# Project scope
cp -r skills/moodle5/moodle5-theme .claude/skills/

# Global scope
cp -r skills/moodle5/moodle5-theme ~/.claude/skills/
```

### Claude Desktop (Cowork)

Upload the `.skill` file from [Releases](https://github.com/nicolasflores9/skills/releases) via Settings → Capabilities → Skills.

## Contributing

Contributions are welcome. To add a new skill:

1. Create a directory under the appropriate category in `skills/`
2. Add a `SKILL.md` with valid YAML frontmatter (`name` + `description`)
3. Keep `SKILL.md` under 500 lines — move detailed docs to `references/`
4. Submit a pull request

## License

[Apache 2.0](LICENSE)
