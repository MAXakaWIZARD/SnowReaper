# SnowReaper
Snowrunner save manipulation and mods deployment tool

## Env
* PHP 8.5
* Symfony 8
* Windows 7+
* WinRAR (for mods deployment)

## Setting up
1. create `.env` file (see `.env.example`)
2. save manipulation setup
   * set `SAVES_DIR` env var
3. mods deployment setup
   * set `MODS_SOURCE_DIR` var to point to directory with custom modified mods, each directory name in format `{mod_id}_{whatever_mod_name}`, only mod id is important (like `12345_kenworth`)
   * set `MODS_DEPLOY_DIR` var to `modio` deployment destination
   * install WinRAR and set `WINRAR_BIN` env var


## List mods
```bash
sr mods:list
```

## Deploy mods
### All mods
```bash
sr mods:deploy
```
### Only those matching provided filter
```bash
sr mods:deploy kenworth
```
### Verbose mode
```bash
sr mods:deploy kenworth -v
```
### Dry run
```bash
sr mods:deploy kenworth --dry-run
```

## Save info
### On slot 1
```bash
sr save:info
```
### On slot 2
```bash
sr save:info -s 2
```

## Add money
### Default (add 100k to slot 1)
```bash
sr save:add-money
```
### Add 20k to slot 2
```bash
sr save:add-money -s 2 -a 20000
```

## Hard mode
### Enable
```bash
sr save:hard:on
```
### Disable
```bash
sr save:hard:off
```
