# Git Workflow & Commit Conventions

This document outlines the Git workflow, branching strategy, and commit message conventions for the ShortletNG project.

---

## Branching Strategy

We use a simplified **Git Flow** approach suitable for an MVP project.

### Main Branches

#### `main`
- **Purpose**: Production-ready code
- **Protection**: Protected, no direct commits
- **Merges from**: `develop` branch only
- **Deploys to**: Production environment

#### `develop`
- **Purpose**: Integration branch for features
- **Protection**: Protected, no direct commits
- **Merges from**: Feature branches, bugfix branches
- **Default branch**: For new feature branches

### Supporting Branches

#### Feature Branches
- **Naming**: `feature/<feature-name>`
- **Examples**:
  - `feature/user-authentication`
  - `feature/property-search`
  - `feature/payment-integration`
  - `feature/booking-system`
- **Created from**: `develop`
- **Merged into**: `develop`
- **Deleted after**: Merge is complete

#### Bugfix Branches
- **Naming**: `bugfix/<bug-description>`
- **Examples**:
  - `bugfix/booking-date-validation`
  - `bugfix/payment-callback-error`
- **Created from**: `develop`
- **Merged into**: `develop`
- **Deleted after**: Merge is complete

#### Hotfix Branches
- **Naming**: `hotfix/<issue-description>`
- **Examples**:
  - `hotfix/payment-gateway-timeout`
  - `hotfix/critical-security-patch`
- **Created from**: `main` (production)
- **Merged into**: Both `main` and `develop`
- **Use case**: Critical production bugs that need immediate fix

---

## Workflow Steps

### 1. Starting New Work

```bash
# Ensure you're on develop and up to date
git checkout develop
git pull origin develop

# Create a new feature branch
git checkout -b feature/property-search

# Start coding...
```

### 2. Making Commits

```bash
# Stage your changes
git add .

# Commit with a descriptive message (see commit conventions below)
git commit -m "Add property search filtering by location and dates"

# Push to remote
git push origin feature/property-search
```

### 3. Keeping Your Branch Updated

```bash
# Fetch latest changes from develop
git fetch origin develop

# Rebase your feature branch (preferred over merge)
git rebase origin/develop

# If conflicts, resolve them, then continue
git rebase --continue

# Force push (if rebased)
git push origin feature/property-search --force-with-lease
```

### 4. Completing Work

```bash
# Push your final changes
git push origin feature/property-search

# Create a pull request on GitHub (see PR guidelines below)
# After review and approval, merge via GitHub

# Delete local branch after merge
git branch -d feature/property-search

# Delete remote branch (if not auto-deleted)
git push origin --delete feature/property-search
```

---

## Commit Message Conventions

We follow a modified **Conventional Commits** specification.

### Format

```
<type>: <subject>

[optional body]

[optional footer]
```

### Types

| Type | Description | Example |
|------|-------------|---------|
| `feat` | New feature | `feat: Add Google OAuth authentication` |
| `fix` | Bug fix | `fix: Resolve booking date overlap validation` |
| `docs` | Documentation changes | `docs: Update README with setup instructions` |
| `style` | Code style/formatting (no logic change) | `style: Format PropertyController with PHP CS Fixer` |
| `refactor` | Code refactoring (no feature/bug change) | `refactor: Extract booking logic into BookingService` |
| `perf` | Performance improvements | `perf: Add database indexes for property search` |
| `test` | Adding or updating tests | `test: Add unit tests for BookingService` |
| `chore` | Maintenance tasks, dependencies | `chore: Update Laravel to 11.x` |
| `build` | Build system, dependencies | `build: Configure Vite for production builds` |
| `ci` | CI/CD configuration | `ci: Add GitHub Actions workflow for tests` |

### Subject Line Rules

1. **Use imperative mood** - "Add feature" not "Added feature" or "Adds feature"
2. **Don't capitalize first letter** - "add feature" not "Add feature" (unless it's a proper noun)
3. **No period at the end** - "Add feature" not "Add feature."
4. **Limit to 72 characters** - Keep it concise
5. **Be descriptive** - Clearly describe what changed

### Examples of Good Commits

```bash
# Feature
git commit -m "feat: Add property search filtering by location and dates"

# Bug fix
git commit -m "fix: Prevent double booking on concurrent requests"

# Refactor
git commit -m "refactor: Extract payment logic into PaymentManager"

# Documentation
git commit -m "docs: Add API documentation for booking endpoints"

# Performance
git commit -m "perf: Optimize property listing query with eager loading"

# Multiple changes (use body)
git commit -m "feat: Implement booking system

- Add booking form with date picker
- Implement availability checking
- Create booking confirmation page
- Add booking email notifications"
```

### Examples of Bad Commits

```bash
# ✗ Too vague
git commit -m "Update files"
git commit -m "Fix bug"
git commit -m "Changes"

# ✗ Not imperative mood
git commit -m "Added property search"
git commit -m "Fixing bugs"

# ✗ Too long subject
git commit -m "Add property search functionality with filters for location, dates, price range, amenities, and property type"
```

---

## Commit Best Practices

### 1. Commit Often, Commit Logical Units

```bash
# ✓ Good: Separate logical changes
git commit -m "feat: Add property search form UI"
git commit -m "feat: Implement property search backend logic"
git commit -m "feat: Add property search result pagination"

# ✗ Bad: Too many unrelated changes in one commit
git commit -m "Add property search, fix bugs, update docs, refactor code"
```

### 2. Don't Commit Half-Done Work

- Only commit when a logical unit is complete
- If you need to save work in progress, use `git stash`

### 3. Test Before Committing

- Ensure your code works before committing
- Run tests if available
- Check for linting errors
- check for static analysis

### 4. Write Meaningful Messages

