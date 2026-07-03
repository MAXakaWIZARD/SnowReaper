# SnowReaper
Snowrunner save manipulation and mods deployment tool

## Env
* PHP 8.5
* Symfony 8

# List mods
```bash
php sr mods:list
```

## Deploy mods
### All mods
```bash
php sr mods:deploy
```
### Only those matching provided filter
```bash
php sr mods:deploy kenworth
```
### Verbose mode
```bash
php sr mods:deploy kenworth -v
```
### Dry run
```bash
php sr mods:deploy kenworth --dry-run
```

## Save info
### On slot 1
```bash
php sr save:info
```
### On slot 2
```bash
php sr save:info -s 2
```

## Add money
### Default (add 100k to slot 1)
```bash
php sr save:add-money
```
### Add 20k to slot 2
```bash
php sr save:add-money -s 2 -a 20000
```

## Hard mode
### Enable
```bash
php sr save:hard:on
```
### Disable
```bash
php sr save:hard:off
```
