| Field         | READ   | WRITE |
| :----         | :---   | :---- |
| id*           | All    | None  |
| title*        | All    | Owner |
| description   | All    | Owner |
| publishAt     | All    | Owner |
| beginAt       | All    | Owner |
| endAt         | All    | Owner |
| pins          | Farmer | None  |
| pinned        | User   | None  |
| products**    | All    | Owner |
| farm/farmId** | All    | None  |

<small>
_Fields marked with * will be seen even at lowest level of details_</br>
_Fields marked with ** are embedded fields_
</small>

##### GET /api/events/:id

##### GET /api/events/
You can use filters in argument of the query.
The filter `farmId`can fileter event by farms.
The filter `subscribed` for users can filter by suscribed products and farms.

##### POST /api/events/:id (for farmer only)
##### PUT /api/events/:id (for farmer owner only)
##### DELETE /api/events/:id (for farmer owner only)
##### POST /api/events/pin/:id (for user only)
##### POST /api/events/unpin/:id (for user only)
