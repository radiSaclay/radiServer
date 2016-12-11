| Field | READ | WRITE |
| :---- | :--- | :---- |
| id | Yes | No |
| farmId | Yes | No |
| productId | Yes | Yes |
| description | Yes | Yes |
| publishAt | Yes | Yes |
| beginAt | Yes | Yes |
| endAt | Yes | Yes |
| pins | Farmer | No |
| pinned | User | No |

##### GET /api/events/{id}
##### GET /api/events/
##### POST /api/events/{id} (for farmer only)
##### PUT /api/events/{id} (for farmer owner only)
##### DELETE /api/events/{id} (for farmer owner only)
##### POST /api/events/pin/{id} (for user only)
##### POST /api/events/unpin/{id} (for user only)
