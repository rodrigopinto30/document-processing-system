# Document Processing Sytstem

This project imeplements a document processing built with **Laravel 11**, using **queues**, **jobs**, **Telescope monitoring** and an architecture to process multiple files.

## Approach

### **Processing**
The system uses Laravel Queues and Jobs to ensure files never block user requests.

- `ProcessFileJob` handles file extraction.
- `FinalizeProcessJob` finalizes the process.


### **Tracking**
The processes table;
- status
- Progess percentage
- Estimated completion time
- Start/finish timespamps
- Logs

### **Clean architecture**
- **Controller**: handle HTTP requests.
- **Jobs**: handle processing.
- **Models**: Encapsulate relationship.

### **Minotoring with Laravel Telescope**

Telescope allows us:
- Job excecution visibility
- DB query visibility
- Exception 
- API request 


## Installation
``` bash

git clone git@github.com:rodrigopinto30/document-processing-system.git

or

git clone https://github.com/rodrigopinto30/document-processing-system.git


cd document-processing-system
```

Start docker containers:
```bash
./start
```

* App will able at: ``http://localhost:8080/``

* Telescope will able at: ``http://localhost:8080/telescope ``

* phpMyAdmin will able at: `` http://localhost:8081/``

Copy the `.env.example` file and rename it as `.env`:

```bash 
cp .env.example .env
```

Then generate the application key:

```bash
docker exec -it dps_app php artisan key:generate
```

Run migrtations:
```bash
docker exec -it dps_app php artisan migrate
```