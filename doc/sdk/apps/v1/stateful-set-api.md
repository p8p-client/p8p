# StatefulSetApi

[← Back to Index](index.md)

- **API Group:** apps
- **API Version:** v1
- **Full Class Name:** `P8p\Sdk\Api\Apps\V1\StatefulSetApi`

---

## Table of Contents

- [list](#listAppsV1NamespacedStatefulSet)
- [create](#createAppsV1NamespacedStatefulSet)
- [deleteCollection](#deleteAppsV1CollectionNamespacedStatefulSet)
- [read](#readAppsV1NamespacedStatefulSet)
- [replace](#replaceAppsV1NamespacedStatefulSet)
- [delete](#deleteAppsV1NamespacedStatefulSet)
- [patch](#patchAppsV1NamespacedStatefulSet)
- [readStatus](#readAppsV1NamespacedStatefulSetStatus)
- [replaceStatus](#replaceAppsV1NamespacedStatefulSetStatus)
- [patchStatus](#patchAppsV1NamespacedStatefulSetStatus)
- [listForAllNamespaces](#listAppsV1StatefulSetForAllNamespaces)

---

## Usage

```php
use P8p\Sdk\Api\Apps\V1\StatefulSetApi;

$statefulSetApi = $client->getApi(StatefulSetApi::class);
```

## Operations

### list

list or watch objects of kind StatefulSet


**Method Signature:**
```php
$statefulSetApi->list(
    string $namespace,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSetList>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `allowWatchBookmarks` | `bool\|null` | allowWatchBookmarks requests watch events with type "BOOKMARK". Servers that do not implement bookmarks may ignore this flag and bookmarks are sent at the server's discretion. Clients should not assume bookmarks are returned at any specific interval, nor may they assume the server will send any BOOKMARK event during a session. If this is not a watch, this field is ignored. |
| `continue` | `null\|string` | The continue option should be set when retrieving more results from the server. Since this value is server defined, clients may only use the continue value from a previous query result with identical query parameters (except for the value of continue) and the server may reject a continue value it does not recognize. If the specified continue value is no longer valid whether due to expiration (generally five to fifteen minutes) or a configuration change on the server, the server will respond with a 410 ResourceExpired error together with a continue token. If the client needs a consistent list, it must restart their list without the continue field. Otherwise, the client may send another list request with the token received with the 410 error, the server will respond with a list starting from the next key, but from the latest snapshot, which is inconsistent from the previous list results - objects that are created, modified, or deleted after the first list request will be included in the response, as long as their keys are after the "next key".<br><br>This field is not supported when watch is true. Clients may start a watch from the last resourceVersion value returned by the server and not miss any modifications. |
| `fieldSelector` | `null\|string` | A selector to restrict the list of returned objects by their fields. Defaults to everything. |
| `labelSelector` | `null\|string` | A selector to restrict the list of returned objects by their labels. Defaults to everything. |
| `limit` | `int\|null` | limit is a maximum number of responses to return for a list call. If more items exist, the server will set the `continue` field on the list metadata to a value that can be used with the same initial query to retrieve the next set of results. Setting a limit may return fewer than the requested amount of items (up to zero items) in the event all requested objects are filtered out and clients should only use the presence of the continue field to determine whether more results are available. Servers may choose not to support the limit argument and will return all of the available results. If limit is specified and the continue field is empty, clients may assume that no more results are available. This field is not supported if watch is true.<br><br>The server guarantees that the objects returned when using continue will be identical to issuing a single list call without a limit - that is, no objects created, modified, or deleted after the first request is issued will be included in any subsequent continued requests. This is sometimes referred to as a consistent snapshot, and ensures that a client that is using limit to receive smaller chunks of a very large result can ensure they see all possible objects. If objects are updated during a chunked list the version of the object that was present at the time the first list result was calculated is returned. |
| `resourceVersion` | `null\|string` | resourceVersion sets a constraint on what resource versions a request may be served from. See https://kubernetes.io/docs/reference/using-api/api-concepts/#resource-versions for details.<br><br>Defaults to unset |
| `resourceVersionMatch` | `null\|string` | resourceVersionMatch determines how resourceVersion is applied to list calls. It is highly recommended that resourceVersionMatch be set for list calls where resourceVersion is set See https://kubernetes.io/docs/reference/using-api/api-concepts/#resource-versions for details.<br><br>Defaults to unset |
| `sendInitialEvents` | `bool\|null` | `sendInitialEvents=true` may be set together with `watch=true`. In that case, the watch stream will begin with synthetic events to produce the current state of objects in the collection. Once all such events have been sent, a synthetic "Bookmark" event  will be sent. The bookmark will report the ResourceVersion (RV) corresponding to the set of objects, and be marked with `"k8s.io/initial-events-end": "true"` annotation. Afterwards, the watch stream will proceed as usual, sending watch events corresponding to changes (subsequent to the RV) to objects watched.<br><br>When `sendInitialEvents` option is set, we require `resourceVersionMatch` option to also be set. The semantic of the watch request is as following: - `resourceVersionMatch` = NotOlderThan<br>  is interpreted as "data at least as new as the provided `resourceVersion`"<br>  and the bookmark event is send when the state is synced<br>  to a `resourceVersion` at least as fresh as the one provided by the ListOptions.<br>  If `resourceVersion` is unset, this is interpreted as "consistent read" and the<br>  bookmark event is send when the state is synced at least to the moment<br>  when request started being processed.<br>- `resourceVersionMatch` set to any other value or unset<br>  Invalid error is returned.<br><br>Defaults to true if `resourceVersion=""` or `resourceVersion="0"` (for backward compatibility reasons) and to false otherwise. |
| `timeoutSeconds` | `int\|null` | Timeout for the list/watch call. This limits the duration of the call, regardless of any activity or inactivity. |
| `watch` | `bool\|null` | Watch for changes to the described resources and return them as a stream of add, update, and remove notifications. Specify resourceVersion. |



---

### create

create a StatefulSet


**Method Signature:**
```php
$statefulSetApi->create(
    string $namespace,
    P8p\Sdk\Schema\Apps\V1\StatefulSet $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSet>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Apps\V1\StatefulSet` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |



---

### deleteCollection

delete collection of StatefulSet


**Method Signature:**
```php
$statefulSetApi->deleteCollection(
    string $namespace,
    P8p\Sdk\Schema\Meta\V1\DeleteOptions $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Meta\V1\Status>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Meta\V1\DeleteOptions` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `continue` | `null\|string` | The continue option should be set when retrieving more results from the server. Since this value is server defined, clients may only use the continue value from a previous query result with identical query parameters (except for the value of continue) and the server may reject a continue value it does not recognize. If the specified continue value is no longer valid whether due to expiration (generally five to fifteen minutes) or a configuration change on the server, the server will respond with a 410 ResourceExpired error together with a continue token. If the client needs a consistent list, it must restart their list without the continue field. Otherwise, the client may send another list request with the token received with the 410 error, the server will respond with a list starting from the next key, but from the latest snapshot, which is inconsistent from the previous list results - objects that are created, modified, or deleted after the first list request will be included in the response, as long as their keys are after the "next key".<br><br>This field is not supported when watch is true. Clients may start a watch from the last resourceVersion value returned by the server and not miss any modifications. |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldSelector` | `null\|string` | A selector to restrict the list of returned objects by their fields. Defaults to everything. |
| `gracePeriodSeconds` | `int\|null` | The duration in seconds before the object should be deleted. Value must be non-negative integer. The value zero indicates delete immediately. If this value is nil, the default grace period for the specified type will be used. Defaults to a per object value if not specified. zero means delete immediately. |
| `ignoreStoreReadErrorWithClusterBreakingPotential` | `bool\|null` | if set to true, it will trigger an unsafe deletion of the resource in case the normal deletion flow fails with a corrupt object error. A resource is considered corrupt if it can not be retrieved from the underlying storage successfully because of a) its data can not be transformed e.g. decryption failure, or b) it fails to decode into an object. NOTE: unsafe deletion ignores finalizer constraints, skips precondition checks, and removes the object from the storage. WARNING: This may potentially break the cluster if the workload associated with the resource being unsafe-deleted relies on normal deletion flow. Use only if you REALLY know what you are doing. The default value is false, and the user must opt in to enable it |
| `labelSelector` | `null\|string` | A selector to restrict the list of returned objects by their labels. Defaults to everything. |
| `limit` | `int\|null` | limit is a maximum number of responses to return for a list call. If more items exist, the server will set the `continue` field on the list metadata to a value that can be used with the same initial query to retrieve the next set of results. Setting a limit may return fewer than the requested amount of items (up to zero items) in the event all requested objects are filtered out and clients should only use the presence of the continue field to determine whether more results are available. Servers may choose not to support the limit argument and will return all of the available results. If limit is specified and the continue field is empty, clients may assume that no more results are available. This field is not supported if watch is true.<br><br>The server guarantees that the objects returned when using continue will be identical to issuing a single list call without a limit - that is, no objects created, modified, or deleted after the first request is issued will be included in any subsequent continued requests. This is sometimes referred to as a consistent snapshot, and ensures that a client that is using limit to receive smaller chunks of a very large result can ensure they see all possible objects. If objects are updated during a chunked list the version of the object that was present at the time the first list result was calculated is returned. |
| `orphanDependents` | `bool\|null` | Deprecated: please use the PropagationPolicy, this field will be deprecated in 1.7. Should the dependent objects be orphaned. If true/false, the "orphan" finalizer will be added to/removed from the object's finalizers list. Either this field or PropagationPolicy may be set, but not both. |
| `propagationPolicy` | `null\|string` | Whether and how garbage collection will be performed. Either this field or OrphanDependents may be set, but not both. The default policy is decided by the existing finalizer set in the metadata.finalizers and the resource-specific default policy. Acceptable values are: 'Orphan' - orphan the dependents; 'Background' - allow the garbage collector to delete the dependents in the background; 'Foreground' - a cascading policy that deletes all dependents in the foreground. |
| `resourceVersion` | `null\|string` | resourceVersion sets a constraint on what resource versions a request may be served from. See https://kubernetes.io/docs/reference/using-api/api-concepts/#resource-versions for details.<br><br>Defaults to unset |
| `resourceVersionMatch` | `null\|string` | resourceVersionMatch determines how resourceVersion is applied to list calls. It is highly recommended that resourceVersionMatch be set for list calls where resourceVersion is set See https://kubernetes.io/docs/reference/using-api/api-concepts/#resource-versions for details.<br><br>Defaults to unset |
| `sendInitialEvents` | `bool\|null` | `sendInitialEvents=true` may be set together with `watch=true`. In that case, the watch stream will begin with synthetic events to produce the current state of objects in the collection. Once all such events have been sent, a synthetic "Bookmark" event  will be sent. The bookmark will report the ResourceVersion (RV) corresponding to the set of objects, and be marked with `"k8s.io/initial-events-end": "true"` annotation. Afterwards, the watch stream will proceed as usual, sending watch events corresponding to changes (subsequent to the RV) to objects watched.<br><br>When `sendInitialEvents` option is set, we require `resourceVersionMatch` option to also be set. The semantic of the watch request is as following: - `resourceVersionMatch` = NotOlderThan<br>  is interpreted as "data at least as new as the provided `resourceVersion`"<br>  and the bookmark event is send when the state is synced<br>  to a `resourceVersion` at least as fresh as the one provided by the ListOptions.<br>  If `resourceVersion` is unset, this is interpreted as "consistent read" and the<br>  bookmark event is send when the state is synced at least to the moment<br>  when request started being processed.<br>- `resourceVersionMatch` set to any other value or unset<br>  Invalid error is returned.<br><br>Defaults to true if `resourceVersion=""` or `resourceVersion="0"` (for backward compatibility reasons) and to false otherwise. |
| `timeoutSeconds` | `int\|null` | Timeout for the list/watch call. This limits the duration of the call, regardless of any activity or inactivity. |



---

### read

read the specified StatefulSet


**Method Signature:**
```php
$statefulSetApi->read(
    string $name,
    string $namespace,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSet>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the StatefulSet |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |



---

### replace

replace the specified StatefulSet


**Method Signature:**
```php
$statefulSetApi->replace(
    string $name,
    string $namespace,
    P8p\Sdk\Schema\Apps\V1\StatefulSet $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSet>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the StatefulSet |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Apps\V1\StatefulSet` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |



---

### delete

delete a StatefulSet


**Method Signature:**
```php
$statefulSetApi->delete(
    string $name,
    string $namespace,
    P8p\Sdk\Schema\Meta\V1\DeleteOptions $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Meta\V1\Status>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the StatefulSet |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Meta\V1\DeleteOptions` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `gracePeriodSeconds` | `int\|null` | The duration in seconds before the object should be deleted. Value must be non-negative integer. The value zero indicates delete immediately. If this value is nil, the default grace period for the specified type will be used. Defaults to a per object value if not specified. zero means delete immediately. |
| `ignoreStoreReadErrorWithClusterBreakingPotential` | `bool\|null` | if set to true, it will trigger an unsafe deletion of the resource in case the normal deletion flow fails with a corrupt object error. A resource is considered corrupt if it can not be retrieved from the underlying storage successfully because of a) its data can not be transformed e.g. decryption failure, or b) it fails to decode into an object. NOTE: unsafe deletion ignores finalizer constraints, skips precondition checks, and removes the object from the storage. WARNING: This may potentially break the cluster if the workload associated with the resource being unsafe-deleted relies on normal deletion flow. Use only if you REALLY know what you are doing. The default value is false, and the user must opt in to enable it |
| `orphanDependents` | `bool\|null` | Deprecated: please use the PropagationPolicy, this field will be deprecated in 1.7. Should the dependent objects be orphaned. If true/false, the "orphan" finalizer will be added to/removed from the object's finalizers list. Either this field or PropagationPolicy may be set, but not both. |
| `propagationPolicy` | `null\|string` | Whether and how garbage collection will be performed. Either this field or OrphanDependents may be set, but not both. The default policy is decided by the existing finalizer set in the metadata.finalizers and the resource-specific default policy. Acceptable values are: 'Orphan' - orphan the dependents; 'Background' - allow the garbage collector to delete the dependents in the background; 'Foreground' - a cascading policy that deletes all dependents in the foreground. |



---

### patch

partially update the specified StatefulSet


**Method Signature:**
```php
$statefulSetApi->patch(
    string $name,
    string $namespace,
    array $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSet>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the StatefulSet |
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

### readStatus

read status of the specified StatefulSet


**Method Signature:**
```php
$statefulSetApi->readStatus(
    string $name,
    string $namespace,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSet>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the StatefulSet |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |



---

### replaceStatus

replace status of the specified StatefulSet


**Method Signature:**
```php
$statefulSetApi->replaceStatus(
    string $name,
    string $namespace,
    P8p\Sdk\Schema\Apps\V1\StatefulSet $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSet>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the StatefulSet |
| `namespace` | `string` | object name and auth scope, such as for teams and projects |
| `body` | `P8p\Sdk\Schema\Apps\V1\StatefulSet` |  |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `dryRun` | `null\|string` | When present, indicates that modifications should not be persisted. An invalid or unrecognized dryRun directive will result in an error response and no further processing of the request. Valid values are: - All: all dry run stages will be processed |
| `fieldManager` | `null\|string` | fieldManager is a name associated with the actor or entity that is making these changes. The value must be less than or 128 characters long, and only contain printable characters, as defined by https://golang.org/pkg/unicode/#IsPrint. |
| `fieldValidation` | `null\|string` | fieldValidation instructs the server on how to handle objects in the request (POST/PUT/PATCH) containing unknown or duplicate fields. Valid values are: - Ignore: This will ignore any unknown fields that are silently dropped from the object, and will ignore all but the last duplicate field that the decoder encounters. This is the default behavior prior to v1.23. - Warn: This will send a warning via the standard warning response header for each unknown field that is dropped from the object, and for each duplicate field that is encountered. The request will still succeed if there are no other errors, and will only persist the last of any duplicate fields. This is the default in v1.23+ - Strict: This will fail the request with a BadRequest error if any unknown fields would be dropped from the object, or if any duplicate fields are present. The error returned from the server will contain all unknown and duplicate fields encountered. |



---

### patchStatus

partially update status of the specified StatefulSet


**Method Signature:**
```php
$statefulSetApi->patchStatus(
    string $name,
    string $namespace,
    array $body
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSet>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the StatefulSet |
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

### listForAllNamespaces

list or watch objects of kind StatefulSet


**Method Signature:**
```php
$statefulSetApi->listForAllNamespaces(
    ?array $query = null,
): Response<P8p\Sdk\Schema\Apps\V1\StatefulSetList>
```


**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `allowWatchBookmarks` | `bool\|null` | allowWatchBookmarks requests watch events with type "BOOKMARK". Servers that do not implement bookmarks may ignore this flag and bookmarks are sent at the server's discretion. Clients should not assume bookmarks are returned at any specific interval, nor may they assume the server will send any BOOKMARK event during a session. If this is not a watch, this field is ignored. |
| `continue` | `null\|string` | The continue option should be set when retrieving more results from the server. Since this value is server defined, clients may only use the continue value from a previous query result with identical query parameters (except for the value of continue) and the server may reject a continue value it does not recognize. If the specified continue value is no longer valid whether due to expiration (generally five to fifteen minutes) or a configuration change on the server, the server will respond with a 410 ResourceExpired error together with a continue token. If the client needs a consistent list, it must restart their list without the continue field. Otherwise, the client may send another list request with the token received with the 410 error, the server will respond with a list starting from the next key, but from the latest snapshot, which is inconsistent from the previous list results - objects that are created, modified, or deleted after the first list request will be included in the response, as long as their keys are after the "next key".<br><br>This field is not supported when watch is true. Clients may start a watch from the last resourceVersion value returned by the server and not miss any modifications. |
| `fieldSelector` | `null\|string` | A selector to restrict the list of returned objects by their fields. Defaults to everything. |
| `labelSelector` | `null\|string` | A selector to restrict the list of returned objects by their labels. Defaults to everything. |
| `limit` | `int\|null` | limit is a maximum number of responses to return for a list call. If more items exist, the server will set the `continue` field on the list metadata to a value that can be used with the same initial query to retrieve the next set of results. Setting a limit may return fewer than the requested amount of items (up to zero items) in the event all requested objects are filtered out and clients should only use the presence of the continue field to determine whether more results are available. Servers may choose not to support the limit argument and will return all of the available results. If limit is specified and the continue field is empty, clients may assume that no more results are available. This field is not supported if watch is true.<br><br>The server guarantees that the objects returned when using continue will be identical to issuing a single list call without a limit - that is, no objects created, modified, or deleted after the first request is issued will be included in any subsequent continued requests. This is sometimes referred to as a consistent snapshot, and ensures that a client that is using limit to receive smaller chunks of a very large result can ensure they see all possible objects. If objects are updated during a chunked list the version of the object that was present at the time the first list result was calculated is returned. |
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |
| `resourceVersion` | `null\|string` | resourceVersion sets a constraint on what resource versions a request may be served from. See https://kubernetes.io/docs/reference/using-api/api-concepts/#resource-versions for details.<br><br>Defaults to unset |
| `resourceVersionMatch` | `null\|string` | resourceVersionMatch determines how resourceVersion is applied to list calls. It is highly recommended that resourceVersionMatch be set for list calls where resourceVersion is set See https://kubernetes.io/docs/reference/using-api/api-concepts/#resource-versions for details.<br><br>Defaults to unset |
| `sendInitialEvents` | `bool\|null` | `sendInitialEvents=true` may be set together with `watch=true`. In that case, the watch stream will begin with synthetic events to produce the current state of objects in the collection. Once all such events have been sent, a synthetic "Bookmark" event  will be sent. The bookmark will report the ResourceVersion (RV) corresponding to the set of objects, and be marked with `"k8s.io/initial-events-end": "true"` annotation. Afterwards, the watch stream will proceed as usual, sending watch events corresponding to changes (subsequent to the RV) to objects watched.<br><br>When `sendInitialEvents` option is set, we require `resourceVersionMatch` option to also be set. The semantic of the watch request is as following: - `resourceVersionMatch` = NotOlderThan<br>  is interpreted as "data at least as new as the provided `resourceVersion`"<br>  and the bookmark event is send when the state is synced<br>  to a `resourceVersion` at least as fresh as the one provided by the ListOptions.<br>  If `resourceVersion` is unset, this is interpreted as "consistent read" and the<br>  bookmark event is send when the state is synced at least to the moment<br>  when request started being processed.<br>- `resourceVersionMatch` set to any other value or unset<br>  Invalid error is returned.<br><br>Defaults to true if `resourceVersion=""` or `resourceVersion="0"` (for backward compatibility reasons) and to false otherwise. |
| `timeoutSeconds` | `int\|null` | Timeout for the list/watch call. This limits the duration of the call, regardless of any activity or inactivity. |
| `watch` | `bool\|null` | Watch for changes to the described resources and return them as a stream of add, update, and remove notifications. Specify resourceVersion. |



---



**Generated by P8P Code Generator**
