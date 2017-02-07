##### POST /auth/login
Expects:
```json
{ "email": "...", "password": "..." }
```

Will return:
```json
{ "validated": true, "token": "..." }

or

{ "validated": false, "msg": "..." }
```

##### POST /auth/signup
Expects:
```json
{ "email": "...", "password": "..." }
```

Will return:
```json
{ "validated": true, "token": "..." }

or

{ "validated": false, "msg": "..." }
```

##### GET /auth/user (for users only)
Will return:
```json
{
  "id": 0,
  "email": "...",
  "farm": { ... }
}
```

##### DELETE /auth/delete
Deletes the user who has made the request (need authorization token)

Will return:
Just response code (404 if not found, 200 if deleted)