```bash
# ✓ Good: Explains what and why
git commit -m "fix: Add validation for past check-in dates

Users were able to book properties with check-in dates in the past,
causing issues with availability calculations. This adds validation
to ensure check-in is always in the future."

# ✗ Bad: No context
git commit -m "fix: Date validation"
```

---

## Pull Request (PR) Guidelines

### PR Title

Follow the same convention as commit messages:

```
feat: Implement property search and filtering
fix: Resolve booking date overlap issue
refactor: Extract booking logic into service layer
```

### PR Description Template

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] New feature
- [ ] Bug fix
- [ ] Refactoring
- [ ] Documentation update
- [ ] Performance improvement

## Related Issue
Closes #123 (if applicable)

## Changes Made
- Change 1
- Change 2
- Change 3

## Testing
Describe how you tested these changes:
- [ ] Manual testing completed
- [ ] Unit tests added/updated
- [ ] Feature tests added/updated

## Screenshots (if applicable)
Add screenshots for UI changes

## Checklist
- [ ] Code follows the project's coding standards
- [ ] Self-review completed
- [ ] Code is commented where necessary
- [ ] Documentation updated (if needed)
- [ ] No new warnings or errors
- [ ] Tests pass locally
```

### PR Review Process

1. **Create PR**: Push your branch and create PR to `develop`
2. **Self-review**: Review your own code first, check for obvious issues
3. **Request review**: Assign reviewers (if working in a team)
4. **Address feedback**: Make requested changes
5. **Approval**: At least one approval required
6. **Merge**: Squash and merge or regular merge (depending on commit history)
7. **Delete branch**: Remove feature branch after merge

---

## Merging Strategies

### Feature Branches → Develop

**Option 1: Squash and Merge** (Recommended for feature branches)
- Combines all commits into one
- Keeps develop history clean
- Use when feature has many small commits

**Option 2: Regular Merge**
- Preserves all commits
- Use when commits are well-organized and meaningful

### Develop → Main

**Regular Merge** (Recommended)
- Preserves feature commits
- Creates a merge commit
- Clear history of what was released

---

## Branch Cleanup

### Delete Merged Branches

```bash
# Delete local branch
git branch -d feature/property-search

# Delete remote branch
git push origin --delete feature/property-search

# Prune deleted remote branches locally
git fetch --prune
```

### List Merged Branches

```bash
# List branches merged into develop
git branch --merged develop

# List branches not yet merged
git branch --no-merged develop
```

---

## Git Commands Cheat Sheet

### Daily Workflow

```bash
# Start new feature
git checkout develop
git pull origin develop
git checkout -b feature/my-feature

# Make changes and commit
git add .
git commit -m "feat: Add my feature"

# Push to remote
git push origin feature/my-feature

# Update from develop
git fetch origin develop
git rebase origin/develop

# Force push after rebase
git push origin feature/my-feature --force-with-lease
```

### Useful Commands

```bash
# View commit history
git log --oneline --graph --all

# View changes
git status
git diff

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Undo last commit (discard changes)
git reset --hard HEAD~1

# Stash changes
git stash
git stash pop

# Amend last commit
git commit --amend

# Cherry-pick a commit
git cherry-pick <commit-hash>
```

---

## Release Workflow

For MVP, keep it simple:

### Version Naming

Use semantic versioning: `v1.0.0`, `v1.1.0`, `v1.1.1`

- **Major** (1.x.x): Breaking changes
- **Minor** (x.1.x): New features (backward compatible)
- **Patch** (x.x.1): Bug fixes

### Creating a Release

```bash
# Ensure develop is ready
git checkout develop
git pull origin develop

# Merge develop into main
git checkout main
git pull origin main
git merge develop

# Create a tag
git tag -a v1.0.0 -m "Release version 1.0.0 - Initial MVP"

# Push to remote
git push origin main
git push origin v1.0.0

# Deploy to production...
```

---

## Common Scenarios

### Scenario 1: Made Changes on Wrong Branch

```bash
# Stash your changes
git stash

# Switch to correct branch
git checkout -b correct-branch

# Apply stashed changes
git stash pop
```

### Scenario 2: Need to Fix Typo in Last Commit

```bash
# Make the fix
git add .

# Amend the last commit
git commit --amend --no-edit

# Force push (if already pushed)
git push origin feature/my-feature --force-with-lease
```

### Scenario 3: Accidentally Committed to Develop

```bash
# Create a new branch from current position
git checkout -b feature/my-feature

# Reset develop to origin
git checkout develop
git reset --hard origin/develop

# Continue working on feature branch
git checkout feature/my-feature
```

---

## Collaboration Tips

1. **Pull before you push** - Always pull latest changes before pushing
2. **Small, focused branches** - Keep feature branches small and focused
3. **Frequent commits** - Commit often with meaningful messages
4. **Communicate** - Use PR descriptions and comments to communicate
5. **Review code** - Review your own code before requesting review
6. **Be respectful** - Be constructive and respectful in code reviews

---

## Git Ignore

Ensure your `.gitignore` includes:

```gitignore
# Laravel
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache

# Vue/Vite
/public/build
/public/mix-manifest.json
npm-debug.log
yarn-error.log

# IDE
/.idea
/.vscode
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Testing
coverage/
.phpunit.result.cache
```

---

## Resources

- [Conventional Commits](https://www.conventionalcommits.org/)
- [Git Flow](https://nvie.com/posts/a-successful-git-branching-model/)
- [GitHub Flow](https://guides.github.com/introduction/flow/)
- [How to Write a Git Commit Message](https://chris.beams.io/posts/git-commit/)

---

**Remember**: Good Git hygiene makes collaboration easier and project history clearer. Take time to write meaningful commits!
