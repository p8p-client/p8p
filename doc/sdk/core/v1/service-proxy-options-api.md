# ServiceProxyOptionsApi

[← Back to Index](index.md)

- **API Group:** Core
- **API Version:** v1
- **Full Class Name:** `P8p\Sdk\Api\Core\V1\ServiceProxyOptionsApi`

---

## Table of Contents

- [connectGetServiceProxy](#connectGetServiceProxy) ⚡
- [connectPutServiceProxy](#connectPutServiceProxy) ⚡
- [connectPostServiceProxy](#connectPostServiceProxy) ⚡
- [connectDeleteServiceProxy](#connectDeleteServiceProxy) ⚡
- [connectOptionsServiceProxy](#connectOptionsServiceProxy) ⚡
- [connectHeadServiceProxy](#connectHeadServiceProxy) ⚡
- [connectPatchServiceProxy](#connectPatchServiceProxy) ⚡
- [connectGetServiceProxyWithPath](#connectGetServiceProxyWithPath) ⚡
- [connectPutServiceProxyWithPath](#connectPutServiceProxyWithPath) ⚡
- [connectPostServiceProxyWithPath](#connectPostServiceProxyWithPath) ⚡
- [connectDeleteServiceProxyWithPath](#connectDeleteServiceProxyWithPath) ⚡
- [connectOptionsServiceProxyWithPath](#connectOptionsServiceProxyWithPath) ⚡
- [connectHeadServiceProxyWithPath](#connectHeadServiceProxyWithPath) ⚡
- [connectPatchServiceProxyWithPath](#connectPatchServiceProxyWithPath) ⚡

---

## Usage

```php
use P8p\Sdk\Api\Core\V1\ServiceProxyOptionsApi;

$serviceProxyOptionsApi = $client->getApi(ServiceProxyOptionsApi::class);
```

## Operations

### connectGetServiceProxy

connect GET requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectGetServiceProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectPutServiceProxy

connect PUT requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectPutServiceProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectPostServiceProxy

connect POST requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectPostServiceProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectDeleteServiceProxy

connect DELETE requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectDeleteServiceProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectOptionsServiceProxy

connect OPTIONS requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectOptionsServiceProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectHeadServiceProxy

connect HEAD requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectHeadServiceProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectPatchServiceProxy

connect PATCH requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectPatchServiceProxy(
    string $name,
    string $namespace,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectGetServiceProxyWithPath

connect GET requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectGetServiceProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectPutServiceProxyWithPath

connect PUT requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectPutServiceProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectPostServiceProxyWithPath

connect POST requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectPostServiceProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectDeleteServiceProxyWithPath

connect DELETE requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectDeleteServiceProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectOptionsServiceProxyWithPath

connect OPTIONS requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectOptionsServiceProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectHeadServiceProxyWithPath

connect HEAD requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectHeadServiceProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---

### connectPatchServiceProxyWithPath

connect PATCH requests to proxy of Service

> [!NOTE]
> This service establishes a WebSocket connection instead of a standard HTTP response.

**Method Signature:**
```php
$serviceProxyOptionsApi->connectPatchServiceProxyWithPath(
    string $name,
    string $namespace,
    string $path,
    ?array $query = null,
): WebSocketConnection
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ServiceProxyOptions |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `path` | `string` | path to the resource |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `path` | `null\|string` | Path is the part of URLs that include service endpoints, suffixes, and parameters to use for the current proxy request to service. For example, the whole request URL is http://localhost/api/v1/namespaces/kube-system/services/elasticsearch-logging/_search?q=user:kimchy. Path is _search?q=user:kimchy. |



---



**Generated by P8P Code Generator**
