# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

P8P is a PHP Kubernetes client SDK generator that creates strongly-typed PHP classes from Kubernetes OpenAPI specifications. The project uses PHP 8.4+ and is structured as a monorepo with three main packages managed by PMU (PHP Monorepo Utility).

## Architecture

The codebase is organized into three interconnected packages:

### 1. Client (`src/Client`)
- **Purpose**: Core HTTP client for communicating with Kubernetes APIs
- **Namespace**: `P8p\Client`
- **Key Components**:
  - `Client`: Main HTTP client that handles requests/responses using Symfony HttpClient and Serializer
  - `ClientFactory`: Creates configured client instances from URL credentials, kubeconfig file, or in-cluster configuration
  - `Response`: Typed response wrapper supporting list operations and pagination
  - `Credentials`: Supports multiple authentication methods (token, certificates, in-cluster service accounts, kubeconfig)
  - `Api\ApiInterface`: Base interface that all generated API classes implement

### 2. CodeGenerator (`src/CodeGenerator`)
- **Purpose**: Reads Kubernetes OpenAPI v3 specs and generates PHP model/service classes
- **Namespace**: `P8p\CodeGenerator`
- **Key Components**:
  - `Reader\OpenApiV3Reader`: Fetches and parses OpenAPI specs from a Kubernetes API server
  - `Reader\ClassMetadataExtractor`: Extracts Kubernetes GVK (Group/Version/Kind) metadata from schemas
  - `Reader\TypeExtractor`: Converts OpenAPI types to Symfony TypeInfo types
  - `Writer\SchemaClassBuilder`: Generates PHP model classes with properties and typed accessors
  - `Writer\ServiceClassBuilder`: Generates API service classes with CRUD methods
  - `Writer\ClassDumper`: Writes generated PHP classes to disk using nette/php-generator
- **Configuration**: Uses `config.php` to define which APIs to generate and type overrides for special Kubernetes types (IntOrString, Quantity, etc.)
- **Core API Support**: Handles Kubernetes core API (empty group) which uses `/api/{version}` instead of `/apis/{group}/{version}`
  - Use `Api::core('v1')` for core resources (Pod, Service, ConfigMap, etc.)
  - Use `new Api('group', 'version')` for grouped resources (Deployment, StatefulSet, etc.)
- **Entry Point**: `generate.php` CLI application

### 3. Sdk (`src/Sdk`)
- **Purpose**: Generated Kubernetes API models and services (output of CodeGenerator)
- **Namespace**: `P8p\Sdk`
- **Content**: Auto-generated PHP classes representing Kubernetes resources and their APIs

## Code Generation Workflow

1. Configure APIs to generate in `src/CodeGenerator/config.php`:
   - Core API: `Api::core('v1')` → fetches from `/openapi/v3/api/v1`
   - Grouped APIs: `new Api('apps', 'v1')` → fetches from `/openapi/v3/apis/apps/v1`
2. Run generator against a Kubernetes API server (default: `http://127.0.0.1:8001/`)
3. Generator fetches OpenAPI specs using the appropriate URL pattern
4. Creates Model objects (Schema and Service) from OpenAPI definitions
5. Writes typed PHP classes to `src/Sdk/src/`

The generated classes use the `K8sSchema` attribute to store Kubernetes metadata (GVK) for runtime reflection.

## Development Commands

All commands use Castor task runner. Run `castor list` to see available tasks.

### Installation
```bash
castor install                    # Install all dependencies (root + all packages)
composer install                  # Install root dependencies only
composer all install              # Install all package dependencies via PMU
```

### Testing
```bash
castor tests:all                  # Run all test suites
castor tests:client               # Run Client package tests
castor tests:client --coverage   # Run with code coverage report
```

Individual package tests (run from package directory):
```bash
cd src/Client && vendor/bin/phpunit
```

### Code Quality
```bash
castor quality:all                # Run all quality checks (fixer + phpstan + rector)
castor quality:csfixer            # Run PHP CS Fixer (auto-fix code style)
castor quality:phpstan            # Run PHPStan static analysis
castor quality:rector             # Run Rector (auto-upgrade code)
```

Quality tools run at the root level and analyze all packages:
- **php-cs-fixer**: Configuration in `.php-cs-fixer.dist.php`
- **phpstan**: Configuration in `phpstan.neon.dist`
- **rector**: Configuration in `rector.php`

### Code Generation
```bash
cd src/CodeGenerator
php generate.php [baseUrl] [-c config.php]  # Generate SDK from Kubernetes API
```
Default base URL is `http://127.0.0.1:8001/` (kubectl proxy)

## Monorepo Structure

This is a PMU-managed monorepo. Each package in `src/` has its own `composer.json` and can be developed independently. The root `composer.json` defines dev tools shared across all packages.

Dependencies between packages:
- `CodeGenerator` depends on `Client`
- `Sdk` depends on `Client`

## Key Technical Decisions

- **PHP 8.4+**: Uses modern PHP features including property hooks, typed properties, and readonly
- **Symfony Components**: HttpClient for HTTP, Serializer for JSON, TypeInfo for type system
- **OpenAPI v3**: Reads Kubernetes OpenAPI specs (not Swagger v2)
- **Type Overrides**: Some Kubernetes types need special handling (IntOrString, Quantity, RawExtension, Time) - configured in `config.php`
- **GVK Metadata**: Stores Kubernetes Group/Version/Kind in `K8sSchema` attribute for resource identification
- **Template URIs**: Uses RFC 6570 URI templates for path parameter expansion (league/uri)
