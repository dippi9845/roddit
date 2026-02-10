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
kubectl exec -it shard1-0 -- mongosh -port 27018
```

And then in the mongo shell

```
rs.initiate({
  _id: "shard1",
  members: [
    { _id: 0, host: "shard1-0.shard1.default.svc.cluster.local:27018" }
  ]
})
```

Now add the other shard pod

```
rs.add("shard1-1.shard1.default.svc.cluster.local:27018")
rs.add("shard1-2.shard1.default.svc.cluster.local:27018")
```

for checking if is working type
```
rs.status()
```
and then you have to see SECONDARY the the added pods and PRIMARY to the current pod

Let's start the config server
```
kubectl apply -f mongo-configsvr.yaml
```

Now let's connect to the config server

```
kubectl exec -it configsvr-0 -- mongosh --port 27019
```

and then initialize

```
rs.initiate({
  _id: "configRepl",
  configsvr: true,
  members: [
    { _id: 0, host: "configsvr-0.config-svc:27019" }
  ]
})
```

Let's start the mongos deployment
```
kubectl apply -f mongos-deployment.yaml
```

now exit from the bash and let's get inside the mongos deployment

```
kubectl exec -it deployment/mongos -- mongosh
```

and then add the shards

```
sh.addShard("shard1/shard1-0.shard1.default.svc.cluster.local:27018")
sh.addShard("shard1/shard1-1.shard1.default.svc.cluster.local:27018")
sh.addShard("shard1/shard1-2.shard1.default.svc.cluster.local:27018")
```