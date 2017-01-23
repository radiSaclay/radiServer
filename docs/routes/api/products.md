| Field       | READ | WRITE |
| :----       | :--- | :---- |
| id*         | All  | None  |
| name*       | All  | Admin |
| parentId    | None | Admin |
| farms**     | All  | None  |
| children**  | All  | None  |
| ancestors** | All  | None  |

<small>
_Fields marked with * will be seen even at lowest level of details_</br>
_Fields marked with ** are embedded fields_
</small>

##### GET /api/products/:id
##### GET /api/products/
##### POST /api/products/
##### PUT /api/products/:id
##### DELETE /api/products/:id
##### POST /api/products/subscribe/:id (for user only)
##### POST /api/products/unsubscribe/:id (for user only)
##### GET /api/products/subscribed (for user only)
