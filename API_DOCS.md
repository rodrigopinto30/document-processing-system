# Endpoints

# 1. Start Process

**POST /process/start**

Starts a new processing.
The system scans ``storage/app/documents/input/`` for ``.txt`` files and generates a ``process_id``.

**Request Body**
```json
{}
```

**Success Response**

staus 200

```json
{
    "message": "Process started successfully",
    "process_id": "01kbqn847sca94pbtepbavxpab"
}
```

**Error Responses**

No files found
```json
{
    "error": "Failed to start process",
    "details": "No files found in documents input folder."
}
```

Server error
```json
{
    "error": "Failed to start process",
    "details": "Internal error message..."
}

```

# 2. Get Process Status

**GET /process/{id}/status**

```bash
GET /process/status/01kbk950s3mq5ndp6hjwamdxrk
```

**Success Response**

status 200

```json
{
    "process_id": "01kbk950s3mq5ndp6hjwamdxrk",
    "status": "RUNNING",
    "progress": {
        "total_files": 10,
        "processed_files": 3,
        "percentage": 30
    },
    "started_at": "2025-12-03T23:35:53+00:00",
    "estimated_completion": "2025-12-03T23:36:40+00:00",
    "results": null
}

```

**Error Response**
```json
{
    "error": "Process not found"
}
```