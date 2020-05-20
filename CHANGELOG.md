# Changelog

## v0.5.x

The major change in this version, is that `sagas` are now handled with 
the `DomainMessage` instead of the `event`. This makes it more consistent with the 
rest of the broadway framework, where all event listeners are handled with the 
`DomainMessage`.


#### BC breaks

- The `Saga::handle*()` arguments are reordered with `State`, `event`, `DomainMessage`   

#### Other changes

-  `StaticallyConfiguredSagaInterface::configuration` array of callables gets a second argument which holds the `DomainMessage`. This way it is not necessary to put the identifier in each `event` because you can fetch it from the `DomainMessage`
 