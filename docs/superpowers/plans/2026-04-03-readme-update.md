# README Update Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rewrite README.md to document all commands and communicate the value of modular Laravel architecture to newcomers.

**Architecture:** Single file edit — README.md already drafted during brainstorming. This plan covers review, verification, and commit.

**Tech Stack:** Markdown, GitHub-flavoured README conventions.

---

### Task 1: Verify README content against source commands

**Files:**
- Modify: `README.md`

- [ ] **Step 1: Cross-check command table against source**

Run:
```bash
grep -h "protected \$signature" src/Console/Commands/*.php | grep -o "module:[a-z:]*"
```

Expected output (19 commands):
```
module:list
module:make:component
module:make:console
module:make:controller
module:make:event
module:make:factory
module:make:job
module:make:listener
module:make:mail
module:make:middleware
module:make:migration
module:make:model
module:make:notification
module:make:policy
module:make:provider
module:make:request
module:make:resource
module:make:seeder
module:make:test
module:make:view
```

Verify every command appears in the `## Command Reference` table in `README.md`. If any are missing, add them.

- [ ] **Step 2: Verify config options against source**

Run:
```bash
cat config/modularize.php
```

Confirm the four keys (`enable`, `root_path`, `autoload_routes`, `autoload_service_provider`) match the Config options table in `README.md`. Update if any mismatch.

- [ ] **Step 3: Verify Laravel/PHP version constraints**

Run:
```bash
cat composer.json | grep -A5 '"require"'
```

Confirm `## Requirements` section lists correct versions (PHP 8.2+, Laravel 10/11/12). Update if needed.

- [ ] **Step 4: Commit**

```bash
git add README.md
git commit -S -m "docs: rewrite README with full command reference and modular intro"
```

---

### Task 2: Commit spec and plan docs

**Files:**
- Create: `docs/superpowers/specs/2026-04-03-readme-update-design.md`
- Create: `docs/superpowers/plans/2026-04-03-readme-update.md`

- [ ] **Step 1: Stage and commit docs**

```bash
git add docs/
git commit -S -m "docs: add brainstorming spec and implementation plan for README update"
```
