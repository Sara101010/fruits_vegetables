### GET `/food`

```markdown
# GET /food
```
This endpoint retrieves a list of food items. It accepts query parameters.

## Request

### Query Parameters
| Parameter  | Type   | Required    | Description                         |
|------------|--------|-------------|-------------------------------------|
| `type`     | string | No          | Filter by type (`fruit` or `vegetable`). |
| `name`     | string | No          | Filter by food name (partial matches allowed). |
| `page`     | int    | No          | The page number for pagination (default: 1). |
| `limit`    | int    | No          | Number of items per page (default: 15). |
| `unit`     | string | No          | Desired unit for quantity value (`kg` or `g`).       |

### Example Request
```http
GET /food?unit=kg&type=vegetable
```
### 200 Ok
The request was successful, and the food list is provided under the keys "fruits" and "vegetables".
If an specific type is required, only that key will be provided.

```json
{
    "fruits": [
        {
            "id": "14",
            "name": "Bananas",
            "quantity": "100",
            "unit": "kg",
            "created_at": "2024-11-23 20:17:07",
            "updated_at": "2024-11-23 20:17:07"
        }
    ],
    "vegetables": [
        {
            "id": "103783737",
            "name": "Carrot",
            "quantity": "10.92",
            "unit": "kg",
            "created_at": "2024-11-23 20:42:30",
            "updated_at": "2024-11-23 20:42:30"
        }
    ]
}
```
### 400 Bad Request
If a value is not valid.