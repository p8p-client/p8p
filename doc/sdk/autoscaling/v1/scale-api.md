# ScaleApi

[← Back to Index](../../index.md)

- **API Group:** autoscaling
- **API Version:** v1
- **Full Class Name:** `P8p\Sdk\Api\Autoscaling\V1\ScaleApi`

---

## Table of Contents

- [readCoreV1ReplicationController](#readCoreV1ReplicationController)
- [replaceCoreV1ReplicationController](#replaceCoreV1ReplicationController)
- [patchCoreV1ReplicationController](#patchCoreV1ReplicationController)
- [readAppsV1Deployment](#readAppsV1Deployment)
- [replaceAppsV1Deployment](#replaceAppsV1Deployment)
- [patchAppsV1Deployment](#patchAppsV1Deployment)
- [readAppsV1ReplicaSet](#readAppsV1ReplicaSet)
- [replaceAppsV1ReplicaSet](#replaceAppsV1ReplicaSet)
- [patchAppsV1ReplicaSet](#patchAppsV1ReplicaSet)
- [readAppsV1StatefulSet](#readAppsV1StatefulSet)
- [replaceAppsV1StatefulSet](#replaceAppsV1StatefulSet)
- [patchAppsV1StatefulSet](#patchAppsV1StatefulSet)

---

## Usage

```php
use P8p\Sdk\Api\Autoscaling\V1\ScaleApi;

$scaleApi = $client->getApi(ScaleApi::class);
```

## Operations

### readCoreV1ReplicationController

read scale of the specified ReplicationController


**Method Signature:**
```php
$scaleApi->readCoreV1ReplicationController(
    string $name,
    string $namespace,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |



---

### replaceCoreV1ReplicationController

replace scale of the specified ReplicationController


**Method Signature:**
```php
$scaleApi->replaceCoreV1ReplicationController(
    string $name,
    string $namespace,
    P8p\Sdk\Schema\Autoscaling\V1\Scale $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Autoscaling\V1\Scale` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |



---

### patchCoreV1ReplicationController

partially update scale of the specified ReplicationController


**Method Signature:**
```php
$scaleApi->patchCoreV1ReplicationController(
    string $name,
    string $namespace,
    array $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `array` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. This field is required for apply requests (application/apply-patch) but optional for non-apply patch types (JsonPatch, MergePatch, StrategicMergePatch). |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |
| `force` | `bool\|null` | Force is going to "force" Apply requests. It means user will re-acquire conflicting fields owned by other people. Force flag must be unset for non-apply patch requests. |



---

### readAppsV1Deployment

read scale of the specified Deployment


**Method Signature:**
```php
$scaleApi->readAppsV1Deployment(
    string $name,
    string $namespace,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |



---

### replaceAppsV1Deployment

replace scale of the specified Deployment


**Method Signature:**
```php
$scaleApi->replaceAppsV1Deployment(
    string $name,
    string $namespace,
    P8p\Sdk\Schema\Autoscaling\V1\Scale $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Autoscaling\V1\Scale` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |



---

### patchAppsV1Deployment

partially update scale of the specified Deployment


**Method Signature:**
```php
$scaleApi->patchAppsV1Deployment(
    string $name,
    string $namespace,
    array $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `array` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. This field is required for apply requests (application/apply-patch) but optional for non-apply patch types (JsonPatch, MergePatch, StrategicMergePatch). |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |
| `force` | `bool\|null` | Force is going to "force" Apply requests. It means user will re-acquire conflicting fields owned by other people. Force flag must be unset for non-apply patch requests. |



---

### readAppsV1ReplicaSet

read scale of the specified ReplicaSet


**Method Signature:**
```php
$scaleApi->readAppsV1ReplicaSet(
    string $name,
    string $namespace,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |



---

### replaceAppsV1ReplicaSet

replace scale of the specified ReplicaSet


**Method Signature:**
```php
$scaleApi->replaceAppsV1ReplicaSet(
    string $name,
    string $namespace,
    P8p\Sdk\Schema\Autoscaling\V1\Scale $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Autoscaling\V1\Scale` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |



---

### patchAppsV1ReplicaSet

partially update scale of the specified ReplicaSet


**Method Signature:**
```php
$scaleApi->patchAppsV1ReplicaSet(
    string $name,
    string $namespace,
    array $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `array` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. This field is required for apply requests (application/apply-patch) but optional for non-apply patch types (JsonPatch, MergePatch, StrategicMergePatch). |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |
| `force` | `bool\|null` | Force is going to "force" Apply requests. It means user will re-acquire conflicting fields owned by other people. Force flag must be unset for non-apply patch requests. |



---

### readAppsV1StatefulSet

read scale of the specified StatefulSet


**Method Signature:**
```php
$scaleApi->readAppsV1StatefulSet(
    string $name,
    string $namespace,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |



---

### replaceAppsV1StatefulSet

replace scale of the specified StatefulSet


**Method Signature:**
```php
$scaleApi->replaceAppsV1StatefulSet(
    string $name,
    string $namespace,
    P8p\Sdk\Schema\Autoscaling\V1\Scale $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Autoscaling\V1\Scale` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |



---

### patchAppsV1StatefulSet

partially update scale of the specified StatefulSet


**Method Signature:**
```php
$scaleApi->patchAppsV1StatefulSet(
    string $name,
    string $namespace,
    array $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Autoscaling\V1\Scale>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the Scale |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `array` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. This field is required for apply requests (application/apply-patch) but optional for non-apply patch types (JsonPatch, MergePatch, StrategicMergePatch). |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |
| `force` | `bool\|null` | Force is going to "force" Apply requests. It means user will re-acquire conflicting fields owned by other people. Force flag must be unset for non-apply patch requests. |



---



**Generated by P8P Code Generator**
