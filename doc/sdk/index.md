# P8P Kubernetes SDK Documentation

This section lists all the APIs that are available through the SDK, providing a complete reference for developers.

---

## Core
- [BindingApi (v1)](./core/v1/binding-api.md)
- [ComponentStatusApi (v1)](./core/v1/component-status-api.md)
- [ConfigMapApi (v1)](./core/v1/config-map-api.md)
- [EndpointsApi (v1)](./core/v1/endpoints-api.md)
- [EventApi (v1)](./core/v1/event-api.md)
- [LimitRangeApi (v1)](./core/v1/limit-range-api.md)
- [NamespaceK8sApi (v1)](./core/v1/namespace-k8s-api.md)
- [NodeApi (v1)](./core/v1/node-api.md)
- [NodeProxyOptionsApi (v1)](./core/v1/node-proxy-options-api.md)
- [PersistentVolumeApi (v1)](./core/v1/persistent-volume-api.md)
- [PersistentVolumeClaimApi (v1)](./core/v1/persistent-volume-claim-api.md)
- [PodApi (v1)](./core/v1/pod-api.md)
- [PodAttachOptionsApi (v1)](./core/v1/pod-attach-options-api.md)
- [PodExecOptionsApi (v1)](./core/v1/pod-exec-options-api.md)
- [PodPortForwardOptionsApi (v1)](./core/v1/pod-port-forward-options-api.md)
- [PodProxyOptionsApi (v1)](./core/v1/pod-proxy-options-api.md)
- [PodTemplateApi (v1)](./core/v1/pod-template-api.md)
- [ReplicationControllerApi (v1)](./core/v1/replication-controller-api.md)
- [ResourceQuotaApi (v1)](./core/v1/resource-quota-api.md)
- [SecretApi (v1)](./core/v1/secret-api.md)
- [ServiceAccountApi (v1)](./core/v1/service-account-api.md)
- [ServiceApi (v1)](./core/v1/service-api.md)
- [ServiceProxyOptionsApi (v1)](./core/v1/service-proxy-options-api.md)

## policy
- [EvictionApi (v1)](./policy/v1/eviction-api.md)
- [PodDisruptionBudgetApi (v1)](./policy/v1/pod-disruption-budget-api.md)

## autoscaling
- [HorizontalPodAutoscalerApi (v1)](./autoscaling/v1/horizontal-pod-autoscaler-api.md)
- [HorizontalPodAutoscalerApi (v2)](./autoscaling/v2/horizontal-pod-autoscaler-api.md)
- [ScaleApi (v1)](./autoscaling/v1/scale-api.md)
- [ScaleApi (v1)](./autoscaling/v1/scale-api.md)

## authentication.k8s.io
- [SelfSubjectReviewApi (v1)](./authentication.k8s.io/v1/self-subject-review-api.md)
- [TokenRequestApi (v1)](./authentication.k8s.io/v1/token-request-api.md)
- [TokenReviewApi (v1)](./authentication.k8s.io/v1/token-review-api.md)

## admissionregistration.k8s.io
- [MutatingWebhookConfigurationApi (v1)](./admissionregistration.k8s.io/v1/mutating-webhook-configuration-api.md)
- [ValidatingAdmissionPolicyApi (v1)](./admissionregistration.k8s.io/v1/validating-admission-policy-api.md)
- [ValidatingAdmissionPolicyBindingApi (v1)](./admissionregistration.k8s.io/v1/validating-admission-policy-binding-api.md)
- [ValidatingWebhookConfigurationApi (v1)](./admissionregistration.k8s.io/v1/validating-webhook-configuration-api.md)

## apiextensions.k8s.io
- [CustomResourceDefinitionApi (v1)](./apiextensions.k8s.io/v1/custom-resource-definition-api.md)

## apiregistration.k8s.io
- [APIServiceApi (v1)](./apiregistration.k8s.io/v1/api-service-api.md)

## apps
- [ControllerRevisionApi (v1)](./apps/v1/controller-revision-api.md)
- [DaemonSetApi (v1)](./apps/v1/daemon-set-api.md)
- [DeploymentApi (v1)](./apps/v1/deployment-api.md)
- [ReplicaSetApi (v1)](./apps/v1/replica-set-api.md)
- [StatefulSetApi (v1)](./apps/v1/stateful-set-api.md)

## authorization.k8s.io
- [LocalSubjectAccessReviewApi (v1)](./authorization.k8s.io/v1/local-subject-access-review-api.md)
- [SelfSubjectAccessReviewApi (v1)](./authorization.k8s.io/v1/self-subject-access-review-api.md)
- [SelfSubjectRulesReviewApi (v1)](./authorization.k8s.io/v1/self-subject-rules-review-api.md)
- [SubjectAccessReviewApi (v1)](./authorization.k8s.io/v1/subject-access-review-api.md)

## batch
- [CronJobApi (v1)](./batch/v1/cron-job-api.md)
- [JobApi (v1)](./batch/v1/job-api.md)

## certificates.k8s.io
- [CertificateSigningRequestApi (v1)](./certificates.k8s.io/v1/certificate-signing-request-api.md)

## coordination.k8s.io
- [LeaseApi (v1)](./coordination.k8s.io/v1/lease-api.md)

## discovery.k8s.io
- [EndpointSliceApi (v1)](./discovery.k8s.io/v1/endpoint-slice-api.md)

## events.k8s.io
- [EventApi (v1)](./events.k8s.io/v1/event-api.md)

## flowcontrol.apiserver.k8s.io
- [FlowSchemaApi (v1)](./flowcontrol.apiserver.k8s.io/v1/flow-schema-api.md)
- [PriorityLevelConfigurationApi (v1)](./flowcontrol.apiserver.k8s.io/v1/priority-level-configuration-api.md)

## networking.k8s.io
- [IngressApi (v1)](./networking.k8s.io/v1/ingress-api.md)
- [IngressClassApi (v1)](./networking.k8s.io/v1/ingress-class-api.md)
- [NetworkPolicyApi (v1)](./networking.k8s.io/v1/network-policy-api.md)

## node.k8s.io
- [RuntimeClassApi (v1)](./node.k8s.io/v1/runtime-class-api.md)

## rbac.authorization.k8s.io
- [ClusterRoleApi (v1)](./rbac.authorization.k8s.io/v1/cluster-role-api.md)
- [ClusterRoleBindingApi (v1)](./rbac.authorization.k8s.io/v1/cluster-role-binding-api.md)
- [RoleApi (v1)](./rbac.authorization.k8s.io/v1/role-api.md)
- [RoleBindingApi (v1)](./rbac.authorization.k8s.io/v1/role-binding-api.md)

## scheduling.k8s.io
- [PriorityClassApi (v1)](./scheduling.k8s.io/v1/priority-class-api.md)

## storage.k8s.io
- [CSIDriverApi (v1)](./storage.k8s.io/v1/csi-driver-api.md)
- [CSINodeApi (v1)](./storage.k8s.io/v1/csi-node-api.md)
- [CSIStorageCapacityApi (v1)](./storage.k8s.io/v1/csi-storage-capacity-api.md)
- [StorageClassApi (v1)](./storage.k8s.io/v1/storage-class-api.md)
- [VolumeAttachmentApi (v1)](./storage.k8s.io/v1/volume-attachment-api.md)

