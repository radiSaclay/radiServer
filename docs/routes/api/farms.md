| Field           | READ | WRITE |
| :----           | :--- | :---- |
| id*             | All  | None  |
| name*           | All  | Owner |
| address         | All  | Owner |
| website         | All  | Owner |
| phone           | All  | Owner |
| email           | All  | Owner |
| subscribed      | User | None  |
| products**      | All  | Owner |
| owner/ownerId** | All  | None  |

<small>
_Fields marked with * will be seen even at lowest level of details_</br>
_Fields marked with ** are embedded fields_
</small>

##### GET /api/farms/{id}
##### GET /api/farms/
##### POST /api/farms/{id} (for user only)
##### PUT /api/farms/{id} (for farmer owner only)
##### DELETE /api/farms/{id} (for farmer owner only)
##### POST /api/farms/subscribe/{id} (for user only)
##### POST /api/farms/unsubscribe/{id} (for user only)
