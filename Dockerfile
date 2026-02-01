FROM python:3.11-slim

# directory di lavoro nel container
WORKDIR /app

# copia requirements e installa dipendenze
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# copia TUTTO il progetto (compresi templates e static)
COPY . .

# variabili Flask
ENV FLASK_APP=src/project/app.py
ENV FLASK_RUN_HOST=0.0.0.0
ENV FLASK_RUN_PORT=5000

EXPOSE 5000

CMD ["flask", "run"]