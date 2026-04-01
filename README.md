# Roddit

We are glade to announce you our first social network!



### Installation

Firstly we start kubernetes

```
minikube start
```

Install and set up the helm repo
```
choco install kubernetes-helm
helm repo add prometheus-community https://prometheus-community.github.io/helm-charts
helm repo update
```

Install the prometheus/grafana stack
```
helm install my-release prometheus-community/kube-prometheus-stack
```

We create the mongosb shard

```
kubectl apply -f mongodb-service.yaml
kubectl apply -f mongo-configsvr.yaml
kubectl apply -f mongodb-shard.yaml
kubectl apply -f mongos-deployment.yaml
```

Set up the database
```
kubectl cp src/database/init-db.js <pod_of_mongos_router>:/tmp/init-db.js
kubectl exec -it <pod_of_mongos_router> mongosh /tmp/init-db.js
```

Set up flask
```
kubectl apply -f flask-service.yaml
kubectl apply -f flask-deployment.yaml
kubectl apply -f flask-hpa.yaml
```

Set up the grafana service and monitor
```
kubectl apply -f webapp-service.yaml
kubectl apply -f flask-monitor.yaml
```

For starting the deployment on port 5000 type

```
kubectl port-forward deployment/flask-app 5000:5000
```

Set up the port-forwarding for visualizing the dashboard from web browser
```
kubectl port-forward deployment/my-release-grafana 3000:3000
```

Get the Grafana password required to login in the browser.
NOTE: The command may vary depending if you are on Linux or Windows:
Linux Version:
```
kubectl get secret --namespace default my-release-grafana -o jsonpath="{.data.admin-password}" | base64 --decode ; echo
```
Windows Version:
```
$pass = kubectl get secret my-release-grafana -o jsonpath="{.data.admin-password}" 
[System.Text.Encoding]::UTF8.GetString([System.Convert]::FromBase64String($pass))
```
