##### (Response) <- \api\mapCollection (Response, Collection, Function)
Apply the function on all elements of the collection and return a json response of the list.

##### (Response) <- \api\view (Response, Propel item)
If item is null, returns `404`, if not serializes it and returns it as a json.
The propel item must have a `serialize` function return an array.

##### (Response) <- \api\update (Request, Response, Propel item)
Update the item through the `unserialize` function using the request's body and returns the item as a json through the `serialize` function.
