# Instructions for AI assistants working on Tetra3D

Tetra3D (app.tetra3d.com) is a Laravel 12 app. Files in this repo use Windows (CRLF) line endings — keep them.

## Before you start
- Read `CHANGELOG.md` first. It explains past fixes, decisions and known follow-ups.

## After every change — update CHANGELOG.md (required)
Every time you change code, config, views or the database, add a new entry at the **top**
of `CHANGELOG.md` (just under the first `---`). Do this in the same task, before you finish.
Write it in detail, so a person or another AI can understand it later without the chat history.

Use this format:

```
## YYYY-MM-DD — Short title

**Type:** Bug fix | Feature | Refactor | Upgrade | Data fix
**Made by:** <who / which AI>, requested by <who>
**Status:** Local only | Tested on staging | Deployed

### Problem
What was wrong or what was asked, with a concrete example (project, page, URL).

### Root cause
Why it happened. Name the files and functions.

### Decision
What approach was chosen and why (and what was rejected), if there was a choice.

### Changes
Numbered list, one item per file: file path → function/section → what changed.

### Not changed / follow-ups
Anything left undone, known risks, data that still needs cleanup.

### How to test
Step-by-step checks with the expected result.
```

Rules:
- One entry per task. If you change more in the same task, update that entry rather than adding a new one.
- Never delete or rewrite old entries. If a later change undoes an earlier one, say so in the new entry.
- Use real file paths and function names. Include SQL or commands someone would need to run.
