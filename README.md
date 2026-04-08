# Roddit

We are glade to announce you our first social network!



### Installation

Firstly we start kubernetes

```
minikube start --container-runtime=containerd
```

Install helm with admin privileges
```
choco install kubernetes-helm
```

Add required repos for prometheus/grafana and chaos mesh + update
```
helm repo add prometheus-community https://prometheus-community.github.io/helm-charts
helm repo add chaos-mesh https://charts.chaos-mesh.org
helm repo update
```

Install the prometheus/grafana stack
```
helm install my-release prometheus-community/kube-prometheus-stack
```

create a seperate namespace for chaos mesh to preserve cluster integrity
```
kubectl create ns chaos-mesh
```

Install chaos mesh
```
helm install chaos-mesh chaos-mesh/chaos-mesh -n chaos-mesh --create-namespace --set chaosDaemon.runtime=containerd --set chaosDaemon.socketPath=/run/containerd/containerd.sock --version 2.6.3
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
kubectl exec -it <pod_of_mongos_router> -- mongosh /tmp/init-db.js
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

Set up the port-forwarding for visualizing grafana dashboard from web browser
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

Creation of local admin user to interact with chaos mesh dashboard
```
kubectl create serviceaccount chaos-admin -n chaos-mesh
kubectl create clusterrolebinding chaos-admin-binding --clusterrole=cluster-admin --serviceaccount=chaos-mesh:chaos-admin
kubectl create token chaos-admin -n chaos-mesh
```

Set up the port-forwarding for visualizing chaos mesh dashboard from web browser
```
kubectl port-forward -n chaos-mesh svc/chaos-dashboard 2333:2333
```

Enable metrics-server to allow hpa
```
minikube addons enable metrics-server
```

Execution of the experiments(DO NOT EXECUTE ALL AT ONCE)
```
kubectl apply -f chaos-high-latency.yaml
kubectl apply -f chaos-heavy-load.yaml
kubectl apply -f chaos-pod-termination.yaml
```

To check if experiments are working
```
curl -o /dev/null -s -w "time_connect: %{time_connect}s\ntime_starttransfer: %{time_starttransfer}s\ntime_total: %{time_total}s\n" http://<IP-FLASK-APP>:<PORT>/ (for chaos-high-latency.yaml)
kubectl get hpa -w (for chaos-heavy-load.yaml)
kubectl get pods -w (for chaos-pod-termination.yaml)
```
