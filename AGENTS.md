# Project Rules

- Prefer full Livewire components (class + Blade) over Volt components.
- Alpine.js is bundled with Livewire; do not import Alpine separately.
- Move complicated JavaScript out of Blade files into helper .js files.
- In Livewire, use `$this->redirectRoute(...)` and then `return;` after redirects.

## Commit Message Prompt
Use this prompt with your AI assistant:

Generate a git commit message in Conventional Commits format.
Rules:
- Format: "<type>(optional-scope): <short summary>"
- Allowed types: feat, fix, perf, refactor, docs, test, chore, ci, build
- Summary: imperative mood, <= 72 chars, no trailing period.
- If there is a breaking change, add "BREAKING CHANGE: <detail>" in the body.
- Provide ONLY the commit message, no extra text.

Context:
<PASTE THE CHANGE SUMMARY HERE>
