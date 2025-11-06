# Contributing Guide

Thank you for your interest in contributing to P8P! This guide will help you get started.

## Code of Conduct

By participating in this project, you agree to respect our code of conduct. Be respectful, professional and constructive in all your interactions.

## How to Contribute

### Types of Contributions

We accept several types of contributions:

- **Bug fixes**: Fix existing issues
- **New features**: Add new capabilities
- **Documentation**: Improve or translate documentation
- **Tests**: Add or improve test coverage
- **Code quality**: Refactoring, optimizations

### Before You Start

1. **Check existing issues**: Make sure an issue doesn't already exist
2. **Create an issue**: For major features, create an issue first for discussion
3. **Small contributions**: Small fixes can be submitted directly via PR

## Project Setup

### Prerequisites

- PHP 8.4 or higher
- Composer
- Git
- kubectl (to test the generator)
- castor (https://github.com/jolicode/castor)

### Installation

1. **Fork the repository** on GitHub

2. **Clone your fork**:
```bash
git clone https://github.com/your-username/p8p.git
cd p8p
```

3. **Install dependencies**:
```bash
castor install
```

4. **Verify installation**:
```bash
castor tests:all
castor quality:all
```

## Development Workflow

### 1. Create a Branch

```bash
git checkout -b feature/my-feature
# or
git checkout -b fix/my-fix
```

### 2. Develop

```bash
cd src/Client  # or CodeGenerator, Sdk
# Modify files
```

### 3. Test

Every feature must be tested!

```bash
castor tests:all
```

### 4. Check Quality

```bash
# All tools
castor quality:all

# Individually
castor quality:csfixer   # Fix code style
castor quality:phpstan   # Static analysis
castor quality:rector    # Code upgrade
```


### 5. Create a Pull Request

1. Go to GitHub
2. Create a Pull Request from your branch to `main`
3. Wait for review


---

**Happy coding! 🚀**
