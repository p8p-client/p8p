# ComponentStatusApi

[← Back to Index](index.md)

- **API Group:** Core
- **API Version:** v1
- **Full Class Name:** `P8p\Sdk\Api\Core\V1\ComponentStatusApi`

---

## Table of Contents

- [list](#list)
- [read](#read)

---

## Usage

```php
use P8p\Sdk\Api\Core\V1\ComponentStatusApi;

$componentStatusApi = $client->getApi(ComponentStatusApi::class);
```

## Operations

### list

list objects of kind ComponentStatus


**Method Signature:**
```php
$componentStatusApi->list(
    ?array $query = null,
): Response<P8p\Sdk\Schema\Core\V1\ComponentStatusList>
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

### read

read the specified ComponentStatus


**Method Signature:**
```php
$componentStatusApi->read(
    string $name,
    ?array $query = null,
): Response<P8p\Sdk\Schema\Core\V1\ComponentStatus>
```

**Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | name of the ComponentStatus |

**Query Parameters:**

| Name | Type | Description |
|------|------|-------------|
| `pretty` | `null\|string` | If 'true', then the output is pretty printed. Defaults to 'false' unless the user-agent indicates a browser or command-line HTTP tool (curl and wget). |



---



**Generated by P8P Code Generator**
