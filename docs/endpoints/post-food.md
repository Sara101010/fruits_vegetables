### POST `/food`

```markdown
# POST http://127.0.0.1:8000/food
```
This endpoint allows adding one or more food items to the system.

## Request

### Headers
- `Content-Type: application/json`

### Body
An array of objects, where each object must include the following fields:

| Field      | Type   | Required    | Description                           |
|------------|--------|-------------|---------------------------------------|
| `id`       | int    | Yes         | Unique identifier for the food item. |
| `name`     | string | Yes         | Name of the food item.               |
| `type`     | string | Yes         | Type of food (`fruit` or `vegetable`). |
| `unit`     | string | Yes         | Unit of measurement (`kg` or `g`).   |
| `quantity` | float  | Yes         | Quantity of the food item.           |

### Example Body
```json
[
  {
    "id": 1,
    "name": "Carrot",
    "type": "vegetable",
    "unit": "kg",
    "quantity": 1.5
  }
]
```
# Responses

### 201 Created
The request was successful, and the food items were added. If any id was already in the
BD, a list will be returned in the response specifying the entries that were skipped.

```json
{
  "existent_ids_skipped": "1,4"
}
```
### 400 Bad Request
If a field is missing or a value is not valid.