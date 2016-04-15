## quick PHP hacks

### Wallet
instead of applying every change on an account immediately this module provides classes that collect (log, cache) the changes only.
usually the application calls *add* and *sub* on one of the *wallet* objects.
at a appropriate point in the logic of the application it either discards all collected changes and the balance remains unchanged or it applies the changes to the balance and syncs the balance to an *engine*.

in computer games parts of the user's game state is captured in counters.
some counters can only increase, like *experience*, abbreviated *xp*.

the *wallet* is a counter which can increase and decrease. it is an account.
often a single request to the game server applies multiple changes to a *wallet*.
in a naive implementation every change is made persistent immediately.

an advanced problem with counters in general are concurrent requests.
concurrent requests are possible
* bug in the front-end code
* a hacker attack - the hacker sends a lot of the same request concurrently.
assuming the backend infrastructure has many application servers and the load is balanced evenly (request queues have almost same length; best chance during off-peak hours) it is very likely that more than one request succeeds.
for example as a result the hacker paid once but received more than one item.
* the front-end sends requests to the game server asynchronously

in case a counter represents real dollars (*premium currency*) any issues with those counters are serious, because either the company or the player lost real money.


#### Getting Started
```php

```

#### Tests
run the tests wihtin the *wallet* folder
```
phpunit .
```

### Benchmark


### Message Queue Worker


### Gotchas
