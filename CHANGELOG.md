# Changelog

## 0.9.x

The major change in this version, is that `sagas` are now handled with 
the `DomainMessage` instead of the `event`. This makes it more consistent with the 
rest of the broadway framework, where all event listeners are handled with the 
`DomainMessage`.
