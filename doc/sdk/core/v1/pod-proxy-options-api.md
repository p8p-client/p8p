# PodProxyOptionsApi

[← Back to Index](index.md)

- **API Group:** Core
- **API Version:** v1
- **Full Class Name:** `P8p\Sdk\Api\Core\V1\PodProxyOptionsApi`

---

## Table of Contents

- [connectGetPodProxy](#connectGetPodProxy) ⚡
- [connectPutPodProxy](#connectPutPodProxy) ⚡
- [connectPostPodProxy](#connectPostPodProxy) ⚡
- [connectDeletePodProxy](#connectDeletePodProxy) ⚡
- [connectOptionsPodProxy](#connectOptionsPodProxy) ⚡
- [connectHeadPodProxy](#connectHeadPodProxy) ⚡
- [connectPatchPodProxy](#connectPatchPodProxy) ⚡
- [connectGetPodProxyWithPath](#connectGetPodProxyWithPath) ⚡
- [connectPutPodProxyWithPath](#connectPutPodProxyWithPath) ⚡
- [connectPostPodProxyWithPath](#connectPostPodProxyWithPath) ⚡
- [connectDeletePodProxyWithPath](#connectDeletePodProxyWithPath) ⚡
- [connectOptionsPodProxyWithPath](#connectOptionsPodProxyWithPath) ⚡
- [connectHeadPodProxyWithPath](#connectHeadPodProxyWithPath) ⚡
- [connectPatchPodProxyWithPath](#connectPatchPodProxyWithPath) ⚡

---

## Usage

```php
use P8p\Sdk\Api\Core\V1\PodProxyOptionsApi;

$podProxyOptionsApi = $client->getApi(PodProxyOptionsApi::class);
```

## Operations

### connectGetPodProxy

connect GET requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectGetPodProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectPutPodProxy

connect PUT requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectPutPodProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectPostPodProxy

connect POST requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectPostPodProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectDeletePodProxy

connect DELETE requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectDeletePodProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectOptionsPodProxy

connect OPTIONS requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectOptionsPodProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectHeadPodProxy

connect HEAD requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectHeadPodProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectPatchPodProxy

connect PATCH requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectPatchPodProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectGetPodProxyWithPath

connect GET requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectGetPodProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectPutPodProxyWithPath

connect PUT requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectPutPodProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectPostPodProxyWithPath

connect POST requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectPostPodProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectDeletePodProxyWithPath

connect DELETE requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectDeletePodProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectOptionsPodProxyWithPath

connect OPTIONS requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectOptionsPodProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectHeadPodProxyWithPath

connect HEAD requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectHeadPodProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---

### connectPatchPodProxyWithPath

connect PATCH requests to proxy of Pod

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$podProxyOptionsApi->connectPatchPodProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the PodProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to pod. |



---



**Generated by P8P Code Generator**
