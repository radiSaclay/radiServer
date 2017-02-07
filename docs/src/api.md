All API helpers takes at least a Request and a Response as arguments, it will always return a Response.

##### (Response) <- \api\listCollection (Request, Response, Propel Query [, Funcion callback])
Returns a paginated and serialized json response of given query, add field returned by the callback if one given.
The propel items must have a `serialize` function return an array.

##### (Response) <- \api\view (Request, Response, Propel Item [, Funcion callback])
If item is null, returns `404`. If not returns a serialized json response of given item, add field returned by the callback if one given.
The propel item must have a `serialize` function return an array.

##### (Response) <- \api\update (Request, Response, Propel item)
Update the item through the `unserialize` function using the request's body and returns the item as a json through the `serialize` function.
The propel item must have a `serialize` function and the `validate` behavior return an array.


##### (Response) <- \api\delete (Request, Response, Propel item)
Delete the item.
