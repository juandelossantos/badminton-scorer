# AGENTS.md

## Skill-Driven Execution Model (MANDATORY)

This project uses a **skill-driven workflow**. You MUST follow these rules for every request.

### Rule 1: Always Check Skills First

For EVERY user request:
1. Determine if any global skill applies (even 1% chance)
2. If a skill applies, you MUST invoke it using the `skill` tool
3. NEVER implement directly if a skill applies
4. ALWAYS follow the skill instructions exactly

### Rule 2: Skill Discovery

Skills are loaded from:
- Project: `.opencode/skills/<name>/SKILL.md`
- Global: `~/.config/opencode/skills/<name>/SKILL.md`
- Claude-compatible: `.claude/skills/<name>/SKILL.md` or `~/.claude/skills/<name>/SKILL.md`

### Rule 3: Intent Mapping

Map user intent to skills automatically:

| User says... | Skill to invoke |
|---|---|
| "build", "create", "haz", "diseña", "crea", "desarrolla" + any web/UI/app | `visual-frontend-mastery` |
| "plan", "break down", "organize" | `planning-and-task-breakdown` |
| "bug", "fix", "error", "broken", "not working" | `debugging-and-error-recovery` |
| "test", "testing", "TDD" | `test-driven-development` |
| "review", "check quality", "refactor" | `code-review-and-quality` |
| "deploy", "ship", "launch", "CI/CD" | `shipping-and-launch` |
| "spec", "specification", "design doc", "requirements" | `spec-driven-development` |
| "security", "auth", "vulnerability", "harden" | `security-and-hardening` |

### Rule 4: Lifecycle Enforcement

For any non-trivial work, follow this lifecycle:

```
DEFINE → spec-driven-development
PLAN   → planning-and-task-breakdown
BUILD  → incremental-implementation + test-driven-development
VERIFY → debugging-and-error-recovery
REVIEW → code-review-and-quality
SHIP   → shipping-and-launch
```

### Rule 5: Anti-Rationalization

These thoughts are WRONG. Ignore them:

- "This is too small for a skill" → NO. Skills exist precisely for structured thinking.
- "I can just quickly implement this" → NO. Check skills first.
- "I'll gather context first" → NO. Invoke the skill. It will tell you what context to gather.
- "The user didn't ask for a spec" → NO. The skill decides what the project needs.
- "I understand what they want" → NO. You have 1% confidence. The skill forces 95% confidence.

### Rule 6: Language Compliance

Detect the user's language from their prompt:
- Spanish prompt ("haz", "diseña", "crea") → Respond in Spanish
- English prompt ("build", "design", "create") → Respond in English
- Never mix languages.

### Rule 7: No Code Before Contract

If `visual-frontend-mastery` applies:
- NO file is created until Discovery Gate completes
- NO code is written until SPEC.md exists (for new features)
- NO design tokens are chosen until user confirms

### Rule 8: Verification

Before marking any task complete:
- [ ] The applicable skill was invoked
- [ ] The skill's workflow was followed completely
- [ ] Required artifacts (specs, plans, tests) exist
- [ ] User confirmed at each gate
