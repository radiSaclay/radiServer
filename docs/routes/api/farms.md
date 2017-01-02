| Field | READ | WRITE |
| :---- | :--- | :---- |
| id | Yes | No |
| ownerId | Yes | No |
| name | Yes | Yes |
| address | Yes | Yes |
| website | Yes | Yes |
| phone | Yes | Yes |
| email | Yes | Yes |
| subscribed | User | No |

##### GET /api/farms/{id}
##### GET /api/farms/
##### POST /api/farms/{id} (for user only)
##### PUT /api/farms/{id} (for farmer owner only)
##### DELETE /api/farms/{id} (for farmer owner only)
##### POST /api/farms/subscribe/{id} (for user only)
##### POST /api/farms/unsubscribe/{id} (for user only)