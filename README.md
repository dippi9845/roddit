# Roddit

We are glade to announce you our first *badass* social network!

### Installation

Firstly we create the mongosb shard

```
kubectl apply -f mongodb-shard.yaml
```

Secondly we create the service

```
kubectl apply -f mongodb-service.yaml
```

And then we need to connect the mongodb pods

```
kubectl exec -it shard1-0 -- mongosh
```

And then in the mongo shell

```
rs.add("shard1-1.shard1.default.svc.cluster.local:27018")
rs.add("shard1-2.shard1.default.svc.cluster.local:27018")
```

for checking if is working type
```
rs.status()
```
and then you have to se SECONDARY the the added pods and PRIMARY to the current pod
