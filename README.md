# Roddit

We are glade to announce you our first social network!



### Installation

Firstly we start kubernetes

```
minikube start
```

We create the mongosb shard

```
kubectl apply -f mongodb-service.yaml
kubectl apply -f mongo-configsvr.yaml
kubectl apply -f mongodb-shard.yaml
kubectl apply -f mongos-deployment.yaml
```

For starting the deployment on port 5000 type

```
kubectl port-forward deployment/flask-app 5000:5000
```
