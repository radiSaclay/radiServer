##### (Bool) <- \auth\isUser (Request)
##### (Bool) <- \auth\isFarmer (Request)
##### (Bool) <- \auth\isAdmin (Request)
##### (Bool) <- \auth\isUserFarmer (User)
##### (Bool) <- \auth\isUserAdmin (User)
##### (User or null) <- \auth\getUser (Request)
##### (Farm or null) <- \auth\getUserFarm (User)
##### (Farm or null) <- \auth\getFarm (Request)

##### (String JWT) <- \auth\createUserToken (User)
Create a jwt token valid for one day containing
- user_id
- user_type = admin|farmer|user
- farm_id if needed
