# NodeProxyOptionsApi

[← Back to Index](../../index.md)

- **API Group:** Core
- **API Version:** v1
- **Full Class Name:** `P8p\Sdk\Api\Core\V1\NodeProxyOptionsApi`

---

## Table of Contents

- [connectGetNodeProxy](#connectGetNodeProxy) ⚡
- [connectPutNodeProxy](#connectPutNodeProxy) ⚡
- [connectPostNodeProxy](#connectPostNodeProxy) ⚡
- [connectDeleteNodeProxy](#connectDeleteNodeProxy) ⚡
- [connectOptionsNodeProxy](#connectOptionsNodeProxy) ⚡
- [connectHeadNodeProxy](#connectHeadNodeProxy) ⚡
- [connectPatchNodeProxy](#connectPatchNodeProxy) ⚡
- [connectGetNodeProxyWithPath](#connectGetNodeProxyWithPath) ⚡
- [connectPutNodeProxyWithPath](#connectPutNodeProxyWithPath) ⚡
- [connectPostNodeProxyWithPath](#connectPostNodeProxyWithPath) ⚡
- [connectDeleteNodeProxyWithPath](#connectDeleteNodeProxyWithPath) ⚡
- [connectOptionsNodeProxyWithPath](#connectOptionsNodeProxyWithPath) ⚡
- [connectHeadNodeProxyWithPath](#connectHeadNodeProxyWithPath) ⚡
- [connectPatchNodeProxyWithPath](#connectPatchNodeProxyWithPath) ⚡

---

## Usage

```php
use P8p\Sdk\Api\Core\V1\NodeProxyOptionsApi;

$nodeProxyOptionsApi = $client->getApi(NodeProxyOptionsApi::class);
```

## Operations

### connectGetNodeProxy

connect GET requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectGetNodeProxy(
    string $name,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectPutNodeProxy

connect PUT requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectPutNodeProxy(
    string $name,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectPostNodeProxy

connect POST requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectPostNodeProxy(
    string $name,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectDeleteNodeProxy

connect DELETE requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectDeleteNodeProxy(
    string $name,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectOptionsNodeProxy

connect OPTIONS requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectOptionsNodeProxy(
    string $name,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectHeadNodeProxy

connect HEAD requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectHeadNodeProxy(
    string $name,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectPatchNodeProxy

connect PATCH requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectPatchNodeProxy(
    string $name,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectGetNodeProxyWithPath

connect GET requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectGetNodeProxyWithPath(
    string $name,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectPutNodeProxyWithPath

connect PUT requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectPutNodeProxyWithPath(
    string $name,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectPostNodeProxyWithPath

connect POST requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectPostNodeProxyWithPath(
    string $name,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectDeleteNodeProxyWithPath

connect DELETE requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectDeleteNodeProxyWithPath(
    string $name,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectOptionsNodeProxyWithPath

connect OPTIONS requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectOptionsNodeProxyWithPath(
    string $name,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectHeadNodeProxyWithPath

connect HEAD requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectHeadNodeProxyWithPath(
    string $name,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---

### connectPatchNodeProxyWithPath

connect PATCH requests to proxy of Node

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$nodeProxyOptionsApi->connectPatchNodeProxyWithPath(
    string $name,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the NodeProxyOptions |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the URL path to use for the current proxy request to node. |



---



**Generated by P8P Code Generator**
