---
name: openspec-default-workflow
description: Ensure every change in this project follows the OpenSpec workflow. Activate for any request that involves creating, modifying, or refactoring code, features, or fixes. This skill overrides default behavior and routes all work through OpenSpec change management.
license: MIT
compatibility: Requires openspec CLI with changes directory at openspec/changes/
metadata:
  author: openspec
  version: "1.0"
---

This project uses OpenSpec for change management. Every code change MUST go through the OpenSpec workflow.

**Activation Triggers** (activate automatically when user mentions):
- Creating/adding new features or functionality
- Fixing bugs or issues
- Refactoring code
- Modifying existing features
- Any request that involves writing or changing code

---

**Default Workflow for Every Change**

1. **If user describes what they want to build or fix**:
   - Use `openspec-propose` skill to create the change proposal and artifacts
   - Run `openspec new change "<name>"` with a derived kebab-case name
   - Generate all required artifacts (proposal.md, design.md, tasks.md)

2. **If user wants to implement an existing change**:
   - Use `openspec-apply-change` skill to work through tasks

3. **If user wants to explore or clarify ideas**:
   - Use `openspec-explore` skill for thinking partnership

4. **If implementation is complete and user wants to finalize**:
   - Use `openspec-archive-change` skill to archive the change

**Rules**

- NEVER skip the OpenSpec workflow for any code change
- Always use `openspec` CLI commands to manage changes
- All artifacts must be created before implementation begins
- Implementation must follow tasks.md exactly
- Archive completed changes before starting new ones

**Change Name Convention**
- Use kebab-case for change names
- Example: "add user authentication" → `add-user-auth`