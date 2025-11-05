# Architecture

P8P uses a monorepo structure managed by PMU (PHP Monorepo Utility) to organize its packages.

## Monorepo Structure

```
p8p/
├── src/
│   ├── Client/                   # Package p8p/client
│   │   ├── src/
│   │   ├── tests/
│   │   ├── composer.json
│   │   └── phpunit.xml.dist
│   ├── CodeGenerator/            # Package p8p/generator
│   │   ├── src/
│   │   ├── tests/
│   │   └── composer.json
│   ├── Sdk/                      # Package p8p/sdk
│   │   ├── src/
│   │   └── composer.json
│   └── ...
├── vendor/                       # Root dependencies
└── ...
```

## What is PMU?

**PMU (PHP Monorepo Utility)** is a tool that facilitates dependency management in a PHP monorepo.

- **Unified installation**: `castor install` installs dependencies for all packages
- **Symlink management**: Creates symbolic links between packages
- **Isolation**: Each package has its own `composer.json` and `vendor/`


## Sub-repository Synchronization

Each sub-repository is automatically synchronized to a **read-only** repository in the GitHub organization.
This synchronization is fully automated via a **GitHub Action**, which uses **splitsh-lite** to split and propagate changes reliably.

Thus, every update made to the main repository is automatically replicated to the corresponding sub-repositories.

